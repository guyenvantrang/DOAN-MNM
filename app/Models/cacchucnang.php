<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CacChucNang extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'CACCHUCNANG';
    protected $primaryKey = 'MCNANG';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MCNANG', 'TENCHUCNANG', 'MOTA', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MCNANG', 'MCNANG');
    }
    protected static function booted()
    {
        static::creating(function ($chucnang) {
            if (empty($chucnang->MCNANG)) {
                $chucnang->MCNANG = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'CN'; // CN = Chức năng
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MCNANG', $code)->exists());
        return $code;
    }

}
