<?php
namespace App\Http\Controllers;

use App\Models\ThongSoChieuRongDay;
use Illuminate\Http\Request;

class Thongsochieurongdaycontroller extends Controller
{
    /**
     * Hiển thị tất cả thông số chiều rộng dây
     */
    public function index()
    {
        $chieurongday = ThongSoChieuRongDay::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.parameters-withstrap', compact('chieurongday'));
    }

    public function hienthitatca()
    {
        $chieurongday = ThongSoChieuRongDay::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.parameters-withstrap-table', compact('chieurongday'))->render();
    }

    public function timtheoid($id)
    {
        // Tìm thông số theo id, nếu không có trả về 404
        $chieurongday = ThongSoChieuRongDay::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('chieurongday', $chieurongday);
    }

    /**
     * Tìm kiếm theo mã hoặc mô tả
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $chieurongday = ThongSoChieuRongDay::when($query, fn($q) => $q->where('MCRD', 'like', "%$query%")
            ->orWhere('CHISO', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();

        return view('pages.manager-page-product.components.parameters-withstrap-table', compact('chieurongday'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = ThongSoChieuRongDay::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $chieurongday = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.parameters-withstrap-table', compact('chieurongday'))->render();
    }

    /**
     * Thêm thông số chiều rộng dây mới
     */
    public function them(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'MOTA' => 'nullable|string',
            'CHISO' => 'required|string|max:100',
            'DONVIDO' => 'nullable|string',
        ]);

        $chieurongday = ThongSoChieuRongDay::create([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO
        ]);

        return redirect()->route('ql-chieurongday')->with('success', 'Thêm chiều rộng dây thành công!');
    }

    /**
     * Xóa thông số chiều rộng dây
     */
    public function xoa($id)
    {
        $chieurongday = ThongSoChieuRongDay::findOrFail($id);
        $chieurongday->delete();

        return redirect()->route('ql-chieurongday')->with('success', 'Xóa thông số thành công!');
    }

    /**
     * Sửa thông số chiều rộng dây
     */
    public function sua(Request $request, $id)
    {
        $chieurongday = ThongSoChieuRongDay::findOrFail($id);

        $request->validate([
            'MOTA' => 'nullable|string',
            'CHISO' => 'required|string|max:100',
            'DONVIDO' => 'nullable|string',
        ]);

        $chieurongday->update([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO
        ]);

        return redirect()->route('ql-chieurongday')->with('success', 'Sửa thông số chiều rộng dây thành công!');
    }

    /**
     * Sắp xếp theo cột
     */
    public function sapxeptheoid(Request $request)
    {
        $column = $request->query('column', 'MCRD');
        $sortDirection = $request->query('direction', 'asc');

        $validColumns = ['MCRD', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MCRD';
        }

        $chieurongday = ThongSoChieuRongDay::orderBy($column, $sortDirection)->get();
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.parameters-withstrap-table', compact('chieurongday', 'column', 'sortDirection', 'nextDirection'));
    }
}
