<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SanPham;
use App\Models\ThuongHieu;
use App\Models\LoaiSP;
use App\Models\DanhGia;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ProductCustomerController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/products",
     * tags={"2. Sản phẩm"},
     * summary="Lấy danh sách sản phẩm (Lọc, Tìm kiếm)",
     * @OA\Parameter(name="keyword", in="query", description="Tìm tên SP", @OA\Schema(type="string")),
     * @OA\Parameter(name="brand", in="query", description="Mã thương hiệu", @OA\Schema(type="string")),
     * @OA\Parameter(name="category", in="query", description="Mã loại", @OA\Schema(type="string")),
     * @OA\Parameter(name="min_price", in="query", description="Giá thấp nhất", @OA\Schema(type="integer")),
     * @OA\Parameter(name="max_price", in="query", description="Giá cao nhất", @OA\Schema(type="integer")),
     * @OA\Parameter(name="sort", in="query", description="Sắp xếp: price_asc, price_desc, name_asc, latest", @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Danh sách sản phẩm")
     * )
     */
    public function index(Request $request) {
        $query = SanPham::where('TRANGTHAI', 1)
            ->with(['thuongHieu', 'loaiSP']);

        if ($request->keyword) {
            $query->where('TENSP', 'like', '%' . $request->keyword . '%');
        }
        if ($request->brand) {
            $query->where('MATHUONGHIEU', $request->brand);
        }
        if ($request->category) {
            $query->where('MALOAI', $request->category);
        }
        if ($request->min_price) {
            $query->where('GIABAN', '>=', $request->min_price);
        }
        if ($request->max_price) {
            $query->where('GIABAN', '<=', $request->max_price);
        }

        switch ($request->sort) {
            case 'price_asc': $query->orderBy('GIABAN', 'asc'); break;
            case 'price_desc': $query->orderBy('GIABAN', 'desc'); break;
            case 'name_asc': $query->orderBy('TENSP', 'asc'); break;
            default: $query->orderBy('NGAYTAO', 'desc'); break;
        }

        return response()->json($query->paginate(12));
    }

    /**
     * @OA\Get(
     * path="/api/products/{id}",
     * tags={"2. Sản phẩm"},
     * summary="Chi tiết sản phẩm",
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Chi tiết")
     * )
     */
    public function show($id) {
        $product = SanPham::with([
            'thuongHieu', 'loaiSP', 'mauSac', 'congNgheChongNuoc', 'chucNang',
            'duongKinh', 'chieuDaiDay', 'doDay', 'khoiLuong', 'chieuRongDay',
            'danhGia.khachHang' // Lấy đánh giá
        ])->find($id);

        if (!$product) return response()->json(['message' => 'Không tìm thấy'], 404);

        return response()->json($product);
    }

    /**
     * @OA\Get(
     * path="/api/products/{id}/related",
     * tags={"2. Sản phẩm"},
     * summary="Sản phẩm liên quan",
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     * @OA\Response(response=200, description="Danh sách 4 sản phẩm")
     * )
     */
    public function related($id) {
        $product = SanPham::find($id);
        if (!$product) return response()->json([]);

        $related = SanPham::where('TRANGTHAI', 1)
            ->where('MASP', '!=', $id)
            ->where(function($q) use ($product) {
                $q->where('MATHUONGHIEU', $product->MATHUONGHIEU)
                  ->orWhere('MALOAI', $product->MALOAI);
            })
            ->inRandomOrder()
            ->limit(4)
            ->get();

        return response()->json($related);
    }

    /**
     * @OA\Get(
     * path="/api/filters",
     * tags={"2. Sản phẩm"},
     * summary="Lấy dữ liệu bộ lọc (Thương hiệu, Loại)",
     * @OA\Response(response=200, description="JSON danh sách")
     * )
     */
    public function filters() {
        return response()->json([
            'brands' => ThuongHieu::select('MATHUONGHIEU', 'TENTHUONGHIEU')->get(),
            'categories' => LoaiSP::select('MALOAI', 'TENLOAI')->get(),
        ]);
    }

    /**
     * @OA\Post(
     * path="/api/products/{id}/review",
     * tags={"2. Sản phẩm"},
     * summary="Gửi đánh giá (Cần login)",
     * security={{"sanctum":{}}},
     * @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     * @OA\RequestBody(
     * @OA\JsonContent(
     * @OA\Property(property="rating", type="integer", example=5),
     * @OA\Property(property="comment", type="string", example="Sản phẩm tốt")
     * )
     * ),
     * @OA\Response(response=200, description="Thành công")
     * )
     */
    public function review(Request $request, $id) {
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        DanhGia::create([
            'MASP' => $id,
            'MAKH' => $request->user()->MAKH,
            'DIEM' => $request->rating,
            'NOIDUNG' => $request->comment,
            'NGAYDANHGIA' => now()
        ]);

        return response()->json(['status' => 'success', 'message' => 'Cảm ơn bạn đã đánh giá!']);
    }
}