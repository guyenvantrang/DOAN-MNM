<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChiTietDonHang extends Model
{
    use HasFactory;
    protected $table = 'CHITIETDONHANG';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = ['MADH', 'MASP', 'SOLUONG', 'DONGIA', 'THANHTIEN'];

    public function sanPham() {
        return $this->belongsTo(SanPham::class, 'MASP', 'MASP');
    }
}