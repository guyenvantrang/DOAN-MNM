<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhieuNhap extends Model
{
    use HasFactory;
    protected $table = 'PHIEUNHAP';
    protected $primaryKey = 'MAPN';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['MAPN', 'MANV', 'MANCC', 'TONGTIEN', 'GHICHU', 'NGAYNHAP'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MAPN)) {
                $model->MAPN = 'PN' . date('dmY') . rand(10, 99); // Ví dụ: PN0112202499
            }
        });
    }

    public function chiTiet() {
        return $this->hasMany(ChiTietPhieuNhap::class, 'MAPN', 'MAPN');
    }

    public function nhanVien() {
        return $this->belongsTo(NhanVien::class, 'MANV', 'MANV');
    }
}