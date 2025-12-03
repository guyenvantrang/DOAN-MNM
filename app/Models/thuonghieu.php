<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class thuonghieu extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'thuonghieu';
    protected $primaryKey = 'MATHUONGHIEU';
    public $incrementing = false; // Vì MATHUONGHIEU là varchar
    protected $keyType = 'string';

    protected $fillable = [
        'MATHUONGHIEU',
        'TENTHUONGHIEU',
        'QUOCGIA',
        'MOTA'
    ];

    // 🔗 Một thương hiệu có nhiều sản phẩm
    public function sanPhams()
    {
        return $this->hasMany(SanPham::class, 'MATHUONGHIEU', 'MATHUONGHIEU');
    }
    protected static function booted()
    {
        static::creating(function ($thuonghieu) {
            if (empty($thuonghieu->MATHUONGHIEU)) {
                $thuonghieu->MATHUONGHIEU = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'TH';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MATHUONGHIEU', $code)->exists());
        return $code;
    }
}
