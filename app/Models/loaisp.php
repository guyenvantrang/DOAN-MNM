<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiSP extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'loai_sp';
    protected $primaryKey = 'MALOAI';
    public $incrementing = false; // Vì MALOAI là varchar
    protected $keyType = 'string';

    protected $fillable = [
        'MALOAI',
        'TENLOAI',
        'MOTA'
    ];

    // 🔗 Một loại sản phẩm có nhiều sản phẩm
    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'MALOAI', 'MALOAI');
    }
    protected static function booted()
    {
        static::creating(function ($loai) {
            if (empty($loai->MALOAI)) {
                $loai->MALOAI = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'LO';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MALOAI', $code)->exists());
        return $code;
    }
}
