<?php

namespace App\Http\Controllers;

use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\GiaoHang;
use App\Models\DonViVanChuyen;
use App\Models\SanPham; // Nhớ import Model Sản phẩm để trừ/cộng tồn kho
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DonHangController extends Controller
{
    /**
     * Hiển thị danh sách đơn hàng
     */
    public function index(Request $request)
    {
        // Load sẵn các quan hệ để tránh N+1 Query
        $query = DonHang::with(['khachHang', 'chiTietDonHangs.sanPham', 'giaoHang.donViVanChuyen']);

        // 1. Lọc theo trạng thái
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('TRANGTHAI_DONHANG', $request->status);
        }

        // 2. Tìm kiếm (Mã đơn, Tên khách, SĐT)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('MADH', 'like', "%$s%")
                  ->orWhere('TEN_NGUOINHAN', 'like', "%$s%")
                  ->orWhere('SDT_NGUOINHAN', 'like', "%$s%");
            });
        }

        $donhangs = $query->orderBy('NGAYDAT', 'desc')->paginate(10);
        $donvivanchuyens = DonViVanChuyen::all(); 

        // Trả về partial view nếu là AJAX (lúc chuyển tab hoặc search)
        if ($request->ajax()) {
            return view('pages.manager-page-product.components.order_table', compact('donhangs'))->render();
        }

        return view('pages.manager-order', compact('donhangs', 'donvivanchuyens'));
    }

    /**
     * API: Lấy chi tiết đơn hàng (cho Modal)
     */
    public function getDetail($id)
    {
        $donhang = DonHang::with(['khachHang', 'chiTietDonHangs.sanPham', 'giaoHang.donViVanChuyen'])
                          ->findOrFail($id);
        return response()->json($donhang);
    }

    /**
     * Xử lý cập nhật trạng thái đơn hàng
     */
    public function updateStatus(Request $request, $id)
    {
        DB::beginTransaction(); // Bắt đầu giao dịch bảo đảm dữ liệu
        try {
            // Load chi tiết đơn hàng để xử lý tồn kho nếu cần
            $donhang = DonHang::with('chiTietDonHangs')->findOrFail($id);
            $action = $request->action; 
            
            // Lấy ID nhân viên đang đăng nhập (Nếu chưa có Auth thì lấy tạm 'NV_ADMIN')
            $currentUserId = Auth::id() ?? 'NV_ADMIN';

            switch ($action) {
                case 'confirm': // 0 -> 1: Duyệt đơn
                    if ($donhang->TRANGTHAI_DONHANG != 0) {
                        throw new \Exception('Đơn hàng không ở trạng thái chờ xử lý.');
                    }
                    $donhang->TRANGTHAI_DONHANG = 1;
                    $donhang->MANV_DUYET = $currentUserId;
                    break;

                case 'ship': // 1 -> 2: Giao hàng
                    if ($donhang->TRANGTHAI_DONHANG != 1) {
                        throw new \Exception('Đơn hàng chưa được xác nhận/chuẩn bị.');
                    }
                    if (!$request->MADVVC) {
                        throw new \Exception('Vui lòng chọn đơn vị vận chuyển.');
                    }
                    
                    $donhang->TRANGTHAI_DONHANG = 2;
                    
                    // Tạo phiếu giao hàng
                    GiaoHang::create([
                        'MAGIAOVAN' => 'GV_' . time() . '_' . rand(100,999),
                        'MADH' => $donhang->MADH,
                        'MADVVC' => $request->MADVVC,
                        'TRANGTHAI_GIAO' => 'DANG_GIAO',
                        'NGAYGIAO' => now(),
                        'MOTA_SUCO' => $request->TRACKING_CODE ?? null // Mã vận đơn
                    ]);
                    break;

                case 'complete': // 2 -> 3: Hoàn thành
                    if ($donhang->TRANGTHAI_DONHANG != 2) {
                        throw new \Exception('Đơn hàng chưa được giao đi.');
                    }
                    $donhang->TRANGTHAI_DONHANG = 3;
                    $donhang->TRANGTHAI_THANHTOAN = 1; // Cập nhật đã thanh toán
                    
                    // Cập nhật bảng giao hàng
                    $giaohang = GiaoHang::where('MADH', $donhang->MADH)->first();
                    if ($giaohang) {
                        $giaohang->update([
                            'NGAYHOANTAT' => now(), 
                            'TRANGTHAI_GIAO' => 'GIAO_THANH_CONG'
                        ]);
                    }
                    break;

                case 'cancel': // Hủy đơn (Có thể hủy ở bước 0 hoặc 1)
                    if ($donhang->TRANGTHAI_DONHANG >= 3) {
                        throw new \Exception('Không thể hủy đơn hàng đã hoàn thành hoặc đã hủy.');
                    }

                    // 1. Cập nhật trạng thái
                    $donhang->TRANGTHAI_DONHANG = 4;
                    $donhang->GHICHU = $request->LYDO ?? 'Hủy bởi nhân viên';
                    
                    // 2. QUAN TRỌNG: Hoàn lại số lượng tồn kho
                    foreach ($donhang->chiTietDonHangs as $ct) {
                        $sanpham = SanPham::find($ct->MASP);
                        if ($sanpham) {
                            $sanpham->increment('SOLUONGTON', $ct->SOLUONG);
                        }
                    }
                    break;
                
                case 'fail': // Giao thất bại / Hoàn hàng
                     $donhang->TRANGTHAI_DONHANG = 5; // Trạng thái trả hàng
                     
                     $giaohang = GiaoHang::where('MADH', $donhang->MADH)->first();
                     if($giaohang) {
                         $giaohang->update([
                             'TRANGTHAI_GIAO' => 'GIAO_THAT_BAI', 
                             'MOTA_SUCO' => $request->LYDO
                         ]);
                     }
                     
                     // Tùy chọn: Có hoàn kho hay không? (Thường hàng hoàn về sẽ nhập lại kho)
                     // Nếu muốn hoàn kho luôn:
                     foreach ($donhang->chiTietDonHangs as $ct) {
                        $sanpham = SanPham::find($ct->MASP);
                        if ($sanpham) {
                            $sanpham->increment('SOLUONGTON', $ct->SOLUONG);
                        }
                    }
                    break;
                
                default:
                    throw new \Exception('Hành động không hợp lệ.');
            }

            $donhang->save();
            DB::commit(); // Xác nhận lưu
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Cập nhật trạng thái đơn hàng thành công!'
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); // Hoàn tác nếu lỗi
            return response()->json([
                'status' => 'error', 
                'message' => $e->getMessage()
            ]);
        }
    }
}