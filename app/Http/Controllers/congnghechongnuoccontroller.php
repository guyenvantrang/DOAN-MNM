<?php
namespace App\Http\Controllers;

use App\Models\CongNgheChongNuoc;
use Illuminate\Http\Request;

class Congnghechongnuoccontroller extends Controller
{
    /**
     * Hiển thị tất cả công nghệ chống nước
     */
    public function index()
    {
        $congnghe = CongNgheChongNuoc::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.technology-waterproof', compact('congnghe'));
    }

    public function hienthitatca()
    {
        $congnghe = CongNgheChongNuoc::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.technology-waterproof-table', compact('congnghe'))->render();
    }

    public function timtheoid($id)
    {
        $congnghe = CongNgheChongNuoc::findOrFail($id);
        return redirect()->back()->with('congnghe', $congnghe);
    }

    /**
     * Tìm kiếm theo mã hoặc tên công nghệ
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $congnghe = CongNgheChongNuoc::when($query, fn($q) => $q->where('MCN', 'like', "%$query%")
            ->orWhere('TEN', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();
        return view('pages.manager-page-product.components.technology-waterproof-table', compact('congnghe'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = CongNgheChongNuoc::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $congnghe = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.technology-waterproof-table', compact('congnghe'))->render();
    }

    /**
     * Thêm công nghệ chống nước mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'TEN' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $congnghe = CongNgheChongNuoc::create([
            'TEN' => $request->TEN,
            'MOTA' => $request->MOTA,
        ]);

        return redirect()->route('ql-congnghe')->with('success', 'Thêm công nghệ chống nước thành công!');
    }

    /**
     * Xóa công nghệ
     */
    public function xoa($id)
    {
        $congnghe = CongNgheChongNuoc::findOrFail($id);
        $congnghe->delete();

        return redirect()->route('ql-congnghe')->with('success', 'Xóa công nghệ thành công!');
    }

    /**
     * Sửa công nghệ
     */
    public function sua(Request $request, $id)
    {
        $congnghe = CongNgheChongNuoc::findOrFail($id);

        $request->validate([
            'TEN' => 'required|max:100',
            'MOTA' => 'nullable|string',
        ]);

        $congnghe->update([
            'TEN' => $request->TEN,
            'MOTA' => $request->MOTA,
        ]);

        return redirect()->route('ql-congnghe')->with('success', 'Sửa công nghệ chống nước thành công!');
    }

    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MCN');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MCN', 'TEN', 'MOTA', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MCN';
        }

        $congnghe = CongNgheChongNuoc::orderBy($column, $sortDirection)->get();
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.technology-waterproof-table', compact('congnghe', 'column', 'sortDirection', 'nextDirection'));
    }
}
