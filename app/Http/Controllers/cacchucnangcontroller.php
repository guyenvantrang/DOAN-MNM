<?php
namespace App\Http\Controllers;

use App\Models\CacChucNang;
use Illuminate\Http\Request;

class Cacchucnangcontroller extends Controller
{
    /**
     * Hiển thị tất cả chức năng
     */
    public function index()
    {
        $chucnang = CacChucNang::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.functions', compact('chucnang'));
    }

    /**
     * Hiển thị tất cả chức năng (ajax/load table)
     */
    public function hienthitatca()
    {
        $chucnang = CacChucNang::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.functions-table', compact('chucnang'))->render();
    }

    /**
     * Tìm chức năng theo id
     */
    public function timtheoid($id)
    {
        $chucnang = CacChucNang::findOrFail($id);
        return redirect()->back()->with('chucnang', $chucnang);
    }

    /**
     * Tìm kiếm theo mã hoặc tên chức năng
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $chucnang = CacChucNang::when($query, fn($q) => $q->where('MCNANG', 'like', "%$query%")
            ->orWhere('TENCHUCNANG', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();
        return view('pages.manager-page-product.components.functions-table', compact('chucnang'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = CacChucNang::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $chucnang = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.functions-table', compact('chucnang'))->render();
    }

    /**
     * Thêm chức năng mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'TENCHUCNANG' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $chucnang = CacChucNang::create([
            'TENCHUCNANG' => $request->TENCHUCNANG,
            'MOTA' => $request->MOTA,
        ]);

        return redirect()->route('ql-chucnang')->with('success', 'Thêm chức năng thành công!');
    }

    /**
     * Xóa chức năng
     */
    public function xoa($id)
    {
        $chucnang = CacChucNang::findOrFail($id);
        $chucnang->delete();

        return redirect()->route('ql-chucnang')->with('success', 'Xóa chức năng thành công!');
    }

    /**
     * Sửa chức năng
     */
    public function sua(Request $request, $id)
    {
        $chucnang = CacChucNang::findOrFail($id);

        $request->validate([
            'TENCHUCNANG' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $chucnang->update([
            'TENCHUCNANG' => $request->TENCHUCNANG,
            'MOTA' => $request->MOTA,
        ]);

        return redirect()->route('ql-chucnang')->with('success', 'Sửa chức năng thành công!');
    }

    /**
     * Sắp xếp chức năng theo cột
     */
    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MCNANG');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MCNANG', 'TENCHUCNANG', 'MOTA', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MCNANG';
        }

        $chucnang = CacChucNang::orderBy($column, $sortDirection)->get();
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.functions-table', compact('chucnang', 'column', 'sortDirection', 'nextDirection'));
    }
}
