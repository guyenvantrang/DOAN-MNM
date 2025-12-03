<?php

namespace App\Http\Controllers;

use App\Models\KhuyenMai;
use App\Models\SanPham;
use Illuminate\Http\Request;

class KhuyenMaiController extends Controller
{
    /* |--------------------------------------------------------------------------
    | PHẦN 1: QUẢN LÝ CRUD KHUYẾN MÃI (View & Form Submit)
    |-------------------------------------------------------------------------- */

    /**
     * Danh sách khuyến mãi
     */
    public function index(Request $request)
    {
        $query = KhuyenMai::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('TENKM', 'like', "%$s%")
                    ->orWhere('MA_CODE', 'like', "%$s%");
            });
        }

        // Sắp xếp: Đang chạy -> Sắp chạy -> Đã kết thúc
        $khuyenmais = $query->orderByRaw("CASE 
            WHEN TRANGTHAI = 1 AND NOW() BETWEEN NGAYBATDAU AND NGAYKETTHUC THEN 1 
            WHEN TRANGTHAI = 1 AND NOW() < NGAYBATDAU THEN 2 
            ELSE 3 END")
            ->orderBy('NGAYKETTHUC', 'desc')
            ->paginate(10);

        return view('pages.manager-promotion', compact('khuyenmais'));
    }

    /**
     * Thêm mới khuyến mãi
     */
    public function them(Request $request)
    {
        $rules = [
            'TENKM' => 'required|string|max:200',
            'LOAIKM' => 'required|in:PHAN_TRAM,TIEN_MAT',
            'GIATRI' => 'required|numeric|min:0',
            'SOLUONG_MA' => 'required|integer|min:1',
            'NGAYBATDAU' => 'required|date',
            'NGAYKETTHUC' => 'required|date|after_or_equal:NGAYBATDAU',
        ];

        if ($request->input('MODE') == 'COUPON') {
            $rules['MA_CODE'] = 'required|string|max:20|unique:khuyenmai,MA_CODE|uppercase';
        } else {
            $rules['MA_CODE'] = 'nullable';
        }

        $request->validate($rules, [
            'MA_CODE.required' => 'Vui lòng nhập Mã Code.',
            'MA_CODE.unique' => 'Mã giảm giá này đã tồn tại.',
            'NGAYKETTHUC.after_or_equal' => 'Ngày kết thúc không hợp lệ.',
        ]);

        $data = $request->all();

        if ($request->input('MODE') == 'PRODUCT') {
            $data['MA_CODE'] = null;
        }
        if ($data['LOAIKM'] == 'TIEN_MAT') {
            $data['GIAM_TOI_DA'] = null;
        }

        KhuyenMai::create($data);

        return redirect()->back()->with('success', 'Tạo chương trình khuyến mãi thành công!');
    }

    /**
     * Cập nhật khuyến mãi
     */
    public function sua(Request $request, $id)
    {
        $km = KhuyenMai::findOrFail($id);

        $rules = [
            'TENKM' => 'required|string|max:200',
            'GIATRI' => 'required|numeric|min:0',
            'NGAYBATDAU' => 'required|date',
            'NGAYKETTHUC' => 'required|date|after_or_equal:NGAYBATDAU',
        ];

        if ($km->MA_CODE) {
            $rules['MA_CODE'] = 'required|string|max:20|uppercase|unique:khuyenmai,MA_CODE,' . $id . ',MAKM';
        }

        $request->validate($rules);
        $km->update($request->all());

        return redirect()->back()->with('success', 'Cập nhật khuyến mãi thành công!');
    }

    /**
     * Xóa khuyến mãi
     */
    public function xoa($id)
    {
        KhuyenMai::destroy($id);
        return redirect()->back()->with('success', 'Đã xóa khuyến mãi!');
    }

    /* |--------------------------------------------------------------------------
    | PHẦN 2: API JSON (DÀNH CHO AJAX / MODAL CHỌN SẢN PHẨM)
    |-------------------------------------------------------------------------- */

    /**
     * API 1: Lấy danh sách sản phẩm ĐÃ ÁP DỤNG cho khuyến mãi này
     * (Hiển thị cột phải Modal)
     */
    public function getSanPhams($id)
    {
        $km = KhuyenMai::findOrFail($id);

        // Lấy MASP, Tên, Hình ảnh để hiển thị list
        $appliedProducts = $km->sanPhams()
            ->select('sanpham.MASP', 'TENSP', 'HINHANHCHINH')
            ->get();

        return response()->json($appliedProducts);
    }

    /**
     * API 2: Tìm kiếm sản phẩm để thêm
     * (Hiển thị cột trái Modal)
     * - Nếu có keyword: Tìm theo tên
     * - Nếu KHÔNG keyword: Load 10 sản phẩm mới nhất (để chọn nhanh)
     */
    public function searchSanPhams(Request $request, $id)
    {
        // 1. Khởi tạo Query: Chỉ lấy SP đang bán & Chưa có trong KM này
        $query = SanPham::where('TRANGTHAI', 1)
            ->whereDoesntHave('khuyenMais', function ($q) use ($id) {
                $q->where('khuyenmai.MAKM', $id);
            });

        // 2. Xử lý tìm kiếm
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;

            // Tìm kiếm gộp: Tên sản phẩm HOẶC Mã sản phẩm
            $query->where(function ($q) use ($keyword) {
                $q->where('TENSP', 'like', "%{$keyword}%")
                    ->orWhere('MASP', 'like', "%{$keyword}%");
            });
        } else {
            // 3. Nếu KHÔNG nhập gì -> Load mặc định 20 sản phẩm mới nhập về
            $query->orderBy('NGAYTAO', 'desc');
        }

        // 4. Lấy dữ liệu (Thêm GIABAN và SOLUONGTON để hiển thị chi tiết hơn nếu cần)
        $products = $query->limit(20)
            ->select('MASP', 'TENSP', 'HINHANHCHINH', 'GIABAN', 'SOLUONGTON')
            ->get();

        return response()->json($products);
    }

    /**
     * API 3: Thêm sản phẩm vào KM (Nhận JSON từ fetch)
     */
    public function themSanPham(Request $request, $id)
    {
        $km = KhuyenMai::findOrFail($id);
        $masp = $request->input('masp');

        if (!$masp) {
            return response()->json(['error' => 'Thiếu mã sản phẩm'], 400);
        }

        // syncWithoutDetaching: Thêm vào nếu chưa có, nếu có rồi thì bỏ qua (không lỗi)
        $km->sanPhams()->syncWithoutDetaching($masp);

        return response()->json(['success' => true]);
    }

    /**
     * API 4: Xóa sản phẩm khỏi KM (Nhận JSON từ fetch)
     */
    public function xoaSanPham(Request $request, $id)
    {
        $km = KhuyenMai::findOrFail($id);
        $masp = $request->input('masp');

        if ($masp) {
            $km->sanPhams()->detach($masp);
        }

        return response()->json(['success' => true]);
    }
}