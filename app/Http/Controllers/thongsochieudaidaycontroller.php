<?php

namespace App\Http\Controllers;

use App\Models\ThongSoChieuDaiDay;
use Illuminate\Http\Request;

class Thongsochieudaidaycontroller extends Controller
{
    /**
     * Hiển thị tất cả chiều dài dây
     */
    public function index()
    {
        $chieudaiday = ThongSoChieuDaiDay::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.parameters-lenghtstrap', compact('chieudaiday'));
    }

    public function hienthitatca()
    {
        $chieudaiday = ThongSoChieuDaiDay::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.parameters-lenghtstrap-table', compact('chieudaiday'))->render();
    }

    public function timchieudaidaytheoid($id)
    {
        // Tìm chiều dài dây theo id, nếu không có trả về 404
        $chieudaiday = ThongSoChieuDaiDay::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('chieudaiday', $chieudaiday);
    }

    /**
     * Tìm kiếm theo mã hoặc mô tả chiều dài dây
     */
    public function timkiemtheomavamoTa(Request $request)
    {
        $query = $request->input('search');
        $chieudaiday = ThongSoChieuDaiDay::when($query, fn($q) => 
            $q->where('MADD', 'like', "%$query%")
              ->orWhere('MOTA', 'like', "%$query%")
        )
        ->orderBy('NGAYTAO', 'desc')
        ->get();

        return view('pages.manager-page-product.components.parameters-lenghtstrap-table', compact('chieudaiday'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = ThongSoChieuDaiDay::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $chieudaiday = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.parameters-lenghtstrap-table', compact('chieudaiday'))->render();
    }

    /**
     * Thêm chiều dài dây mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'MOTA' => 'nullable|string',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'required|string',
        ]);

        $chieudaiday = ThongSoChieuDaiDay::create([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
        ]);

        return redirect()->route('ql-chieudaiday')->with('success', 'Thêm chiều dài dây thành công!');
    }

    /**
     * Xóa chiều dài dây
     */
    public function xoa($id)
    {
        $chieudaiday = ThongSoChieuDaiDay::findOrFail($id);
        $chieudaiday->delete();

        return redirect()->route('ql-chieudaiday')->with('success', 'Xóa chiều dài dây thành công!');
    }

    /**
     * Sửa chiều dài dây
     */
    public function sua(Request $request, $id)
    {
        $chieudaiday = ThongSoChieuDaiDay::findOrFail($id);

        $request->validate([
            'MOTA' => 'nullable|string',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'required|string',
        ]);

        $chieudaiday->update([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
        ]);

        return redirect()->route('ql-chieudaiday')->with('success', 'Sửa chiều dài dây thành công!');
    }

    /**
     * Sắp xếp theo cột
     */
    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MADD');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MADD', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MADD';
        }

        $chieudaiday = ThongSoChieuDaiDay::orderBy($column, $sortDirection)->get();

        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.parameters-lenghtstrap-table', compact('chieudaiday', 'column', 'sortDirection', 'nextDirection'));
    }
}
