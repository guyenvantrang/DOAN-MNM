<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongSoDoDay extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'THONGSO_DODAY';
    protected $primaryKey = 'MADDY';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MADDY', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MADDY', 'MADDY');
    }
     protected static function booted()
    {
        static::creating(function ($dodday) {
            if (empty($dodday->MADDY)) {
                $dodday->MADDY = self::generateCode();
            }
        });
    }
    private static function generateCode()
    {
        $prefix = 'DD';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MADDY', $code)->exists());
        return $code;
    }
}
