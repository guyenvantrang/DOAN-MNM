<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaLamViec extends Model
{
    use HasFactory;
    protected $table = 'CALAMVIEC';
    protected $primaryKey = 'MACA';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['MACA', 'TENCA', 'GIOBATDAU', 'GIOKETTHUC'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MACA)) {
                $model->MACA = 'CA' . rand(10, 99);
            }
        });
    }
}