<?php

namespace App\Http\Controllers;

use App\Models\PhieuNhap;
use App\Models\NhaCungCap;
use App\Models\SanPham;
use App\Models\ChiTietPhieuNhap;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PhieuNhapController extends Controller
{
    public function index(Request $request)
    {
        $phieunhaps = PhieuNhap::with(['nhaCungCap', 'nhanVien'])->orderBy('NGAYNHAP', 'desc')->paginate(10);
        $nhacungcaps = NhaCungCap::all();
        // Lấy danh sách sản phẩm để chọn nhập
        $sanphams = SanPham::select('MASP', 'TENSP', 'GIANHAP')->get(); 

        if ($request->ajax()) {
            return view('pages.partials.import_table', compact('phieunhaps'))->render();
        }

        return view('pages.manager-import', compact('phieunhaps', 'nhacungcaps', 'sanphams'));
    }

    // Thêm phiếu nhập (Xử lý phức tạp: Header + Detail)
    public function them(Request $request)
    {
        $request->validate([
            'MANCC' => 'required',
            'products' => 'required|array', // Mảng sản phẩm từ form
            'products.*.masp' => 'required',
            'products.*.soluong' => 'required|integer|min:1',
            'products.*.gianhap' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // 1. Tạo Phiếu Nhập
            $phieuNhap = new PhieuNhap();
            $phieuNhap->MANV = Auth::guard('nhanvien')->id() ?? 'NV0001'; // Demo ID nếu chưa login
            $phieuNhap->MANCC = $request->MANCC;
            $phieuNhap->GHICHU = $request->GHICHU;
            $phieuNhap->save(); // MAPN tự sinh trong Model boot()

            $tongTien = 0;

            // 2. Tạo Chi tiết & Cập nhật kho
            foreach ($request->products as $prod) {
                $thanhTien = $prod['soluong'] * $prod['gianhap'];
                
                ChiTietPhieuNhap::create([
                    'MAPN' => $phieuNhap->MAPN,
                    'MASP' => $prod['masp'],
                    'SOLUONG' => $prod['soluong'],
                    'GIANHAP' => $prod['gianhap'],
                    'THANHTIEN' => $thanhTien
                ]);

                // Cập nhật tồn kho và giá nhập mới cho Sản Phẩm
                $sanpham = SanPham::find($prod['masp']);
                $sanpham->SOLUONGTON += $prod['soluong'];
                $sanpham->GIANHAP = $prod['gianhap'];
                $sanpham->save();

                $tongTien += $thanhTien;
            }

            // 3. Update tổng tiền phiếu nhập
            $phieuNhap->TONGTIEN = $tongTien;
            $phieuNhap->save();

            DB::commit();
            return redirect()->back()->with('success', 'Nhập hàng thành công!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Lỗi nhập hàng: ' . $e->getMessage());
        }
    }

    public function chitiet($id)
    {
        $phieunhap = PhieuNhap::with(['chiTiet.sanPham', 'nhaCungCap'])->findOrFail($id);
        return view('pages.manager-import-detail', compact('phieunhap'));
    }
}