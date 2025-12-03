<?php
namespace App\Http\Controllers;

use App\Models\ThongSoDuongKinh;
use Illuminate\Http\Request;

class Thongsoduongkinhcontroller extends Controller
{
    /**
     * Hiển thị tất cả đường kính
     */
    public function index()
    {
        $duongkinh = ThongSoDuongKinh::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.page.parameters-diameter', compact('duongkinh'));
    }

    public function hienthitatca()
    {
        $duongkinh = ThongSoDuongKinh::orderBy('NGAYTAO', 'desc')->get();
        return view('pages.manager-page-product.components.parameters-diameter-table', compact('duongkinh'))->render();
    }

    public function timduongkinhtheoid($id)
    {
        // Tìm đường kính theo id, nếu không có trả về 404
        $duongkinh = ThongSoDuongKinh::findOrFail($id);

        // Truyền dữ liệu lên session flash để hiển thị modal/message
        return redirect()->back()->with('duongkinh', $duongkinh);
    }

    /**
     * Tìm kiếm theo mã hoặc mô tả đường kính
     */
    public function timkiemtheomavaten(Request $request)
    {
        $query = $request->input('search');
        $duongkinh = ThongSoDuongKinh::when($query, fn($q) => $q->where('MADK', 'like', "%$query%")
            ->orWhere('CHISO', 'like', "%$query%"))
            ->orderBy('NGAYTAO', 'desc')
            ->get();
        return view('pages.manager-page-product.components.parameters-diameter-table', compact('duongkinh'))->render();
    }

    /**
     * Tìm kiếm theo khoảng ngày tạo
     */
    public function timkiemtheongay(Request $request)
    {
        $date_from = $request->input('date_from');
        $date_to = $request->input('date_to');

        $query = ThongSoDuongKinh::query();

        if ($date_from) {
            $query->whereDate('NGAYTAO', '>=', $date_from);
        }
        if ($date_to) {
            $query->whereDate('NGAYTAO', '<=', $date_to);
        }

        $duongkinh = $query->orderBy('NGAYTAO', 'desc')->get();

        return view('pages.manager-page-product.components.parameters-diameter-table', compact('duongkinh'))->render();
    }

    /**
     * Thêm đường kính mới
     */
    public function them(Request $request)
    {
        $request->validate([
            'MOTA' => 'required|max:255',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'nullable|string|max:50',
        ]);

        $duongkinh = ThongSoDuongKinh::create([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
            'NGAYTAO' => now(),
        ]);

        return redirect()->route('ql-duongkinh')->with('success', 'Thêm đường kính thành công!');
    }

    /**
     * Xóa đường kính
     */
    public function xoa($id)
    {
        $duongkinh = ThongSoDuongKinh::findOrFail($id);
        $duongkinh->delete();

        return redirect()->route('ql-duongkinh')->with('success', 'Xóa đường kính thành công!');
    }

    /**
     * Sửa đường kính
     */
    public function sua(Request $request, $id)
    {
        $duongkinh = ThongSoDuongKinh::findOrFail($id);

        $request->validate([
            'MOTA' => 'required|max:255',
            'CHISO' => 'required|numeric',
            'DONVIDO' => 'nullable|string|max:50',
        ]);

        $duongkinh->update([
            'MOTA' => $request->MOTA,
            'CHISO' => $request->CHISO,
            'DONVIDO' => $request->DONVIDO,
        ]);

        return redirect()->route('ql-duongkinh')->with('success', 'Sửa đường kính thành công!');
    }

    public function sapxeptheoid(Request $request)
    {
        // Lấy tên cột muốn sắp xếp, mặc định 'MADK'
        $column = $request->query('column', 'MADK');

        // Lấy hướng sắp xếp, mặc định 'asc'
        $sortDirection = $request->query('direction', 'asc');

        // Kiểm tra cột hợp lệ để tránh lỗi SQL injection
        $validColumns = ['MADK', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];
        if (!in_array($column, $validColumns)) {
            $column = 'MADK';
        }

        $duongkinh = ThongSoDuongKinh::orderBy($column, $sortDirection)->get();

        // Toggle hướng sắp xếp
        $nextDirection = $sortDirection === 'asc' ? 'desc' : 'asc';

        return view('pages.manager-page-product.components.parameters-diameter-table', compact('duongkinh', 'column', 'sortDirection', 'nextDirection'));
    }
}
