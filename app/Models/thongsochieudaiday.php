<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongSoChieuDaiDay extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'THONGSO_CHIEUDAIDAY';
    protected $primaryKey = 'MADD';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MADD', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MADD', 'MADD');
    }
    protected static function booted()
    {
        static::creating(function ($chieudaiday) {
            if (empty($chieudaiday->MADD)) {
                $chieudaiday->MADD = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'CDD';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MADD', $code)->exists());
        return $code;
    }

}
