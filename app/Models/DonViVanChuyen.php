<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonViVanChuyen extends Model
{
    use HasFactory;
    protected $table = 'DONVIVANCHUYEN';
    protected $primaryKey = 'MADVVC';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['MADVVC', 'TENDVVC', 'HOTLINE', 'DIACHI'];

    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MADVVC)) {
                $model->MADVVC = 'DVVC' . rand(10, 99);
            }
        });
    }
}