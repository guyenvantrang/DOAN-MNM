<?php
namespace App\Http\Controllers;
use App\Models\thuonghieu;
use Illuminate\Http\Request;
class Thuonghieucontroller extends Controller
{
    /**
     * Hiển thị tất cả loại sản phẩm
     */
    public function index()
    {
        $thuonghieu = thuonghieu::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.brand', compact('thuonghieu'));
    }
    public function hienthitatca()
    {
        $thuonghieu = thuonghieu::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.brand-table', compact('thuonghieu'))->render();
    }
    public function timloaitheoid($id)
    {
        // Tìm loại sản phẩm theo id, nếu không có trả về 404
        $thuonghieu = thuonghieu::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('thuonghieu', $thuonghieu);
    }


    /**
     * Tìm kiếm theo mã hoặc tên loại sản phẩm
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $thuonghieu = thuonghieu::when($query, fn($q) => $q->where('MATHUONGHIEU', 'like', "%$query%")
            ->orWhere('TENTHUONGHIEU', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();
        return view('pages.manager-page-product.components.brand-table', compact('thuonghieu'))->render();
    }



    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = thuonghieu::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $thuonghieu = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.brand-table', compact('thuonghieu'))->render();
    }

    /**
     * Thêm loại sản phẩm mới
     */
    public function them(Request $request)
    {
        // Chỉ validate TENLOAI và MOTA, MALOAI sẽ tự sinh
        $request->validate([
            'TENTHUONGHIEU' => 'required|max:100',
            'MOTA' => 'nullable|string',
            'QUOCGIA' => 'nullable|string',
        ]);

        $thuonghieu = thuonghieu::create([
            'TENTHUONGHIEU' => $request->TENTHUONGHIEU,
            'MOTA' => $request->MOTA,
            'QUOCGIA' =>$request->QUOCGIA
        ]);

        return redirect()->route('ql-thuonghieu')->with('success', 'Thêm thương hiệu thành công!');
    }
    /**
     * Xóa loại sản phẩm
     */
    public function xoa($id)
    {
        $thuonghieu = thuonghieu::findOrFail($id);
        $thuonghieu->delete();

        return redirect()->route('ql-thuonghieu')->with('success', 'Xóa loại sản phẩm thành công!');
    }

    /**
     * Sửa loại sản phẩm
     */
    public function sua(Request $request, $id)
    {
        $thuonghieu = thuonghieu::findOrFail($id);

        $request->validate([
            'TENTHUONGHIEU' => 'required|max:100',
            'MOTA' => 'nullable|string',
            'QUOCGIA' => 'nullable|string',
        ]);

        $thuonghieu->update([
            'TENTHUONGHIEU' => $request->TENTHUONGHIEU,
            'MOTA' => $request->MOTA,
            'QUOCGIA' => $request->QUOCGIA
        ]);

        return redirect()->route('ql-thuonghieu')->with('success', 'sửa thương hiệu sản phẩm thành công!');
    }
    public function sapxeptheoid(Request $request)
    {
        // Lấy tên cột muốn sắp xếp, mặc định 'MALOAI'
        $column = $request->query('column', 'MATHUONGHIEU');

        // Lấy hướng sắp xếp, mặc định 'asc'
        $sortDirection = $request->query('direction', 'asc');

        // Kiểm tra xem cột có tồn tại trong model không để tránh lỗi SQL injection
        $validColumns = ['MATHUONGHIEU', 'TENTHUONGHIEU', 'MOTA','QUOCGIA', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MATHUONGHIEU';
        }

        // Lấy dữ liệu sắp xếp
        $thuonghieu = thuonghieu::orderBy($column, $sortDirection)->get();

        // Toggle hướng sắp xếp cho lần click tiếp theo
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.brand-table', compact('thuonghieu', 'column', 'sortDirection', 'nextDirection'));
    }


}
