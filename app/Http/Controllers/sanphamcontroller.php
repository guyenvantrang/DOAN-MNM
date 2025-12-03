<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

// Import tất cả các Model liên quan đến bảng Sản phẩm và Thông số
use App\Models\SanPham;
use App\Models\ThuongHieu;
use App\Models\LoaiSP;
use App\Models\ThongSoDuongKinh;
use App\Models\ThongSoChieuDaiDay;
use App\Models\ThongSoDoDay;
use App\Models\ThongSoChieuRongDay;
use App\Models\ThongSoKhoiLuong;
use App\Models\CongNgheChongNuoc;
use App\Models\MauSac;
use App\Models\CacChucNang;

class SanphamController extends Controller
{
    /**
     * Helper: Lấy tất cả dữ liệu danh mục để truyền vào View
     * Giúp code gọn hơn, tránh lặp lại ở index và form sửa
     */
    private function getAllCategories()
    {
        return [
            'thuonghieus' => ThuongHieu::all(),
            'loaisps' => LoaiSP::all(),
            'duongkinhs' => ThongSoDuongKinh::all(),
            'chieudadays' => ThongSoChieuDaiDay::all(),
            'dodays' => ThongSoDoDay::all(),
            'chieurongdays' => ThongSoChieuRongDay::all(),
            'khoiluongs' => ThongSoKhoiLuong::all(),
            'chongnuocs' => CongNgheChongNuoc::all(),
            'mausacs' => MauSac::all(),
            'chucnangs' => CacChucNang::all(),
        ];
    }

    /**
     * Trang chủ quản lý sản phẩm
     */
    public function index(Request $request)
    {
        // Khởi tạo query
        $query = SanPham::with(['thuongHieu', 'loaiSP', 'mauSac', 'congngheChongNuoc']);

        // 1. Lọc theo Danh mục (Loại SP)
        if ($request->filled('category')) {
            $categories = explode(',', $request->category);
            $query->whereIn('MALOAI', $categories);
        }

        // 2. Lọc theo Thương hiệu (Dùng whereHas)
        if ($request->filled('brands')) {
            $brands = explode(',', $request->brands);
            
            // Lọc các sản phẩm MÀ CÓ quan hệ 'thuongHieu' nằm trong danh sách
            $query->whereHas('thuongHieu', function($q) use ($brands) {
                $q->whereIn('MATHUONGHIEU', $brands);
            });
        }

        // 3. Lọc theo Giá
        if ($request->filled('min_price')) {
            $query->where('GIABAN', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('GIABAN', '<=', $request->max_price);
        }

        // 4. Lọc theo Tồn kho
        if ($request->filled('stock_type')) {
            $type = $request->stock_type;
            if ($type == 'instock') {
                $query->where('SOLUONGTON', '>', 5);
            } elseif ($type == 'low') {
                $query->whereBetween('SOLUONGTON', [1, 5]);
            } elseif ($type == 'out') {
                $query->where('SOLUONGTON', '<=', 0);
            }
        }

        // 5. Lọc theo Thời gian nhập
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('NGAYTAO', '>=', $request->start_date)
                ->whereDate('NGAYTAO', '<=', $request->end_date);
        } elseif ($request->filled('preset_date') && $request->preset_date > 0) {
            $query->where('NGAYTAO', '>=', now()->subDays($request->preset_date));
        }

        // 6. Lọc theo Thông số kỹ thuật (Ví dụ: Màu sắc, Chức năng...)
        // Lưu ý: Tên input phải khớp với name trong component specifications.blade.php
        $specs = ['MADK', 'MADD', 'MADDY', 'MCRD', 'MKL', 'MCN', 'MMS', 'MCNANG'];
        foreach ($specs as $spec) {
            if ($request->filled($spec)) {
                $query->where($spec, $request->input($spec));
            }
        }

        // 7. Tìm kiếm từ khóa (giữ nguyên logic cũ)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('TENSP', 'like', "%$search%")
                    ->orWhere('MASP', 'like', "%$search%");
            });
        }

        // Sắp xếp mặc định
        $sanphams = $query->orderBy('NGAYTAO', 'desc')->paginate(10);

        // Trả về dữ liệu cho AJAX
        if ($request->ajax()) {
            return view('pages.manager-page-product.components.product-table', compact('sanphams'))->render();
        }

        // Trả về view chính (khi load trang lần đầu)
        $categories = $this->getAllCategories(); // Hàm helper cũ của bạn
        return view('pages.manager-product', array_merge(['sanphams' => $sanphams], $categories));
    }

    /**
     * API: Trả về HTML Modal Sửa (Quan trọng: Phải có đủ biến dropdown)
     */
    public function timsanphamtheoid($id)
    {
        $chitietsanpham = SanPham::findOrFail($id);

        // Lấy lại toàn bộ danh sách danh mục để điền vào <select> trong Modal Sửa
        $categories = $this->getAllCategories();

        return view('components.message-box.detail-product', array_merge(
            ['chitietsanpham' => $chitietsanpham],
            $categories
        ));
    }

    /**
     * Chức năng Thêm Sản Phẩm
     */
    /**
     * Chức năng Thêm Sản Phẩm (Create)
     */
    public function them(Request $request)
    {
        // 1. Validate dữ liệu đầu vào
        $request->validate([
            'TENSP' => 'required|string|max:200',
            'GIABAN' => 'required|numeric|min:0',
            'GIANHAP' => 'nullable|numeric|min:0',
            'SOLUONGTON' => 'integer|min:0',
            'HINHANHCHINH' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            // Validate từng file trong mảng ảnh chi tiết
            'CHITIETHINHANH' => 'nullable|array',
            'CHITIETHINHANH.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            // Khóa ngoại
            'MATHUONGHIEU' => 'nullable|exists:thuonghieu,MATHUONGHIEU',
            'MALOAI' => 'nullable|exists:loai_sp,MALOAI',
        ]);

        // Lấy toàn bộ dữ liệu request trừ ảnh (sẽ xử lý riêng)
        $data = $request->except(['HINHANHCHINH', 'CHITIETHINHANH']);

        // 2. Xử lý Ảnh Chính (Main Image)
        if ($request->hasFile('HINHANHCHINH')) {
            $file = $request->file('HINHANHCHINH');
            // Đặt tên file unique để tránh trùng lặp: time_uniqueid.extension
            $filename = time() . '_main_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sanpham'), $filename);
            $data['HINHANHCHINH'] = 'uploads/sanpham/' . $filename;
        }

        // 3. Xử lý Ảnh Chi Tiết (Multiple Images)
        $detailImages = [];
        if ($request->hasFile('CHITIETHINHANH')) {
            foreach ($request->file('CHITIETHINHANH') as $key => $file) {
                $filename = time() . '_detail_' . $key . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/sanpham'), $filename);
                $detailImages[] = 'uploads/sanpham/' . $filename;
            }
        }
        // Gán mảng đường dẫn vào data (Model đã có casts='array' nên không cần json_encode thủ công)
        $data['CHITIETHINHANH'] = $detailImages;

        // 4. Các thiết lập mặc định
        $data['TRANGTHAI'] = 1; // Mặc định đang bán

        // Lưu vào DB (MASP sẽ được Model tự động sinh qua sự kiện booted)
        SanPham::create($data);

        return redirect()->back()->with('success', 'Thêm sản phẩm thành công!');
    }

    /**
     * Chức năng Cập nhật Sản Phẩm (Update)
     */
    public function sua(Request $request, $id)
    {
        $sanpham = SanPham::findOrFail($id);

        // 1. Validate
        $request->validate([
            'TENSP' => 'required|string|max:200',
            'GIABAN' => 'numeric|min:0',
            'GIANHAP' => 'nullable|numeric|min:0',
            'HINHANHCHINH' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:10240',
            'CHITIETHINHANH' => 'nullable|array',
            'CHITIETHINHANH.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:20480',
        ]);

        // Loại bỏ các trường không cần update trực tiếp hoặc trường xử lý riêng
        $data = $request->except(['MASP', 'CHITIETHINHANH', 'HINHANHCHINH', 'KEEP_IMAGES']);

        // ---------------------------------------------------------
        // 2. XỬ LÝ ẢNH ĐẠI DIỆN (MAIN IMAGE)
        // ---------------------------------------------------------
        if ($request->hasFile('HINHANHCHINH')) {
            // Xóa ảnh cũ nếu tồn tại vật lý
            if ($sanpham->HINHANHCHINH && File::exists(public_path($sanpham->HINHANHCHINH))) {
                File::delete(public_path($sanpham->HINHANHCHINH));
            }

            // Upload ảnh mới
            $file = $request->file('HINHANHCHINH');
            $filename = time() . '_main_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/sanpham'), $filename);

            $sanpham->HINHANHCHINH = 'uploads/sanpham/' . $filename;
        }

        // ---------------------------------------------------------
        // 3. XỬ LÝ ẢNH CHI TIẾT (ARRAY IMAGES)
        // ---------------------------------------------------------

        // A. Lấy danh sách ảnh cũ người dùng MUỐN GIỮ LẠI (từ input hidden)
        $keptImages = $request->input('KEEP_IMAGES', []);

        // B. Lấy danh sách ảnh hiện tại trong Database
        // Vì có casts='array' nên $sanpham->CHITIETHINHANH trả về mảng (hoặc null)
        $currentDbImages = $sanpham->CHITIETHINHANH ?? [];

        // C. Tìm và Xóa ảnh rác (Ảnh có trong DB nhưng không nằm trong danh sách giữ lại)
        if (is_array($currentDbImages)) {
            $deletedImages = array_diff($currentDbImages, $keptImages);

            foreach ($deletedImages as $imgToDelete) {
                if (File::exists(public_path($imgToDelete))) {
                    File::delete(public_path($imgToDelete));
                }
            }
        }

        // D. Upload và thêm ảnh MỚI
        $newImages = [];
        if ($request->hasFile('CHITIETHINHANH')) {
            foreach ($request->file('CHITIETHINHANH') as $key => $file) {
                $filename = time() . '_detail_' . $key . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/sanpham'), $filename);
                $newImages[] = 'uploads/sanpham/' . $filename;
            }
        }

        // E. Gộp mảng: [Ảnh cũ giữ lại] + [Ảnh mới upload]
        $finalDetailImages = array_merge($keptImages, $newImages);
        $sanpham->CHITIETHINHANH = $finalDetailImages;

        // ---------------------------------------------------------
        // 4. LƯU DỮ LIỆU CUỐI CÙNG
        // ---------------------------------------------------------
        $sanpham->fill($data); // Cập nhật các thông tin text (Tên, giá, mô tả...)
        $sanpham->save();      // Lưu xuống DB

        return redirect()->back()->with('success', 'Cập nhật sản phẩm thành công!');
    }
    /**
     * Chức năng Xóa Sản Phẩm
     */
    /**
     * Chức năng Xóa Sản Phẩm (Delete)
     */
    public function xoa($id)
    {
        try {
            // 1. Tìm sản phẩm
            $sanpham = SanPham::findOrFail($id);

            // 2. Xóa ảnh đại diện (Main Image)
            if ($sanpham->HINHANHCHINH && File::exists(public_path($sanpham->HINHANHCHINH))) {
                File::delete(public_path($sanpham->HINHANHCHINH));
            }

            // 3. Xóa danh sách ảnh chi tiết (Detail Images)
            // Lưu ý: Vì Model đã có casts => 'array', nên $sanpham->CHITIETHINHANH là mảng
            if (!empty($sanpham->CHITIETHINHANH) && is_array($sanpham->CHITIETHINHANH)) {
                foreach ($sanpham->CHITIETHINHANH as $imagePath) {
                    // Kiểm tra từng file xem có tồn tại không rồi mới xóa
                    if ($imagePath && File::exists(public_path($imagePath))) {
                        File::delete(public_path($imagePath));
                    }
                }
            }

            // 4. Xóa dữ liệu trong Database
            $sanpham->delete();

            return redirect()->back()->with('success', 'Đã xóa sản phẩm và toàn bộ hình ảnh liên quan!');

        } catch (\Illuminate\Database\QueryException $e) {
            // Lỗi ràng buộc khóa ngoại (Ví dụ: Sản phẩm này đã có người mua trong bảng chitietdonhang)
            if ($e->getCode() == 23000) {
                return redirect()->back()->with('error', 'Không thể xóa sản phẩm này vì đã phát sinh đơn hàng hoặc dữ liệu liên quan.');
            }
            return redirect()->back()->with('error', 'Lỗi cơ sở dữ liệu: ' . $e->getMessage());
        } catch (\Exception $e) {
            // Các lỗi khác
            return redirect()->back()->with('error', 'Đã xảy ra lỗi khi xóa: ' . $e->getMessage());
        }
    }

    /**
     * Chức năng Tìm kiếm (Mã hoặc Tên)
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $sanphams = SanPham::with(['thuongHieu', 'loaiSP'])
            ->where(function ($q) use ($query) {
                $q->where('MASP', 'like', "%$query%")
                    ->orWhere('TENSP', 'like', "%$query%");
            })
            ->paginate(10);

        if ($request->ajax()) {
            return view('pages.manager-page-product.components.product-table', compact('sanphams'))->render();
        }

        return view('pages.manager-product', array_merge(['sanphams' => $sanphams], $this->getAllCategories()));
    }

    /**
     * Chức năng Lọc theo ngày
     */
    public function timkiemtheongay(Request $request)
    {
        $query = SanPham::query();
        if ($request->date_from) {
            $query->whereDate('NGAYTAO', '>=', $request->date_from);
        }
        if ($request->date_to) {
            $query->whereDate('NGAYTAO', '<=', $request->date_to);
        }

        $sanphams = $query->paginate(10);

        if ($request->ajax()) {
            return view('pages.manager-page-product.components.product-table', compact('sanphams'))->render();
        }

        return view('pages.manager-product', array_merge(['sanphams' => $sanphams], $this->getAllCategories()));
    }

    /**
     * Chức năng Sắp xếp
     */
    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MASP');
        $direction = $request->query('direction', 'asc');

        // Chỉ cho phép sắp xếp theo các cột an toàn
        $allowedColumns = ['MASP', 'TENSP', 'GIABAN', 'SOLUONGTON', 'NGAYTAO'];
        if (!in_array($column, $allowedColumns))
            $column = 'MASP';

        $sanphams = SanPham::orderBy($column, $direction)->paginate(10);

        if ($request->ajax()) {
            return view('pages.manager-page-product.components.product-table', compact('sanphams'))->render();
        }

        return view('pages.manager-product', array_merge(['sanphams' => $sanphams], $this->getAllCategories()));
    }


}