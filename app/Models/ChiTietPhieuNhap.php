<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietPhieuNhap extends Model
{
    use HasFactory;
    protected $table = 'CHITIETPHIEUNHAP';
    public $timestamps = false;
    // Bảng này khóa chính phức hợp (MAPN, MASP), Laravel không hỗ trợ native tốt
    // nên ta set incrementing = false và primaryKey là null hoặc 1 cột đại diện
    public $incrementing = false;
    protected $primaryKey = null; 

    protected $fillable = ['MAPN', 'MASP', 'SOLUONG', 'GIANHAP', 'THANHTIEN'];

    public function sanPham() {
        return $this->belongsTo(SanPham::class, 'MASP', 'MASP');
    }
}