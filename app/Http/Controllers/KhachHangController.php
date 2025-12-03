<?php

namespace App\Http\Controllers;

use App\Models\KhachHang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KhachHangController extends Controller
{
    public function index(Request $request)
    {
        $query = KhachHang::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('HOTEN', 'like', "%$s%")
                    ->orWhere('EMAIL', 'like', "%$s%")
                    ->orWhere('SDT', 'like', "%$s%");
            });
        }

        // Sắp xếp mới nhất lên đầu
        $khachhangs = $query->orderBy('NGAYTAO', 'desc')->paginate(10);

        if ($request->ajax()) {
            return view('pages.manager-page-product.components.customer_table', compact('khachhangs'))->render();
        }

        return view('pages.manager-customer', compact('khachhangs'));
    }

    /**
     * Xử lý Thêm Mới
     */
    public function them(Request $request)
    {
        // Validation giữ nguyên (Laravel tự động trả về JSON 422 nếu gọi bằng AJAX)
        $request->validate([
            'HOTEN' => 'required|string|max:100',
            'EMAIL' => 'required|email|unique:khachhang,EMAIL',
            'SDT' => 'required|string|max:15',
            'MATKHAU' => 'required|string|min:6',
            'DIACHI' => 'nullable|string',
        ], [
            'HOTEN.required' => 'Vui lòng nhập họ tên.',
            'EMAIL.required' => 'Vui lòng nhập email.',
            'EMAIL.unique' => 'Email này đã tồn tại.',
            'MATKHAU.required' => 'Vui lòng nhập mật khẩu.',
            'MATKHAU.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
        ]);

        try {
            $data = $request->all();
            $data['MATKHAU'] = Hash::make($request->MATKHAU);
            $data['TRANGTHAI'] = 1;
            $data['NGAYTAO'] = now();

            KhachHang::create($data);

            // 👇 TRẢ VỀ JSON
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm khách hàng thành công!'
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function sua(Request $request, $id)
    {
        $khachhang = KhachHang::findOrFail($id);

        $request->validate([
            'HOTEN' => 'required|string|max:100',
            'EMAIL' => 'required|email|unique:khachhang,EMAIL,' . $id . ',MAKH',
            'MATKHAU' => 'nullable|string|min:6',
        ], [
            'EMAIL.unique' => 'Email này đã được sử dụng.',
            'MATKHAU.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.'
        ]);

        $data = $request->except(['MATKHAU']);
        if ($request->filled('MATKHAU')) {
            $data['MATKHAU'] = Hash::make($request->MATKHAU);
        }

        $khachhang->update($data);

        // 👇 TRẢ VỀ JSON
        return response()->json([
            'status' => 'success',
            'message' => 'Cập nhật thành công!'
        ]);
    }

    public function xoa($id)
    {
        try {
            $khachhang = KhachHang::withCount('donHang')->findOrFail($id);

            // Kiểm tra ràng buộc: Nếu đã mua hàng thì chỉ Khóa, không Xóa
            if ($khachhang->don_hang_count > 0) {
                $khachhang->update(['TRANGTHAI' => 0]);
                return redirect()->back()->with('warning', 'Khách hàng này đã có lịch sử mua hàng. Hệ thống đã chuyển sang trạng thái "Khóa" để bảo toàn dữ liệu.');
            }

            $khachhang->delete();
            return redirect()->back()->with('success', 'Đã xóa khách hàng vĩnh viễn!');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
        }
    }

    // API lấy chi tiết cho Modal Sửa (AJAX)
    public function getDetail($id)
    {
        return response()->json(KhachHang::find($id));
    }
}