<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongSoKhoiLuong extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'THONGSO_KHOILUONG';
    protected $primaryKey = 'MKL';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MKL', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MKL', 'MKL');
    }
    protected static function booted()
    {
        static::creating(function ($khoiluong) {
            if (empty($khoiluong->MKL)) {
                $khoiluong->MKL = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'KL'; // prefix cho khối lượng
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MKL', $code)->exists());

        return $code;
    }

}
