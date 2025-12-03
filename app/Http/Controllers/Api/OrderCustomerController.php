<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GioHang;
use App\Models\DonHang;
use App\Models\ChiTietDonHang;
use App\Models\KhuyenMai;
use App\Models\SanPham;
use Illuminate\Support\Facades\DB;
use OpenApi\Annotations as OA;

class OrderCustomerController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/checkout/preview",
     * tags={"Khách hàng - Đặt hàng"},
     * summary="Xem trước đơn hàng & Áp mã giảm giá",
     * description="API này dùng để tính toán tổng tiền, phí ship và kiểm tra mã giảm giá trước khi đặt.",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="coupon_code", type="string", example="SALE50", description="Mã giảm giá (nếu có)")
     * )
     * ),
     * @OA\Response(response=200, description="Trả về chi tiết tiền (Tạm tính, Ship, Giảm giá, Tổng)")
     * )
     */
    public function preview(Request $request)
    {
        $user = $request->user();
        // Lấy giỏ hàng kèm thông tin sản phẩm để tính giá
        $cartItems = GioHang::with('sanPham')->where('MAKH', $user->MAKH)->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['status' => 'error', 'message' => 'Giỏ hàng trống'], 400);
        }

        // 1. Tính Tổng Tiền Hàng (Tạm tính)
        $tongTienHang = 0;
        foreach ($cartItems as $item) {
            // Dùng giá hiện tại (đã trừ khuyến mãi sản phẩm nếu có)
            $giaThucTe = $item->sanPham->gia_ban_hien_tai;
            $tongTienHang += $giaThucTe * $item->SOLUONG;
        }

        // 2. Tính Phí Ship (Ví dụ: > 1 triệu thì Freeship, ngược lại 30k)
        $phiVanChuyen = ($tongTienHang >= 1000000) ? 0 : 30000;

        // 3. Xử lý Mã Giảm Giá (Coupon)
        $giamGia = 0;
        $couponInfo = null;

        if ($request->coupon_code) {
            $km = KhuyenMai::where('MA_CODE', $request->coupon_code)
                ->where('TRANGTHAI', 1)
                ->where('NGAYBATDAU', '<=', now())
                ->where('NGAYKETTHUC', '>=', now())
                ->first();

            if ($km) {
                // Kiểm tra số lượng mã còn lại
                if ($km->SOLUONG_MA <= 0) {
                    return response()->json(['status' => 'error', 'message' => 'Mã giảm giá đã hết lượt sử dụng'], 400);
                }

                // Kiểm tra giá trị đơn hàng tối thiểu
                if ($tongTienHang < $km->DON_TOI_THIEU) {
                    return response()->json(['status' => 'error', 'message' => "Đơn hàng chưa đạt tối thiểu: " . number_format($km->DON_TOI_THIEU) . " đ"], 400);
                }

                // Tính toán số tiền giảm
                if ($km->LOAIKM == 'PHAN_TRAM') {
                    $giamGia = $tongTienHang * ($km->GIATRI / 100);
                    // Kiểm tra mức giảm tối đa
                    if ($km->GIAM_TOI_DA && $giamGia > $km->GIAM_TOI_DA) {
                        $giamGia = $km->GIAM_TOI_DA;
                    }
                } else {
                    // Giảm tiền mặt trực tiếp
                    $giamGia = $km->GIATRI;
                }

                $couponInfo = $km;
            } else {
                return response()->json(['status' => 'error', 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn'], 400);
            }
        }

        // 4. Tổng Thanh Toán Cuối Cùng
        $tongCong = max(0, $tongTienHang + $phiVanChuyen - $giamGia);

        return response()->json([
            'status' => 'success',
            'data' => [
                'items' => $cartItems,
                'tam_tinh' => $tongTienHang,
                'phi_ship' => $phiVanChuyen,
                'giam_gia' => $giamGia,
                'tong_cong' => $tongCong,
                'coupon' => $couponInfo // Trả về thông tin mã để frontend hiển thị
            ]
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/checkout/place-order",
     * tags={"Khách hàng - Đặt hàng"},
     * summary="Chốt đơn hàng (Lưu vào DB)",
     * description="Gọi API này khi khách nhấn nút Đặt hàng. Hệ thống sẽ tính toán lại tiền một lần nữa để đảm bảo bảo mật.",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * required=true,
     * @OA\JsonContent(
     * required={"ten_nguoinhan","sdt_nguoinhan","diachi_giaohang","payment_method"},
     * @OA\Property(property="ten_nguoinhan", type="string", example="Nguyen Van A"),
     * @OA\Property(property="sdt_nguoinhan", type="string", example="0909123456"),
     * @OA\Property(property="diachi_giaohang", type="string", example="123 Đường ABC, HCM"),
     * @OA\Property(property="payment_method", type="string", example="COD"),
     * @OA\Property(property="coupon_code", type="string", example="SALE50"),
     * @OA\Property(property="ghichu", type="string", example="Giao giờ hành chính")
     * )
     * ),
     * @OA\Response(response=200, description="Đặt hàng thành công")
     * )
     */
    public function placeOrder(Request $request)
    {
        $user = $request->user();

        // 1. Validate dữ liệu giao hàng
        $request->validate([
            'ten_nguoinhan' => 'required|string|max:100',
            'sdt_nguoinhan' => 'required|string|max:15',
            'diachi_giaohang' => 'required|string',
            'payment_method' => 'required|in:COD,VNPAY'
        ]);

        // 2. Gọi lại hàm preview để lấy số liệu chính xác (Server-side calculation)
        // Không tin tưởng số liệu client gửi lên
        $previewRequest = new Request($request->all());
        $previewRequest->setUserResolver(function () use ($user) {
            return $user; });

        $previewResponse = $this->preview($previewRequest);
        $previewContent = $previewResponse->getData();

        // Nếu preview báo lỗi (ví dụ mã hết hạn trong lúc đang đặt) thì dừng lại
        if (isset($previewContent->status) && $previewContent->status == 'error') {
            return response()->json($previewContent, 400);
        }

        $bill = $previewContent->data;

        DB::beginTransaction();
        try {
            // 3. Tạo Mã Đơn Hàng duy nhất
            $madh = 'DH' . date('YmdHis') . rand(100, 999);

            // 4. Lưu Đơn Hàng
            $donhang = DonHang::create([
                'MADH' => $madh,
                'MAKH' => $user->MAKH,
                'TEN_NGUOINHAN' => $request->ten_nguoinhan,
                'SDT_NGUOINHAN' => $request->sdt_nguoinhan,
                'DIACHI_GIAOHANG' => $request->diachi_giaohang,

                // Các con số lấy từ $bill đã tính toán ở trên
                'TONGTIENHANG' => $bill->tam_tinh,
                'PHIVANCHUYEN' => $bill->phi_ship,
                'GIAMGIA' => $bill->giam_gia,
                'TONGTHANHTOAN' => $bill->tong_cong,

                'PT_THANHTOAN' => $request->payment_method,
                'TRANGTHAI_DONHANG' => 0, // Mặc định: Chờ xác nhận
                'TRANGTHAI_THANHTOAN' => 0, // Mặc định: Chưa thanh toán
                'MAKM' => $bill->coupon ? $bill->coupon->MAKM : null, // Lưu ID khuyến mãi nếu có
                'GHICHU' => $request->ghichu,
                'NGAYDAT' => now()
            ]);

            // 5. Lưu Chi Tiết Đơn Hàng & Trừ Tồn Kho
            $cartItems = GioHang::with('sanPham')->where('MAKH', $user->MAKH)->get();

            foreach ($cartItems as $item) {
                // Kiểm tra tồn kho lần cuối
                if ($item->sanPham->SOLUONGTON < $item->SOLUONG) {
                    throw new \Exception("Sản phẩm {$item->sanPham->TENSP} vừa hết hàng. Vui lòng cập nhật giỏ hàng.");
                }

                ChiTietDonHang::create([
                    'MADH' => $madh,
                    'MASP' => $item->MASP,
                    'SOLUONG' => $item->SOLUONG,
                    'DONGIA' => $item->sanPham->gia_ban_hien_tai, // Giá tại thời điểm mua
                    'THANHTIEN' => $item->sanPham->gia_ban_hien_tai * $item->SOLUONG
                ]);

                // Trừ tồn kho
                $item->sanPham->decrement('SOLUONGTON', $item->SOLUONG);
            }

            // 6. Trừ lượt sử dụng của Mã Giảm Giá (nếu có dùng)
            if ($bill->coupon) {
                KhuyenMai::where('MAKM', $bill->coupon->MAKM)->decrement('SOLUONG_MA');
            }

            // 7. Xóa Giỏ Hàng
            GioHang::where('MAKH', $user->MAKH)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Đặt hàng thành công!',
                'order_id' => $madh
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => 'Lỗi khi đặt hàng: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     * path="/api/orders",
     * tags={"Khách hàng - Đặt hàng"},
     * summary="Lịch sử đơn hàng",
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Danh sách đơn hàng")
     * )
     */
    public function history(Request $request)
    {
        $orders = DonHang::where('MAKH', $request->user()->MAKH)
            ->orderBy('NGAYDAT', 'desc')
            ->paginate(10);
        return response()->json($orders);
    }

    /**
     * @OA\Get(
     * path="/api/orders/{id}",
     * tags={"Khách hàng - Đặt hàng"},
     * summary="Chi tiết đơn hàng",
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Chi tiết đơn hàng và sản phẩm")
     * )
     */
    public function detail(Request $request, $id)
    {
        $order = DonHang::with(['chiTietDonHangs.sanPham', 'giaoHang.donViVanChuyen'])
            ->where('MAKH', $request->user()->MAKH)
            ->where('MADH', $id)
            ->first();

        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Không tìm thấy đơn hàng'], 404);
        }

        return response()->json($order);
    }

    /**
     * @OA\Post(
     * path="/api/orders/{id}/cancel",
     * tags={"Khách hàng - Đặt hàng"},
     * summary="Hủy đơn hàng (Chỉ khi chưa xử lý)",
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     * @OA\RequestBody(@OA\JsonContent(@OA\Property(property="ly_do", type="string", example="Đổi ý"))),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function cancel(Request $request, $id)
    {
        $order = DonHang::where('MAKH', $request->user()->MAKH)
            ->where('MADH', $id)
            ->firstOrFail();

        // Chỉ cho phép hủy khi đơn hàng ở trạng thái "Chờ xác nhận" (0)
        if ($order->TRANGTHAI_DONHANG != 0) {
            return response()->json(['status' => 'error', 'message' => 'Đơn hàng đã được xử lý hoặc đang giao, không thể hủy.'], 400);
        }

        DB::beginTransaction();
        try {
            // 1. Cập nhật trạng thái đơn -> 4 (Đã hủy)
            $order->TRANGTHAI_DONHANG = 4;
            $order->GHICHU = $order->GHICHU . " | Khách hủy: " . $request->ly_do;
            $order->save();

            // 2. Hoàn lại số lượng tồn kho cho từng sản phẩm
            $details = ChiTietDonHang::where('MADH', $id)->get();
            foreach ($details as $dt) {
                SanPham::where('MASP', $dt->MASP)->increment('SOLUONGTON', $dt->SOLUONG);
            }

            // 3. Hoàn lại lượt dùng Mã giảm giá (nếu có)
            if ($order->MAKM) {
                KhuyenMai::where('MAKM', $order->MAKM)->increment('SOLUONG_MA');
            }

            DB::commit();
            return response()->json(['status' => 'success', 'message' => 'Đã hủy đơn hàng thành công.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}