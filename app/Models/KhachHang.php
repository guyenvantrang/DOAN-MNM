<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // ✅ Bắt buộc cho API

class KhachHang extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. CẤU HÌNH BẢNG
    protected $table = 'khachhang';
    protected $primaryKey = 'MAKH'; // Khóa chính tự tăng
    
    // Tự động quản lý NGAYTAO, NGAYSUA
    public $timestamps = true;
    const CREATED_AT = 'NGAYTAO';
    const UPDATED_AT = 'NGAYSUA';

    // 2. CÁC CỘT ĐƯỢC PHÉP GÁN DỮ LIỆU
    protected $fillable = [
        'HOTEN', 
        'EMAIL', 
        'SDT', 
        'MATKHAU', 
        'DIACHI', 
        'TRANGTHAI', 
        // Không cần khai báo NGAYTAO/NGAYSUA ở đây
    ];

    // 3. ẨN DỮ LIỆU NHẠY CẢM KHI TRẢ VỀ API
    protected $hidden = [
        'MATKHAU', 
        'remember_token',
    ];

    // 4. ÉP KIỂU DỮ LIỆU
    protected $casts = [
        'TRANGTHAI' => 'integer',
        'NGAYTAO' => 'datetime',
        'NGAYSUA' => 'datetime',
        'MATKHAU' => 'hashed', // Tự động hash password (Laravel 10+)
        'NGAYTAO',
        'NGAYSUA'
    ];

    // 5. CẤU HÌNH MẬT KHẨU CHO AUTHENTICATION
    public function getAuthPassword() {
        return $this->MATKHAU;
    }

    /* |--------------------------------------------------------------------------
    | RELATIONSHIPS (CÁC MỐI QUAN HỆ)
    |--------------------------------------------------------------------------
    */

    // Một khách hàng có nhiều Đơn hàng
    public function donHang() {
        return $this->hasMany(DonHang::class, 'MAKH', 'MAKH');
    }

    // Một khách hàng có nhiều món trong Giỏ hàng
    public function gioHang() {
        return $this->hasMany(GioHang::class, 'MAKH', 'MAKH');
    }

    // ✅ MỚI: Một khách hàng có nhiều Đánh giá/Phản hồi
    public function danhGia() {
        return $this->hasMany(DanhGia::class, 'MAKH', 'MAKH');
    }
}