<?php

namespace App\Http\Controllers;

use App\Models\CaLamViec;
use Illuminate\Http\Request;

class CaLamViecController extends Controller
{
    public function index()
    {
        $calamviecs = CaLamViec::all(); // Lấy tất cả ca
        return view('pages.manager-shift', compact('calamviecs'));
    }

    public function them(Request $request)
    {
        $request->validate([
            'TENCA' => 'required|string|max:50',
            'GIOBATDAU' => 'required',
            'GIOKETTHUC' => 'required',
        ]);

        CaLamViec::create($request->all());

        return redirect()->back()->with('success', 'Thêm ca làm việc thành công!');
    }

    public function sua(Request $request, $id)
    {
        $ca = CaLamViec::findOrFail($id);
        
        $request->validate([
            'TENCA' => 'required|string|max:50',
            'GIOBATDAU' => 'required',
            'GIOKETTHUC' => 'required',
        ]);

        $ca->update($request->all());

        return redirect()->back()->with('success', 'Cập nhật ca làm việc thành công!');
    }

    public function xoa($id)
    {
        $ca = CaLamViec::findOrFail($id);
        // Có thể thêm kiểm tra nếu Ca đang được sử dụng trong Lịch làm việc thì không cho xóa
        // if ($ca->lichLamViecs()->count() > 0) { ... }

        $ca->delete();
        return redirect()->back()->with('success', 'Đã xóa ca làm việc!');
    }
}