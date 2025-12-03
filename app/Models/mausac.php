<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MauSac extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'mausac';
    protected $primaryKey = 'MMS';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MMS', 'TENMAU', 'MOTA', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MMS', 'MMS');
    }
    protected static function booted()
    {
        static::creating(function ($mausac) {
            if (empty($mausac->MMS)) {
                $mausac->MMS = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'MS';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MMS', $code)->exists());
        return $code;
    }

}
