<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DanhGia extends Model
{
    protected $table = 'danhgia';
    public $timestamps = false; // Tự quản lý ngày giờ

    protected $fillable = [
        'MASP', 'MAKH', 'DIEM', 'NOIDUNG', 'NGAYDANHGIA'
    ];

    // Quan hệ ngược lại: Đánh giá thuộc về 1 Khách hàng
    public function khachHang() {
        return $this->belongsTo(KhachHang::class, 'MAKH', 'MAKH');
    }

    // Đánh giá thuộc về 1 Sản phẩm
    public function sanPham() {
        return $this->belongsTo(SanPham::class, 'MASP', 'MASP');
    }
}