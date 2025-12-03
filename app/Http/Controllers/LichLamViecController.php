<?php

namespace App\Http\Controllers;
use App\Models\LichLamViec;
use App\Models\NhanVien;
use App\Models\CaLamViec;
use App\Models\ChiTietViPham;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LichLamViecController extends Controller
{
    /**
     * Trang chính hiển thị giao diện lập lịch
     */
    // App/Http/Controllers/LichLamViecController.php

    public function index()
    {
        // Lấy nhân viên kèm chức vụ để hiển thị đẹp hơn
        $nhanviens = NhanVien::with('chucVu')->where('TRANGTHAI', 1)->get();
        $calamviecs = CaLamViec::all();
        return view('pages.manager-schedule', compact('nhanviens', 'calamviecs'));
    }

    // API Lấy dữ liệu
    public function getSchedule(Request $request)
    {
        $start = Carbon::parse($request->start);
        $end = Carbon::parse($request->end);
        $manv = $request->manv;

        $query = LichLamViec::with(['nhanVien.chucVu', 'caLamViec', 'viPhams'])
            ->whereBetween('NGAYLAM', [$start->format('Y-m-d'), $end->format('Y-m-d')]);

        if ($manv && $manv !== 'all') {
            $query->where('MANV', $manv);
        }

        $schedules = $query->get();

        // Format dữ liệu và TỰ ĐỘNG PHÂN LOẠI BUỔI
        $events = $schedules->map(function ($item) {
            $gioBatDau = (int) substr($item->caLamViec->GIOBATDAU, 0, 2);

            // Logic phân buổi tự động dựa trên giờ bắt đầu
            $session = 'morning'; // Mặc định
            if ($gioBatDau >= 12 && $gioBatDau < 17)
                $session = 'afternoon';
            if ($gioBatDau >= 17)
                $session = 'evening';

            return [
                'id' => $item->MALICH,
                'manv' => $item->MANV,
                'ten_nv' => $item->nhanVien->HO . ' ' . $item->nhanVien->TEN,
                'chuc_vu' => $item->nhanVien->chucVu->TENCV ?? 'NV',
                'maca' => $item->MACA,
                'tenca' => $item->caLamViec->TENCA,
                'gio_bat_dau' => substr($item->caLamViec->GIOBATDAU, 0, 5),
                'gio_ket_thuc' => substr($item->caLamViec->GIOKETTHUC, 0, 5),
                'date' => $item->NGAYLAM,
                'session' => $session, // Trả về buổi để frontend xếp chỗ
                'ghichu' => $item->GHICHU,
                'ds_loi' => $item->viPhams,
                'tong_tien_phat' => $item->TIEN_TRU
            ];
        });

        return response()->json($events);
    }

    // API Lưu / Cập nhật / Đổi lịch
    /**
     * API: Lưu lịch làm việc (Thêm mới / Cập nhật / Ghi nhận lỗi)
     */
    public function store(Request $request)
    {
        // Sử dụng Transaction để đảm bảo toàn vẹn dữ liệu (Lưu cả lịch và lỗi cùng lúc)
        \Illuminate\Support\Facades\DB::beginTransaction();

        try {
            // 1. Validate cơ bản
            if (!$request->MANV || !$request->MACA || !$request->NGAYLAM) {
                return response()->json(['status' => 'error', 'message' => 'Vui lòng chọn đầy đủ: Nhân viên, Ca làm và Ngày.']);
            }

            // 2. CHECK TRÙNG LỊCH (Conflict Check)
            $query = LichLamViec::where('MANV', $request->MANV)
                ->where('NGAYLAM', $request->NGAYLAM);

            if ($request->id) {
                // Nếu là cập nhật thì loại trừ chính nó ra khỏi check trùng
                $query->where('MALICH', '!=', $request->id);
                $lich = LichLamViec::find($request->id);
                if (!$lich) {
                    return response()->json(['status' => 'error', 'message' => 'Không tìm thấy lịch cần sửa']);
                }
            } else {
                $lich = new LichLamViec();
            }

            // Check: 1 ngày không được làm trùng Ca
            $isConflict = (clone $query)->where('MACA', $request->MACA)->exists();

            if ($isConflict) {
                return response()->json(['status' => 'error', 'message' => 'Nhân viên này đã có lịch ca này trong ngày rồi!']);
            }

            // 3. Xử lý Tổng tiền phạt từ danh sách lỗi (Frontend gửi lên)
            $tongTienPhat = 0;
            $danhSachLoi = $request->DANH_SACH_LOI ?? []; // Mảng JSON: [{noidung: '...', sotien: 10000}, ...]

            if (is_array($danhSachLoi)) {
                foreach ($danhSachLoi as $loi) {
                    $tongTienPhat += isset($loi['sotien']) ? (int) $loi['sotien'] : 0;
                }
            }

            // 4. Lưu thông tin bảng chính (LICHLAMVIEC)
            $lich->fill([
                'MANV' => $request->MANV,
                'MACA' => $request->MACA,
                'NGAYLAM' => $request->NGAYLAM,
                'GHICHU' => $request->GHICHU,
                'TRANGTHAI' => 'DA_PHAN_CONG',
                'TIEN_TRU' => $tongTienPhat // Lưu tổng phạt để hiển thị nhanh ngoài bảng
            ]);
            $lich->save();

            // 5. Lưu chi tiết vi phạm (Bảng phụ CHITIET_VIPHAM)
            // Xóa hết lỗi cũ của lịch này (nếu có) để lưu lại danh sách mới nhất
            $lich->viPhams()->delete();

            if (is_array($danhSachLoi) && count($danhSachLoi) > 0) {
                foreach ($danhSachLoi as $loi) {
                    // Chỉ lưu nếu có nội dung lỗi
                    if (!empty($loi['noidung'])) {
                        \App\Models\ChiTietViPham::create([
                            'MALICH' => $lich->MALICH,
                            'NOIDUNG' => $loi['noidung'],
                            'SOTIEN' => $loi['sotien'] ?? 0
                        ]);
                    }
                }
            }

            // Commit Transaction (Xác nhận lưu thành công)
            \Illuminate\Support\Facades\DB::commit();

            return response()->json(['status' => 'success', 'message' => 'Lưu lịch và ghi nhận lỗi thành công!']);

        } catch (\Exception $e) {
            // Nếu có lỗi, hoàn tác mọi thay đổi DB
            \Illuminate\Support\Facades\DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        LichLamViec::destroy($id);
        return response()->json(['status' => 'success']);
    }


    /**
     * API: Lập lịch nhanh theo tháng (Copy mẫu)
     * Đây là tính năng nâng cao, bạn có thể triển khai logic:
     * Lặp qua tất cả ngày trong tháng -> Tạo bản ghi.
     */
    public function autoScheduleMonth(Request $request)
    {
        // Logic lập lịch tự động sẽ viết ở đây tùy theo quy tắc công ty bạn
        return response()->json(['message' => 'Chức năng đang phát triển']);
    }
}