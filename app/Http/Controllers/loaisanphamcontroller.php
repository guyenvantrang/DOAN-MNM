<?php
namespace App\Http\Controllers;
use App\Models\LoaiSP;
use Illuminate\Http\Request;
class Loaisanphamcontroller extends Controller
{
    /**
     * Hiển thị tất cả loại sản phẩm
     */
    public function index()
    {
        $loaisps = LoaiSP::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.category', compact('loaisps'));
    }
    public function hienthitatca()
    {
        $loaisps = LoaiSP::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.category-table', compact('loaisps'))->render();
    }
    public function timloaitheoid($id)
    {
        // Tìm loại sản phẩm theo id, nếu không có trả về 404
        $loai = LoaiSP::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('editLoai', $loai);
    }


    /**
     * Tìm kiếm theo mã hoặc tên loại sản phẩm
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $loaisps = LoaiSP::when($query, fn($q) => $q->where('MALOAI', 'like', "%$query%")
            ->orWhere('TENLOAI', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();
        return view('pages.manager-page-product.components.category-table', compact('loaisps'))->render();
    }



    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = LoaiSP::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $loaisps = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.category-table', compact('loaisps'))->render();
    }

    /**
     * Thêm loại sản phẩm mới
     */
    public function them(Request $request)
    {
        // Chỉ validate TENLOAI và MOTA, MALOAI sẽ tự sinh
        $request->validate([
            'TENLOAI' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $loai = LoaiSP::create([
            'TENLOAI' => $request->TENLOAI,
            'MOTA' => $request->MOTA,
        ]);

        return redirect()->route('ql-loaisanpham')->with('success', 'Thêm loại sản phẩm thành công!');
    }
    /**
     * Xóa loại sản phẩm
     */
    public function xoa($id)
    {
        $loai = LoaiSP::findOrFail($id);
        $loai->delete();

        return redirect()->route('ql-loaisanpham')->with('success', 'Xóa loại sản phẩm thành công!');
    }

    /**
     * Sửa loại sản phẩm
     */
    public function sua(Request $request, $id)
    {
        $loai = LoaiSP::findOrFail($id);

        $request->validate([
            'TENLOAI' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $loai->update([
            'TENLOAI' => $request->TENLOAI,
            'MOTA' => $request->MOTA,
        ]);

        return redirect()->route('ql-loaisanpham')->with('success', 'Xóa loại sản phẩm thành công!');
    }
    public function sapxeptheoid(Request $request)
    {
        // Lấy tên cột muốn sắp xếp, mặc định 'MALOAI'
        $column = $request->query('column', 'MALOAI');

        // Lấy hướng sắp xếp, mặc định 'asc'
        $sortDirection = $request->query('direction', 'asc');

        // Kiểm tra xem cột có tồn tại trong model không để tránh lỗi SQL injection
        $validColumns = ['MALOAI', 'TENLOAI', 'MOTA', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MALOAI';
        }

        // Lấy dữ liệu sắp xếp
        $loaisps = LoaiSP::orderBy($column, $sortDirection)->get();

        // Toggle hướng sắp xếp cho lần click tiếp theo
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.category-table', compact('loaisps', 'column', 'sortDirection', 'nextDirection'));
    }


}
