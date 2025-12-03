<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuyenMai extends Model
{
    use HasFactory;

    protected $table = 'khuyenmai';
    protected $primaryKey = 'MAKM';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'MAKM', 'TENKM', 'MA_CODE', 'LOAIKM', 'GIATRI', 'GIAM_TOI_DA', 
        'DON_TOI_THIEU', 'SOLUONG_MA', 'NGAYBATDAU', 'NGAYKETTHUC', 
        'TRANGTHAI', 'MOTA', 'NGAYTAO'
    ];

    protected $casts = [
        'NGAYBATDAU' => 'datetime',
        'NGAYKETTHUC' => 'datetime',
        'NGAYTAO' => 'datetime',
        'GIATRI' => 'integer',
        'GIAM_TOI_DA' => 'integer',
        'DON_TOI_THIEU' => 'integer',
    ];

    // Tự động sinh mã KM
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MAKM)) {
                $prefix = 'KM';
                $maxCode = static::where('MAKM', 'like', $prefix . '%')
                    ->selectRaw('MAX(CAST(SUBSTRING(MAKM, 3) AS UNSIGNED)) as max_id')
                    ->value('max_id');
                $nextId = $maxCode ? $maxCode + 1 : 1;
                $model->MAKM = $prefix . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }
            $model->NGAYTAO = now();
        });
    }

    // 👇 QUAN TRỌNG: Khuyến mãi áp dụng cho nhiều SẢN PHẨM (chứ không phải khuyenMais)
    public function sanPhams()
    {
        return $this->belongsToMany(SanPham::class, 'khuyenmai_sanpham', 'MAKM', 'MASP');
    }
}