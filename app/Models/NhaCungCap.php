<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NhaCungCap extends Model
{
    use HasFactory;
    protected $table = 'NHACUNGCAP';
    protected $primaryKey = 'MANCC';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = ['MANCC', 'TENNCC', 'DIACHI', 'SDT', 'EMAIL', 'NGAYTAO'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MANCC)) {
                $model->MANCC = 'NCC' . rand(100, 999);
            }
        });
    }
}