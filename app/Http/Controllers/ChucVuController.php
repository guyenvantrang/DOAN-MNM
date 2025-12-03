<?php

namespace App\Http\Controllers;

use App\Models\ChucVu;
use Illuminate\Http\Request;

class ChucVuController extends Controller
{
    /**
     * Hiển thị danh sách chức vụ
     */
    public function index(Request $request)
    {
        // Lấy danh sách chức vụ, sắp xếp mới nhất
        $chucvus = ChucVu::orderBy('NGAYTAO', 'desc')->paginate(10);

        // Nếu là request AJAX (khi chuyển trang hoặc tìm kiếm)
        if ($request->ajax()) {
            return view('pages.partials.role_table', compact('chucvus'))->render();
        }

        return view('pages.manager-role', compact('chucvus'));
    }

    /**
     * Thêm chức vụ mới
     */
    public function them(Request $request)
    {
        $request->validate([
            // MACV sẽ tự sinh trong Model nên không cần validate required
            'TENCV' => 'required|string|max:50|unique:CHUCVU,TENCV',
            'QUYENHAN' => 'nullable|string', // Ví dụ: json hoặc text mô tả quyền
            'MOTA' => 'nullable|string',
        ], [
            'TENCV.unique' => 'Tên chức vụ này đã tồn tại.',
            'TENCV.required' => 'Vui lòng nhập tên chức vụ.'
        ]);

        $data = $request->all();
        
        // MACV sẽ được Model tự động sinh (VD: CV001) thông qua event boot()
        ChucVu::create($data);

        return redirect()->back()->with('success', 'Thêm chức vụ thành công!');
    }

    /**
     * Cập nhật chức vụ
     */
    public function sua(Request $request, $id)
    {
        $chucvu = ChucVu::findOrFail($id);

        $request->validate([
            // Validate unique ngoại trừ chính nó
            'TENCV' => 'required|string|max:50|unique:CHUCVU,TENCV,' . $id . ',MACV',
            'QUYENHAN' => 'nullable|string',
            'MOTA' => 'nullable|string',
        ], [
            'TENCV.unique' => 'Tên chức vụ này đã tồn tại.'
        ]);

        $data = $request->all();
        $chucvu->update($data);

        return redirect()->back()->with('success', 'Cập nhật chức vụ thành công!');
    }

    /**
     * Xóa chức vụ
     * Lưu ý: Cần kiểm tra xem có nhân viên nào đang giữ chức vụ này không
     */
    public function xoa($id)
    {
        $chucvu = ChucVu::withCount('nhanViens')->findOrFail($id);

        // Kiểm tra ràng buộc dữ liệu
        if ($chucvu->nhan_viens_count > 0) {
            return redirect()->back()->with('error', 'Không thể xóa chức vụ này vì đang có ' . $chucvu->nhan_viens_count . ' nhân viên nắm giữ!');
        }

        $chucvu->delete();

        return redirect()->back()->with('success', 'Đã xóa chức vụ!');
    }

    /**
     * Tìm kiếm chức vụ
     */
    public function timkiem(Request $request)
    {
        $query = $request->input('search');
        
        $chucvus = ChucVu::query()
            ->when($query, function($q) use ($query) {
                $q->where('TENCV', 'like', "%$query%")
                  ->orWhere('MACV', 'like', "%$query%");
            })
            ->orderBy('NGAYTAO', 'desc')
            ->paginate(10);

        if ($request->ajax()) {
            return view('pages.partials.role_table', compact('chucvus'))->render();
        }

        return view('pages.manager-role', compact('chucvus'));
    }
}