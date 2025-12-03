<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GioHang;
use App\Models\SanPham;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CartController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/cart",
     * tags={"Khách hàng - Giỏ hàng"},
     * summary="Xem giỏ hàng",
     * security={{"sanctum":{}}},
     * @OA\Response(response=200, description="Danh sách sản phẩm")
     * )
     */
    public function index(Request $request) {
        $cart = GioHang::with(['sanPham' => function($q) {
            $q->select('MASP', 'TENSP', 'GIABAN', 'HINHANHCHINH', 'SOLUONGTON');
        }])->where('MAKH', $request->user()->MAKH)->get();
        
        return response()->json($cart);
    }

    /**
     * @OA\Post(
     * path="/api/cart/add",
     * tags={"Khách hàng - Giỏ hàng"},
     * summary="Thêm vào giỏ",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="masp", type="string", example="SP001"),
     * @OA\Property(property="quantity", type="integer", example=1)
     * )
     * ),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function add(Request $request) {
        $request->validate([
            'masp' => 'required|exists:sanpham,MASP',
            'quantity' => 'integer|min:1'
        ]);

        $userId = $request->user()->MAKH;
        $qty = $request->quantity ?? 1;
        $product = SanPham::find($request->masp);

        if ($product->TRANGTHAI == 0) return response()->json(['status'=>'error', 'message'=>'Sản phẩm ngừng kinh doanh'], 400);

        $item = GioHang::where('MAKH', $userId)->where('MASP', $request->masp)->first();
        $currentQty = $item ? $item->SOLUONG : 0;

        if (($currentQty + $qty) > $product->SOLUONGTON) {
            return response()->json(['status'=>'error', 'message'=>"Kho chỉ còn {$product->SOLUONGTON} sản phẩm"], 400);
        }

        if ($item) {
            $item->increment('SOLUONG', $qty);
        } else {
            GioHang::create(['MAKH' => $userId, 'MASP' => $request->masp, 'SOLUONG' => $qty, 'NGAYTHEM' => now()]);
        }

        return response()->json(['status' => 'success', 'message' => 'Đã thêm vào giỏ']);
    }

    /**
     * @OA\Put(
     * path="/api/cart/update",
     * tags={"Khách hàng - Giỏ hàng"},
     * summary="Cập nhật số lượng",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="masp", type="string", example="SP001"),
     * @OA\Property(property="quantity", type="integer", example=5)
     * )
     * ),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function update(Request $request) {
        $request->validate(['masp' => 'required', 'quantity' => 'required|integer|min:1']);
        
        $product = SanPham::find($request->masp);
        if ($request->quantity > $product->SOLUONGTON) {
            return response()->json(['status'=>'error', 'message'=>"Kho chỉ còn {$product->SOLUONGTON}"], 400);
        }

        GioHang::where('MAKH', $request->user()->MAKH)
            ->where('MASP', $request->masp)
            ->update(['SOLUONG' => $request->quantity]);

        return response()->json(['status' => 'success']);
    }

    /**
     * @OA\Delete(
     * path="/api/cart/remove",
     * tags={"Khách hàng - Giỏ hàng"},
     * summary="Xóa món hàng",
     * security={{"sanctum":{}}},
     * @OA\RequestBody(
     * @OA\JsonContent(@OA\Property(property="masp", type="string", example="SP001"))
     * ),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function remove(Request $request) {
        GioHang::where('MAKH', $request->user()->MAKH)
            ->where('MASP', $request->masp)
            ->delete();
        return response()->json(['status' => 'success']);
    }
}