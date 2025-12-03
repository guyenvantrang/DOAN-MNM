<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongSoChieuRongDay extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'THONGSO_CHIEURONGDAY';
    protected $primaryKey = 'MCRD';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MCRD', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MCRD', 'MCRD');
    }
    protected static function booted()
    {
        static::creating(function ($chieurongday) {
            if (empty($chieurongday->MCRD)) {
                $chieurongday->MCRD = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'CRD'; // prefix mới cho "Chiều Rộng Dây"
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MCRD', $code)->exists());
        return $code;
    }

}
