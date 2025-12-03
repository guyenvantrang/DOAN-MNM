<?php

namespace App\Http\Controllers;

use App\Models\ThongSoDoDay;
use Illuminate\Http\Request;

class Thongsododaycontroller extends Controller
{
    /**
     * Hiển thị tất cả độ dày
     */
    public function index()
    {
        $dodays = ThongSoDoDay::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.parameters-thickness', compact('dodays'));
    }

    /**
     * Hiển thị tất cả độ dày (phần component table)
     */
    public function hienthitatca()
    {
        $dodays = ThongSoDoDay::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.parameters-thickness-table', compact('dodays'))->render();
    }

    /**
     * Tìm độ dày theo ID
     */
    public function timtheoid($id)
    {
        $doday = ThongSoDoDay::findOrFail($id);
        return redirect()->back()->with('doday', $doday);
    }

    /**
     * Tìm kiếm theo mã hoặc mô tả độ dày
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');

        $dodays = ThongSoDoDay::when($query, fn($q) => $q->where('MADDY', 'like', "%$query%")
            ->orWhere('CHISO', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();

        return view('pages.manager-page-product.components.parameters-thickness-table', compact('dodays'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = ThongSoDoDay::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $dodays = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.parameters-thickness-table', compact('dodays'))->render();
    }

    /**
     * Thêm độ dày mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'MOTA' => 'nullable|string|max:255',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'required|string|max:20',
        ]);

        ThongSoDoDay::create([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
            'NGAYTAO' => now(),
        ]);

        return redirect()->route('ql-doday')->with('success', 'Thêm độ dày thành công!');
    }

    /**
     * Xóa độ dày
     */
    public function xoa($id)
    {
        $doday = ThongSoDoDay::findOrFail($id);
        $doday->delete();

        return redirect()->route('ql-doday')->with('success', 'Xóa độ dày thành công!');
    }

    /**
     * Sửa độ dày
     */
    public function sua(Request $request, $id)
    {
        $doday = ThongSoDoDay::findOrFail($id);

        $request->validate([
            'MOTA' => 'nullable|string|max:255',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'required|string|max:20',
        ]);

        $doday->update([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
        ]);

        return redirect()->route('ql-doday')->with('success', 'Sửa độ dày thành công!');
    }

    /**
     * Sắp xếp theo cột
     */
    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MADDY');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MADDY', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MADDY';
        }

        $dodays = ThongSoDoDay::orderBy($column, $sortDirection)->get();

        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.parameters-thickness-table', compact('dodays', 'column', 'sortDirection', 'nextDirection'));
    }
}
