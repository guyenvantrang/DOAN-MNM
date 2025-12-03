<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class NhanVien extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'NHANVIEN';
    protected $primaryKey = 'MANV';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    // Map timestamps cột custom nếu muốn dùng tính năng auto date của Laravel
    const CREATED_AT = 'NGAYTAO';
    const UPDATED_AT = null; // Bảng này không có NGAYSUA theo thiết kế, hoặc bạn có thể thêm

    protected $fillable = [
        'MANV', 'MACV', 'HO', 'TEN', 'EMAIL', 'MATKHAU', 
        'SDT', 'DIACHI', 'NGAYVAOLAM', 'TRANGTHAI', 'NGAYTAO'
    ];

    protected $hidden = ['MATKHAU']; // Ẩn mật khẩu khi return JSON

    public function getAuthPassword()
    {
        return $this->MATKHAU;
    }

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MANV)) {
                $model->MANV = self::generateCode();
            }
        });
    }

    private static function generateCode()
    {
        $prefix = 'NV';
        do {
            $code = $prefix . rand(1000, 9999); // 4 số cho nhân viên
        } while (self::where('MANV', $code)->exists());
        return $code;
    }

    // Relationships
    public function chucVu() {
        return $this->belongsTo(ChucVu::class, 'MACV', 'MACV');
    }

    public function donHangDuyet() {
        return $this->hasMany(DonHang::class, 'MANV_DUYET', 'MANV');
    }
}