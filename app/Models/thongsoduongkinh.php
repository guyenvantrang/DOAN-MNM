<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongSoDuongKinh extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'THONGSO_DUONGKINH';
    protected $primaryKey = 'MADK';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['MADK', 'MOTA', 'CHISO', 'DONVIDO', 'NGAYTAO'];

    public function sanPham()
    {
        return $this->hasMany(SanPham::class, 'MADK', 'MADK');
    }
    protected static function booted()
{
    static::creating(function ($duongkinh) {
        if (empty($duongkinh->MADK)) {
            $duongkinh->MADK = self::generateCode();
        }
    });
}

private static function generateCode()
{
    $prefix = 'DK'; // DK = Đường Kính
    do {
        $code = $prefix . rand(10, 99);
    } while (self::where('MADK', $code)->exists());
    return $code;
}

}
