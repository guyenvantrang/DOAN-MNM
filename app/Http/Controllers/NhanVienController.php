<?php

namespace App\Http\Controllers;

use App\Models\NhanVien;
use App\Models\ChucVu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class NhanVienController extends Controller
{
public function index(Request $request)
    {
        // Eager load chức vụ
        $nhanviens = NhanVien::with('chucVu')->paginate(10);
        
        // ✅ TỐI ƯU: Sắp xếp chức vụ theo tên để dễ tìm trong dropdown
        $chucvus = ChucVu::orderBy('TENCV', 'asc')->get(); 

        // Debug nhanh: Bỏ comment dòng dưới để xem controller lấy được mấy dòng
        // dd($chucvus->toArray()); 

        if ($request->ajax()) {
            return view('pages.partials.employee_table', compact('nhanviens'))->render();
        }

        return view('pages.manager-employee', compact('nhanviens', 'chucvus'));
    }

    // Thêm nhân viên
    public function them(Request $request)
    {
        $request->validate([
            'HO' => 'required|string|max:50',
            'TEN' => 'required|string|max:50',
            'EMAIL' => 'required|email|unique:NHANVIEN,EMAIL',
            'MATKHAU' => 'required|min:6',
            'SDT' => 'required|string|max:15',
            'MACV' => 'required|exists:CHUCVU,MACV',
            'NGAYVAOLAM' => 'nullable|date',
        ]);

        $data = $request->all();
        // Hash mật khẩu trước khi lưu
        $data['MATKHAU'] = Hash::make($request->MATKHAU);
        
        // MANV sẽ tự sinh trong Model boot() nên không cần truyền
        NhanVien::create($data);

        return redirect()->back()->with('success', 'Thêm nhân viên thành công!');
    }

    // Sửa nhân viên
    public function sua(Request $request, $id)
    {
        $nhanvien = NhanVien::findOrFail($id);

        $request->validate([
            'HO' => 'required|string|max:50',
            'TEN' => 'required|string|max:50',
            'MACV' => 'required|exists:CHUCVU,MACV',
            'TRANGTHAI' => 'integer'
        ]);

        $data = $request->except(['MATKHAU']); // Mặc định không update mật khẩu ở đây

        // Nếu có nhập mật khẩu mới thì mới update
        if ($request->filled('MATKHAU')) {
            $data['MATKHAU'] = Hash::make($request->MATKHAU);
        }

        $nhanvien->update($data);

        return redirect()->back()->with('success', 'Cập nhật thông tin nhân viên thành công!');
    }

    // Xóa nhân viên (Hoặc khóa tài khoản)
    public function xoa($id)
    {
        $nhanvien = NhanVien::findOrFail($id);
        // Thay vì xóa vĩnh viễn, thường ta chỉ set TRANGTHAI = 0
        $nhanvien->delete(); 
        return redirect()->back()->with('success', 'Đã xóa nhân viên!');
    }

    // Tìm kiếm
    public function timkiem(Request $request)
    {
        $query = $request->input('search');
        $nhanviens = NhanVien::with('chucVu')
            ->when($query, fn($q) => $q->where('TEN', 'like', "%$query%")
            ->orWhere('HO', 'like', "%$query%")
            ->orWhere('EMAIL', 'like', "%$query%"))
            ->paginate(10);

        if ($request->ajax()) {
            return view('pages.partials.employee_table', compact('nhanviens'))->render();
        }
        return view('pages.manager-employee', compact('nhanviens'));
    }
}