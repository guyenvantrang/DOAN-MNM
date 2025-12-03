<?php
namespace App\Http\Controllers;

use App\Models\ThongSoKhoiLuong;
use Illuminate\Http\Request;

class Thongsokhoiluongcontroller extends Controller
{
    /**
     * Hiển thị tất cả khối lượng
     */
    public function index()
    {
        $khoiluong = ThongSoKhoiLuong::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.parameters-weight', compact('khoiluong'));
    }

    public function hienthitatca()
    {
        $khoiluong = ThongSoKhoiLuong::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.parameters-weight-table', compact('khoiluong'))->render();
    }

    public function timtheoid($id)
    {
        // Tìm khối lượng theo id, nếu không có trả về 404
        $khoiluong = ThongSoKhoiLuong::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('khoiluong', $khoiluong);
    }

    /**
     * Tìm kiếm theo mã hoặc mô tả
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $khoiluong = ThongSoKhoiLuong::when($query, fn($q) => $q->where('MKL', 'like', "%$query%")
            ->orWhere('CHISO', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();

        return view('pages.manager-page-product.components.parameters-weight-table', compact('khoiluong'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = ThongSoKhoiLuong::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $khoiluong = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.parameters-weight-table', compact('khoiluong'))->render();
    }

    /**
     * Thêm khối lượng mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'MOTA' => 'nullable|string',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'nullable|string',
        ]);

        $khoiluong = ThongSoKhoiLuong::create([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
        ]);

        return redirect()->route('ql-khoiluong')->with('success', 'Thêm khối lượng thành công!');
    }

    /**
     * Xóa khối lượng
     */
    public function xoa($id)
    {
        $khoiluong = ThongSoKhoiLuong::findOrFail($id);
        $khoiluong->delete();

        return redirect()->route('ql-khoiluong')->with('success', 'Xóa khối lượng thành công!');
    }

    /**
     * Sửa khối lượng
     */
    public function sua(Request $request, $id)
    {
        $khoiluong = ThongSoKhoiLuong::findOrFail($id);

        $request->validate([
            'MOTA' => 'nullable|string',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'nullable|string',
        ]);

        $khoiluong->update([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
        ]);

        return redirect()->route('ql-khoiluong')->with('success', 'Sửa khối lượng thành công!');
    }

    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MKL');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MKL', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MKL';
        }

        $khoiluong = ThongSoKhoiLuong::orderBy($column, $sortDirection)->get();
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.parameters-weight-table', compact('khoiluong', 'column', 'sortDirection', 'nextDirection'));
    }
}
