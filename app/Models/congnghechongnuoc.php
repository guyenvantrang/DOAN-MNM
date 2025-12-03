<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CongNgheChongNuoc extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'congnghe_chongnuoc';
    protected $primaryKey = 'MCN';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MCN', 'TEN', 'MOTA', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MCN', 'MCN');
    }
    protected static function booted()
    {
        static::creating(function ($congnghe) {
            if (empty($congnghe->MCN)) {
                $congnghe->MCN = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'CN';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MCN', $code)->exists());
        return $code;
    }

}
