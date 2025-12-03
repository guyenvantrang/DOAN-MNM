<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use HasFactory;

    protected $table = 'CHUCVU';
    protected $primaryKey = 'MACV';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false; // Tự quản lý NGAYTAO

    protected $fillable = ['MACV', 'TENCV', 'QUYENHAN', 'NGAYTAO'];

    // Auto generate code
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MACV)) {
                $model->MACV = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'CV';
        do {
            $code = $prefix . rand(10, 99);
        } while (self::where('MACV', $code)->exists());
        return $code;
    }

    public function nhanVien()
    {
        return $this->hasMany(NhanVien::class, 'MACV', 'MACV');
    }
}