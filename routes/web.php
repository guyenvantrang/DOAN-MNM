<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SanphamController;
use App\Http\Controllers\Loaisanphamcontroller;
use App\Http\Controllers\Thuonghieucontroller;
use App\Http\Controllers\Thongsoduongkinhcontroller;
use App\Http\Controllers\Thongsochieudaidaycontroller;
use App\Http\Controllers\Thongsododaycontroller;
use App\Http\Controllers\Thongsochieurongdaycontroller;
use App\Http\Controllers\Thongsokhoiluongcontroller;
use App\Http\Controllers\Congnghechongnuoccontroller;
use App\Http\Controllers\Mausaccontroller;
use App\Http\Controllers\Cacchucnangcontroller;
use App\Http\Controllers\LichLamViecController;


// =========== Điều hướng các trang chính ======================================================================
// Trang chủ
Route::get('/', function () {
    return view('pages.home');
})->name('home');

// Quản lý sản phẩm
Route::get('/manager_product', [SanphamController::class, 'index'])->name('ql_sanpham');

// Quản lý nhân viên
Route::get('/ql_nhan_vien', function () {
    return view('pages.manager_staff');
})->name('ql_nhan_vien');

// Quản lý xếp lịch
Route::get('/ql_nhan_vien_xep_lich', function () {
    return view('pages.employee_scheduling');
})->name('ql_nhan_vien_xep_lich');

// Thống kê doanh thu
Route::get('/ql_thong_ke', function () {
    return view('pages.statistical');
})->name('ql_thong_ke');

// Phân quyền truy cập
Route::get('/ql_phan_quyen', function () {
    return view('pages.decentralization');
})->name('ql_phan_quyen');






//====== Quản lý load dữ liệu lên bộ lọc==========================================================================
Route::get('/thuonghieu-json', [SanPhamController::class, 'getThuongHieu']);
Route::get('/loaisp-json', [SanPhamController::class, 'getLoaiSP']);
Route::get('/duongkinh-json', [SanPhamController::class, 'getThongSoDuongKinh']);
Route::get('/chieudaiday-json', [SanPhamController::class, 'getThongSoChieuDaiDay']);
Route::get('/doday-json', [SanPhamController::class, 'getThongSoDoDay']);
Route::get('/chieurongday-json', [SanPhamController::class, 'getThongSoChieuRongDay']);
Route::get('/khoiluong-json', [SanPhamController::class, 'getThongSoKhoiLuong']);
Route::get('/chongnuoc-json', [SanPhamController::class, 'getCongNgheChongNuoc']);
Route::get('/mausac-json', [SanPhamController::class, 'getMauSac']);
Route::get('/chucnang-json', [SanPhamController::class, 'getCacChucNang']);




// ========== Quản lý trang loại sản phẩm ============================================================================
Route::get('/loaisanpham', [Loaisanphamcontroller::class, 'index'])->name('ql-loaisanpham');
Route::prefix('admin')->group(function () {
    // Trang danh sách loại sản phẩm
    Route::get('/loai_sp/hienthitatca', [Loaisanphamcontroller::class, 'hienthitatca'])->name('loai_sp.hienthitatca');

    // Tìm kiếm theo mã hoặc tên (AJAX)
    Route::get('/loai_sp/timkiemtheomavaten', [Loaisanphamcontroller::class, 'timkiemtheomavaten'])
        ->name('loai_sp.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/loai_sp/timkiemtheongay', [Loaisanphamcontroller::class, 'timkiemtheongay'])
        ->name('loai_sp.timkiemtheongay');

    // Thêm loại sản phẩm
    Route::post('/loaisanpham', [Loaisanphamcontroller::class, 'them'])->name('loai_sp.them');

    // Cập nhật loại sản phẩm
    Route::put('/loai_sp/sua/{id}', [Loaisanphamcontroller::class, 'sua'])->name('loai_sp.sua');

    // Xóa loại sản phẩm
    Route::delete('/loai_sp/xoa/{id}', [Loaisanphamcontroller::class, 'xoa'])->name('loai_sp.xoa');
    // Tìm loại theo id
    Route::get('/loai_sp/{id}', [Loaisanphamcontroller::class, 'timloaitheoid'])->name('loai_sp.timloaitheoid');
    // Sắp xếp id
    Route::get('/admin/loai_sp/sapxep', [Loaisanphamcontroller::class, 'sapxeptheoid'])->name('loai_sp.sapxeptheoid');
});

// ========== Quản lý trang thương hiệu ============================================================================
Route::get('/thuonghieu', [Thuonghieucontroller::class, 'index'])->name('ql-thuonghieu');
Route::prefix('thuonghieu')->group(function () {
    // Trang danh sách loại sản phẩm
    Route::get('/thuonghieu_sp/hienthitatca', [Thuonghieucontroller::class, 'hienthitatca'])->name('thuonghieu_sp.hienthitatca');

    // Tìm kiếm theo mã hoặc tên (AJAX)
    Route::get('/thuonghieu_sp/timkiemtheomavaten', [Thuonghieucontroller::class, 'timkiemtheomavaten'])
        ->name('thuonghieu_sp.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/thuonghieu_sp/timkiemtheongay', [Thuonghieucontroller::class, 'timkiemtheongay'])
        ->name('thuonghieu_sp.timkiemtheongay');

    // Thêm loại sản phẩm
    Route::post('/thuonghieusanpham', [Thuonghieucontroller::class, 'them'])->name('thuonghieu-sp.them');

    // Cập nhật loại sản phẩm
    Route::put('/thuonghieu_sp/sua/{id}', [Thuonghieucontroller::class, 'sua'])->name('thuonghieu-sp.sua');

    // Xóa loại sản phẩm
    Route::delete('/thuonghieu_sp/xoa/{id}', [Thuonghieucontroller::class, 'xoa'])->name('thuonghieu-sp.xoa');
    // Tìm loại theo id
    Route::get('/thuonghieu_sp/{id}', [Thuonghieucontroller::class, 'timloaitheoid'])->name('thuonghieu-sp.timloaitheoid');
    // Sắp xếp id
    Route::get('/admin/thuonghieu_sp/sapxep', [Thuonghieucontroller::class, 'sapxeptheoid'])->name('thuonghieu-sp.sapxeptheoid');


});

// ========== Quản lý trang đường kính ============================================================================
Route::get('/duongkinh', [Thongsoduongkinhcontroller::class, 'index'])->name('ql-duongkinh');

Route::prefix('duongkinh')->group(function () {
    // Trang danh sách đường kính
    Route::get('/duongkinh_sp/hienthitatca', [Thongsoduongkinhcontroller::class, 'hienthitatca'])->name('duongkinh_sp.hienthitatca');

    // Tìm kiếm theo mã hoặc mô tả (AJAX)
    Route::get('/duongkinh_sp/timkiemtheomavaten', [Thongsoduongkinhcontroller::class, 'timkiemtheomavaten'])
        ->name('duongkinh_sp.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/duongkinh_sp/timkiemtheongay', [Thongsoduongkinhcontroller::class, 'timkiemtheongay'])
        ->name('duongkinh_sp.timkiemtheongay');

    // Thêm đường kính
    Route::post('/duongkinhsp', [Thongsoduongkinhcontroller::class, 'them'])->name('duongkinh-sp.them');

    // Cập nhật đường kính
    Route::put('/duongkinh_sp/sua/{id}', [Thongsoduongkinhcontroller::class, 'sua'])->name('duongkinh-sp.sua');

    // Xóa đường kính
    Route::delete('/duongkinh_sp/xoa/{id}', [Thongsoduongkinhcontroller::class, 'xoa'])->name('duongkinh-sp.xoa');

    // Tìm đường kính theo id
    Route::get('/duongkinh_sp/{id}', [Thongsoduongkinhcontroller::class, 'timduongkinhtheoid'])->name('duongkinh-sp.timduongkinhtheoid');

    // Sắp xếp id
    Route::get('/admin/duongkinh_sp/sapxep', [Thongsoduongkinhcontroller::class, 'sapxeptheoid'])->name('duongkinh-sp.sapxeptheoid');
});

// ========== Quản lý trang chiều dài dây ============================================================================

Route::get('/chieudaiday', [Thongsochieudaidaycontroller::class, 'index'])->name('ql-chieudaiday');

Route::prefix('chieudaiday')->group(function () {
    // Trang danh sách chiều dài dây
    Route::get('/chieudaiday_sp/hienthitatca', [Thongsochieudaidaycontroller::class, 'hienthitatca'])
        ->name('chieudaiday_sp.hienthitatca');

    // Tìm kiếm theo mã hoặc mô tả (AJAX)
    Route::get('/chieudaiday_sp/timkiemtheomavamoTa', [Thongsochieudaidaycontroller::class, 'timkiemtheomavamoTa'])
        ->name('chieudaiday_sp.timkiemtheomavamoTa');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/chieudaiday_sp/timkiemtheongay', [Thongsochieudaidaycontroller::class, 'timkiemtheongay'])
        ->name('chieudaiday_sp.timkiemtheongay');

    // Thêm chiều dài dây
    Route::post('/chieudaiday_sp', [Thongsochieudaidaycontroller::class, 'them'])
        ->name('chieudaiday-sp.them');

    // Cập nhật chiều dài dây
    Route::put('/chieudaiday_sp/sua/{id}', [Thongsochieudaidaycontroller::class, 'sua'])
        ->name('chieudaiday-sp.sua');

    // Xóa chiều dài dây
    Route::delete('/chieudaiday_sp/xoa/{id}', [Thongsochieudaidaycontroller::class, 'xoa'])
        ->name('chieudaiday-sp.xoa');

    // Tìm chiều dài dây theo id
    Route::get('/chieudaiday_sp/{id}', [Thongsochieudaidaycontroller::class, 'timchieudaidaytheoid'])
        ->name('chieudaiday-sp.timchieudaidaytheoid');

    // Sắp xếp theo cột (id, mô tả, chỉ số, đơn vị đo, ngày tạo)
    Route::get('/admin/chieudaiday_sp/sapxep', [Thongsochieudaidaycontroller::class, 'sapxeptheoid'])
        ->name('chieudaiday-sp.sapxeptheoid');
});


// ========== Quản lý trang độ dày ============================================================================


Route::get('/doday', [Thongsododaycontroller::class, 'index'])->name('ql-doday');

Route::prefix('doday')->group(function () {
    // Trang danh sách độ dày
    Route::get('/doday_sp/hienthitatca', [Thongsododaycontroller::class, 'hienthitatca'])->name('doday_sp.hienthitatca');

    // Tìm kiếm theo mã hoặc mô tả (AJAX)
    Route::get('/doday_sp/timkiemtheomavaten', [Thongsododaycontroller::class, 'timkiemtheomavaten'])
        ->name('doday_sp.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/doday_sp/timkiemtheongay', [Thongsododaycontroller::class, 'timkiemtheongay'])
        ->name('doday_sp.timkiemtheongay');

    // Thêm độ dày mới
    Route::post('/dodaysanpham', [Thongsododaycontroller::class, 'them'])->name('doday-sp.them');

    // Cập nhật độ dày
    Route::put('/doday_sp/sua/{id}', [Thongsododaycontroller::class, 'sua'])->name('doday-sp.sua');

    // Xóa độ dày
    Route::delete('/doday_sp/xoa/{id}', [Thongsododaycontroller::class, 'xoa'])->name('doday-sp.xoa');

    // Tìm độ dày theo id
    Route::get('/doday_sp/{id}', [Thongsododaycontroller::class, 'timtheoid'])->name('doday-sp.timtheoid');

    // Sắp xếp theo id
    Route::get('/admin/doday_sp/sapxep', [Thongsododaycontroller::class, 'sapxeptheoid'])->name('doday-sp.sapxeptheoid');
});


// ========== Quản lý trang chiều rộng dây ====================================================================
Route::get('/chieurongday', [Thongsochieurongdaycontroller::class, 'index'])->name('ql-chieurongday');

Route::prefix('chieurongday')->group(function () {
    // Trang danh sách chiều rộng dây
    Route::get('/crd/hienthitatca', [Thongsochieurongdaycontroller::class, 'hienthitatca'])->name('crd.hienthitatca');

    // Tìm kiếm theo mã hoặc mô tả (AJAX)
    Route::get('/crd/timkiemtheomavaten', [Thongsochieurongdaycontroller::class, 'timkiemtheomavaten'])
        ->name('crd.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/crd/timkiemtheongay', [Thongsochieurongdaycontroller::class, 'timkiemtheongay'])
        ->name('crd.timkiemtheongay');

    // Thêm thông số chiều rộng dây
    Route::post('/crd/them', [Thongsochieurongdaycontroller::class, 'them'])->name('crd.them');

    // Cập nhật thông số chiều rộng dây
    Route::put('/crd/sua/{id}', [Thongsochieurongdaycontroller::class, 'sua'])->name('crd.sua');

    // Xóa thông số chiều rộng dây
    Route::delete('/crd/xoa/{id}', [Thongsochieurongdaycontroller::class, 'xoa'])->name('crd.xoa');

    // Tìm thông số theo id
    Route::get('/crd/{id}', [Thongsochieurongdaycontroller::class, 'timtheoid'])->name('crd.timtheoid');

    // Sắp xếp theo id
    Route::get('/admin/crd/sapxep', [Thongsochieurongdaycontroller::class, 'sapxeptheoid'])->name('crd.sapxep');
});

// ========== Quản lý trang khối lượng đồng hồ ====================================================================
Route::get('/khoiluong', [Thongsokhoiluongcontroller::class, 'index'])->name('ql-khoiluong');

Route::prefix('khoiluong')->group(function () {
    // Trang danh sách khối lượng
    Route::get('/kl/hienthitatca', [Thongsokhoiluongcontroller::class, 'hienthitatca'])->name('kl.hienthitatca');

    // Tìm kiếm theo mã hoặc mô tả (AJAX)
    Route::get('/kl/timkiemtheomavaten', [Thongsokhoiluongcontroller::class, 'timkiemtheomavaten'])
        ->name('kl.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/kl/timkiemtheongay', [Thongsokhoiluongcontroller::class, 'timkiemtheongay'])
        ->name('kl.timkiemtheongay');

    // Thêm khối lượng mới
    Route::post('/kl/them', [Thongsokhoiluongcontroller::class, 'them'])->name('kl.them');

    // Cập nhật khối lượng
    Route::put('/kl/sua/{id}', [Thongsokhoiluongcontroller::class, 'sua'])->name('kl.sua');

    // Xóa khối lượng
    Route::delete('/kl/xoa/{id}', [Thongsokhoiluongcontroller::class, 'xoa'])->name('kl.xoa');

    // Tìm khối lượng theo id
    Route::get('/kl/{id}', [Thongsokhoiluongcontroller::class, 'timtheoid'])->name('kl.timtheoid');

    // Sắp xếp theo id
    Route::get('/admin/kl/sapxep', [Thongsokhoiluongcontroller::class, 'sapxeptheoid'])->name('kl.sapxep');
});


// ========== Quản lý công nghệ chống nước ====================================================================
Route::get('/congnghe', [Congnghechongnuoccontroller::class, 'index'])->name('ql-congnghe');

Route::prefix('congnghe')->group(function () {
    // Trang danh sách công nghệ
    Route::get('/cn/hienthitatca', [Congnghechongnuoccontroller::class, 'hienthitatca'])->name('cn.hienthitatca');

    // Tìm kiếm theo mã hoặc tên (AJAX)
    Route::get('/cn/timkiemtheomavaten', [Congnghechongnuoccontroller::class, 'timkiemtheomavaten'])
        ->name('cn.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/cn/timkiemtheongay', [Congnghechongnuoccontroller::class, 'timkiemtheongay'])
        ->name('cn.timkiemtheongay');

    // Thêm công nghệ mới
    Route::post('/cn/them', [Congnghechongnuoccontroller::class, 'them'])->name('cn.them');

    // Cập nhật công nghệ
    Route::put('/cn/sua/{id}', [Congnghechongnuoccontroller::class, 'sua'])->name('cn.sua');

    // Xóa công nghệ
    Route::delete('/cn/xoa/{id}', [Congnghechongnuoccontroller::class, 'xoa'])->name('cn.xoa');

    // Tìm công nghệ theo id
    Route::get('/cn/{id}', [Congnghechongnuoccontroller::class, 'timtheoid'])->name('cn.timtheoid');

    // Sắp xếp theo id
    Route::get('/admin/cn/sapxep', [Congnghechongnuoccontroller::class, 'sapxeptheoid'])->name('cn.sapxep');
});

// ========== Quản lý màu sắc ====================================================================
Route::get('/mausac', [Mausaccontroller::class, 'index'])->name('ql-mausac');

Route::prefix('mausac')->group(function () {
    // Trang danh sách màu sắc
    Route::get('/ms/hienthitatca', [Mausaccontroller::class, 'hienthitatca'])->name('ms.hienthitatca');

    // Tìm kiếm theo mã hoặc tên màu (AJAX)
    Route::get('/ms/timkiemtheomavaten', [Mausaccontroller::class, 'timkiemtheomavaten'])
        ->name('ms.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/ms/timkiemtheongay', [Mausaccontroller::class, 'timkiemtheongay'])
        ->name('ms.timkiemtheongay');

    // Thêm màu mới
    Route::post('/ms/them', [Mausaccontroller::class, 'them'])->name('ms.them');

    // Cập nhật màu
    Route::put('/ms/sua/{id}', [Mausaccontroller::class, 'sua'])->name('ms.sua');

    // Xóa màu
    Route::delete('/ms/xoa/{id}', [Mausaccontroller::class, 'xoa'])->name('ms.xoa');

    // Tìm màu theo id
    Route::get('/ms/{id}', [Mausaccontroller::class, 'timtheoid'])->name('ms.timtheoid');

    // Sắp xếp theo id
    Route::get('/admin/ms/sapxep', [Mausaccontroller::class, 'sapxeptheoid'])->name('ms.sapxep');
});
// ========== Quản lý chức năng ====================================================================

Route::get('/chucnang', [Cacchucnangcontroller::class, 'index'])->name('ql-chucnang');

Route::prefix('chucnang')->group(function () {
    // Trang danh sách chức năng
    Route::get('/cn/hienthitatca', [Cacchucnangcontroller::class, 'hienthitatca'])->name('cn.hienthitatca_');

    // Tìm kiếm theo mã hoặc tên chức năng (AJAX)
    Route::get('/cn/timkiemtheomavaten', [Cacchucnangcontroller::class, 'timkiemtheomavaten'])
        ->name('cn.timkiemtheomavaten');

    // Tìm kiếm theo ngày tạo (AJAX)
    Route::get('/cn/timkiemtheongay', [Cacchucnangcontroller::class, 'timkiemtheongay'])
        ->name('cn.timkiemtheongay');

    // Thêm chức năng mới
    Route::post('/cn/them', [Cacchucnangcontroller::class, 'them'])->name('cn.them_');

    // Cập nhật chức năng
    Route::put('/cn/sua/{id}', [Cacchucnangcontroller::class, 'sua'])->name('cn.sua_');

    // Xóa chức năng
    Route::delete('/cn/xoa/{id}', [Cacchucnangcontroller::class, 'xoa'])->name('cn.xoa_');

    // Tìm chức năng theo id
    Route::get('/cn/{id}', [Cacchucnangcontroller::class, 'timtheoid'])->name('cn.timtheoid_');

    // Sắp xếp theo id
    Route::get('/admin/cn/sapxep', [Cacchucnangcontroller::class, 'sapxeptheoid'])->name('cn.sapxep_');
});
// 1. Route trang chủ quản lý sản phẩm (Vừa load trang, vừa load Ajax ban đầu)
Route::get('/sanpham', [SanphamController::class, 'index'])->name('ql-sanpham');

// 2. Nhóm các chức năng AJAX và thao tác dữ liệu
Route::prefix('sanpham')->group(function () {

    // --- Các chức năng Tìm kiếm & Lọc (GET) ---
    // (Phải đặt trên route {id} để tránh xung đột)

    // Tìm kiếm theo mã hoặc tên
    Route::get('/sp/tim-kiem-ma-ten', [SanphamController::class, 'timkiemtheomavaten'])
        ->name('sp.timkiemtheomavaten');

    // Tìm kiếm theo ngày
    Route::get('/sp/tim-kiem-ngay', [SanphamController::class, 'timkiemtheongay'])
        ->name('sp.timkiemtheongay');

    // Sắp xếp (Tôi đã rút gọn URL)
    Route::get('/sp/sap-xep', [SanphamController::class, 'sapxeptheoid'])
        ->name('sp.sapxep');

    // --- Các chức năng Thao tác (CRUD) ---

    // Thêm mới
    Route::post('/sp/them', [SanphamController::class, 'them'])->name('sp.them');

    // Cập nhật (Lưu ý: Form HTML phải có @method('PUT'))
    Route::put('/sp/sua/{id}', [SanphamController::class, 'sua'])->name('sp.sua');

    // Xóa (Lưu ý: Form HTML phải có @method('DELETE'))
    Route::delete('/sp/xoa/{id}', [SanphamController::class, 'xoa'])->name('sp.xoa');

    // --- Chức năng lấy dữ liệu chi tiết cho Modal (GET) ---
    // Route này bắt mọi URL có dạng /sanpham/sp/XXXX nên phải để cuối cùng trong nhóm GET
    Route::get('/sp/{id}', [SanphamController::class, 'timsanphamtheoid'])->name('sp.timtheoid');
});


use App\Http\Controllers\NhanVienController;
// --- 1. QUẢN LÝ NHÂN VIÊN ---
Route::prefix('nhan-vien')->name('nv.')->group(function () {
    Route::get('/', [NhanVienController::class, 'index'])->name('index');
    Route::get('/tim-kiem', [NhanVienController::class, 'timkiem'])->name('timkiem');
    Route::post('/them', [NhanVienController::class, 'them'])->name('them');
    Route::put('/sua/{id}', [NhanVienController::class, 'sua'])->name('sua');
    Route::delete('/xoa/{id}', [NhanVienController::class, 'xoa'])->name('xoa');

    // 👇 QUAN TRỌNG: Phải trỏ vào Controller (Không dùng function view trực tiếp)
    Route::get('/lap-lich', [LichLamViecController::class, 'index'])->name('lichlam');
});

use App\Http\Controllers\ChucVuController; // Nhớ import dòng này


// --- QUẢN LÝ CHỨC VỤ (MỚI) ---
Route::prefix('chuc-vu')->name('cv.')->group(function () {
    // Danh sách & Trang chủ
    Route::get('/', [ChucVuController::class, 'index'])->name('index');

    // Tìm kiếm (Ajax)
    Route::get('/tim-kiem', [ChucVuController::class, 'timkiem'])->name('timkiem');

    // Thêm
    Route::post('/them', [ChucVuController::class, 'them'])->name('them');

    // Sửa
    Route::put('/sua/{id}', [ChucVuController::class, 'sua'])->name('sua');

    // Xóa
    Route::delete('/xoa/{id}', [ChucVuController::class, 'xoa'])->name('xoa');
});



// Nhóm route xử lý Lịch làm việc (AJAX)
Route::prefix('nhan-vien/lap-lich')->name('lich.')->group(function () {
    // Lấy dữ liệu hiển thị lên bảng
    Route::get('/get-data', [LichLamViecController::class, 'getSchedule'])->name('get');

    // Lưu (Thêm mới hoặc Cập nhật)
    Route::post('/store', [LichLamViecController::class, 'store'])->name('store');

    // Xóa
    Route::delete('/delete/{id}', [LichLamViecController::class, 'destroy'])->name('delete');
});
use App\Http\Controllers\CaLamViecController; // Nhớ import dòng này

// --- QUẢN LÝ CA LÀM VIỆC ---
Route::prefix('ca-lam-viec')->name('ca.')->group(function () {
    Route::get('/', [CaLamViecController::class, 'index'])->name('index');
    Route::post('/them', [CaLamViecController::class, 'them'])->name('them');
    Route::put('/sua/{id}', [CaLamViecController::class, 'sua'])->name('sua');
    Route::delete('/xoa/{id}', [CaLamViecController::class, 'xoa'])->name('xoa');
});

use App\Http\Controllers\DonHangController; // Nhớ import Controller này

// ... Các route cũ ...

// --- QUẢN LÝ ĐƠN HÀNG (ONLINE SALES) ---
Route::prefix('quan-ly-don-hang')->name('dh.')->group(function () {
    // Trang danh sách đơn hàng
    Route::get('/', [DonHangController::class, 'index'])->name('index');

    // API lấy chi tiết đơn hàng (cho Modal)
    Route::get('/chi-tiet/{id}', [DonHangController::class, 'getDetail'])->name('detail');

    // API cập nhật trạng thái (Duyệt, Giao, Hủy...)
    Route::post('/cap-nhat/{id}', [DonHangController::class, 'updateStatus'])->name('update');
});


use App\Http\Controllers\KhachHangController;

// --- QUẢN LÝ KHÁCH HÀNG ---
Route::prefix('khach-hang')->name('kh.')->group(function () {
    Route::get('/', [KhachHangController::class, 'index'])->name('index');
    Route::post('/them', [KhachHangController::class, 'them'])->name('them');
    Route::put('/sua/{id}', [KhachHangController::class, 'sua'])->name('sua');
    Route::delete('/xoa/{id}', [KhachHangController::class, 'xoa'])->name('xoa');
});

use App\Http\Controllers\Api\ProductCustomerController;
// Public Routes
Route::get('/products', [ProductCustomerController::class, 'index']);
Route::get('/products/{id}', [ProductCustomerController::class, 'show']);
Route::get('/products/{id}/related', [ProductCustomerController::class, 'related']); // Mới
Route::get('/filters', [ProductCustomerController::class, 'filters']); // Mới

// Private Routes (Cần login)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/products/{id}/review', [ProductCustomerController::class, 'review']);
});
use App\Http\Controllers\KhuyenMaiController;

// --- QUẢN LÝ KHUYẾN MÃI ---
Route::prefix('khuyen-mai')->group(function () {
    // Các Route quản lý chính
    Route::get('/', [KhuyenMaiController::class, 'index'])->name('km.index');
    Route::post('/them', [KhuyenMaiController::class, 'them'])->name('km.them');
    Route::put('/sua/{id}', [KhuyenMaiController::class, 'sua'])->name('km.sua');
    Route::delete('/xoa/{id}', [KhuyenMaiController::class, 'xoa'])->name('km.xoa');

    // --- CÁC ROUTE API CHO MODAL SẢN PHẨM ---
    Route::get('/{id}/san-pham', [KhuyenMaiController::class, 'getSanPhams']);
    Route::get('/{id}/tim-san-pham', [KhuyenMaiController::class, 'searchSanPhams']);
    Route::post('/{id}/them-san-pham', [KhuyenMaiController::class, 'themSanPham']);
    Route::delete('/{id}/xoa-san-pham', [KhuyenMaiController::class, 'xoaSanPham']);
});