<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LichLamViec extends Model
{
    use HasFactory;
    protected $table = 'LICHLAMVIEC';
    protected $primaryKey = 'MALICH'; // INT Auto increment
    public $timestamps = false;

    protected $fillable = ['MANV', 'MACA', 'NGAYLAM', 'GHICHU', 'TRANGTHAI'];

    public function nhanVien()
    {
        return $this->belongsTo(NhanVien::class, 'MANV', 'MANV');
    }
    public function viPhams()
    {
        return $this->hasMany(ChiTietViPham::class, 'MALICH', 'MALICH');
    }
    public function caLamViec()
    {
        return $this->belongsTo(CaLamViec::class, 'MACA', 'MACA');
    }
}