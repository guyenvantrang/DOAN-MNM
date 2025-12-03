<?php
namespace App\Http\Controllers;

use App\Models\MauSac;
use Illuminate\Http\Request;

class Mausaccontroller extends Controller
{
    /**
     * Hiển thị tất cả màu sắc
     */
    public function index()
    {
        $mausac = MauSac::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.color', compact('mausac'));
    }

    public function hienthitatca()
    {
        $mausac = MauSac::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.color-table', compact('mausac'))->render();
    }

    public function timtheoid($id)
    {
        // Tìm màu theo id, nếu không có trả về 404
        $mausac = MauSac::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('mausac', $mausac);
    }

    /**
     * Tìm kiếm theo mã hoặc tên màu
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $mausac = MauSac::when($query, fn($q) => $q->where('MMS', 'like', "%$query%")
            ->orWhere('TENMAU', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();

        return view('pages.manager-page-product.components.color-table', compact('mausac'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = MauSac::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $mausac = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.color-table', compact('mausac'))->render();
    }

    /**
     * Thêm màu mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'TENMAU' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $mausac = MauSac::create([
            'TENMAU' => $request->TENMAU,
            'MOTA' => $request->MOTA
        ]);

        return redirect()->route('ql-mausac')->with('success', 'Thêm màu sắc thành công!');
    }

    /**
     * Xóa màu
     */
    public function xoa($id)
    {
        $mausac = MauSac::findOrFail($id);
        $mausac->delete();

        return redirect()->route('ql-mausac')->with('success', 'Xóa màu sắc thành công!');
    }

    /**
     * Sửa màu
     */
    public function sua(Request $request, $id)
    {
        $mausac = MauSac::findOrFail($id);

        $request->validate([
            'TENMAU' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $mausac->update([
            'TENMAU' => $request->TENMAU,
            'MOTA' => $request->MOTA
        ]);

        return redirect()->route('ql-mausac')->with('success', 'Sửa màu sắc thành công!');
    }

    /**
     * Sắp xếp theo cột
     */
    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MMS');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MMS', 'TENMAU', 'MOTA', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MMS';
        }

        $mausac = MauSac::orderBy($column, $sortDirection)->get();
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.color-table', compact('mausac', 'column', 'sortDirection', 'nextDirection'));
    }
}
