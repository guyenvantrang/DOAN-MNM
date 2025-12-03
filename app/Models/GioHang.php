<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioHang extends Model
{
    use HasFactory;
    protected $table = 'GIOHANG';
    public $incrementing = false;
    protected $primaryKey = null; // Khóa phức hợp (MAKH, MASP)
    public $timestamps = false;

    protected $fillable = ['MAKH', 'MASP', 'SOLUONG', 'NGAYTHEM'];
    public function giaoHang()
    {
        return $this->hasOne(GiaoHang::class, 'MADH', 'MADH');
    }
    public function sanPham()
    {
        return $this->belongsTo(SanPham::class, 'MASP', 'MASP');
    }
}