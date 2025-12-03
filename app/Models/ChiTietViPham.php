<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class ChiTietViPham extends Model
{
    protected $table = 'chitiet_vipham';
    public $timestamps = false;
    protected $fillable = ['MALICH', 'NOIDUNG', 'SOTIEN'];
}