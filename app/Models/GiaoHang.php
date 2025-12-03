<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiaoHang extends Model
{
    use HasFactory;
    protected $table = 'GIAOHANG';
    protected $primaryKey = 'MAGIAOVAN'; // Mã vận đơn
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MAGIAOVAN', 'MADH', 'MADVVC', 'SHIPPER_TEN', 'SHIPPER_SDT',
        'TRANGTHAI_GIAO', 'MOTA_SUCO', 'NGAYGIAO', 'NGAYHOANTAT'
    ];
    

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MAGIAOVAN)) {
                $model->MAGIAOVAN = 'VD' . rand(10000, 99999);
            }
        });
    }

    public function donHang() {
        return $this->belongsTo(DonHang::class, 'MADH', 'MADH');
    }
}