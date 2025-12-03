<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonHang extends Model
{
    use HasFactory;
    protected $table = 'DONHANG';
    protected $primaryKey = 'MADH';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const CREATED_AT = 'NGAYDAT';
    const UPDATED_AT = 'NGAYCAPNHAT';

    protected $fillable = [
        'MADH', 'MAKH', 'MANV_DUYET', 'MAKM',
        'TEN_NGUOINHAN', 'SDT_NGUOINHAN', 'DIACHI_GIAOHANG',
        'TONGTIENHANG', 'PHIVANCHUYEN', 'GIAMGIA', 'TONGTHANHTOAN',
        'PT_THANHTOAN', 'TRANGTHAI_THANHTOAN', 'TRANGTHAI_DONHANG',
        'YEU_CAU_HUY', 'LYDO_HUY', 'GHICHU', 'NGAYDAT', 'NGAYCAPNHAT'
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MADH)) {
                // Tạo mã đơn hàng dạng: DH + timestamp + random (VD: DH170624999)
                $model->MADH = 'DH' . time() . rand(10, 99); 
            }
        });
    }

    // Relationships
    public function chiTiet() {
        return $this->hasMany(ChiTietDonHang::class, 'MADH', 'MADH');
    }

    public function khachHang() {
        return $this->belongsTo(KhachHang::class, 'MAKH', 'MAKH');
    }

    public function khuyenMai() {
        return $this->belongsTo(KhuyenMai::class, 'MAKM', 'MAKM');
    }
    
    public function giaoHang() {
        return $this->hasOne(GiaoHang::class, 'MADH', 'MADH');
    }
}