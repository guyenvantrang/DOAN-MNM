<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SanPham extends Model
{
    use HasFactory;

    // 1. CẤU HÌNH BẢNG
    protected $table = 'sanpham';
    protected $primaryKey = 'MASP';
    public $incrementing = false; // Khóa chính là chuỗi (SP000001)
    protected $keyType = 'string';

    // Cấu hình Timestamps
    const CREATED_AT = 'NGAYTAO';
    const UPDATED_AT = 'NGAYSUA';

    // 2. KHAI BÁO CÁC CỘT
    protected $fillable = [
        'MASP',
        'TENSP',
        'MATHUONGHIEU',
        'MALOAI',
        'GIABAN',
        'GIANHAP',
        'SOLUONGTON',
        'HINHANHCHINH',
        'CHITIETHINHANH', // Lưu JSON mảng ảnh
        'MADK',
        'MADD',
        'MADDY',
        'MCRD',
        'MKL',
        'MCN',
        'MMS',
        'MCNANG',
        'MOTA',
        'TRANGTHAI'
    ];

    // 3. ÉP KIỂU DỮ LIỆU
    protected $casts = [
        'CHITIETHINHANH' => 'array', // Tự động chuyển JSON <-> Array
        'GIABAN' => 'integer',
        'GIANHAP' => 'integer',
        'SOLUONGTON' => 'integer',
        'TRANGTHAI' => 'integer',
        'NGAYTAO' => 'datetime',
        'NGAYSUA' => 'datetime',
    ];

    // Tự động thêm thuộc tính ảo này vào JSON khi query
    protected $appends = ['gia_ban_hien_tai'];

    /**
     * 🚀 TỰ ĐỘNG TẠO MÃ SẢN PHẨM (SP000001)
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            if (empty($model->MASP)) {
                $prefix = 'SP';
                // Tìm số lớn nhất hiện tại
                $maxCode = static::where('MASP', 'like', $prefix . '%')
                    ->selectRaw('MAX(CAST(SUBSTRING(MASP, 3) AS UNSIGNED)) as max_id')
                    ->value('max_id');

                $nextId = $maxCode ? $maxCode + 1 : 1;
                // Tạo mã SP + 6 số đệm
                $model->MASP = $prefix . str_pad($nextId, 6, '0', STR_PAD_LEFT);
            }
        });
    }

    /* |--------------------------------------------------------------------------
    | RELATIONSHIPS (CÁC MỐI QUAN HỆ)
    |-------------------------------------------------------------------------- */

    public function thuongHieu()
    {
        return $this->belongsTo(ThuongHieu::class, 'MATHUONGHIEU', 'MATHUONGHIEU');
    }

    public function loaiSP()
    {
        return $this->belongsTo(LoaiSP::class, 'MALOAI', 'MALOAI');
    }

    // Các thông số kỹ thuật
    public function duongKinh()
    {
        return $this->belongsTo(ThongSoDuongKinh::class, 'MADK', 'MADK');
    }
    public function chieuDaiDay()
    {
        return $this->belongsTo(ThongSoChieuDaiDay::class, 'MADD', 'MADD');
    }
    public function doDay()
    {
        return $this->belongsTo(ThongSoDoDay::class, 'MADDY', 'MADDY');
    }
    public function chieuRongDay()
    {
        return $this->belongsTo(ThongSoChieuRongDay::class, 'MCRD', 'MCRD');
    }
    public function khoiLuong()
    {
        return $this->belongsTo(ThongSoKhoiLuong::class, 'MKL', 'MKL');
    }
    public function congNgheChongNuoc()
    {
        return $this->belongsTo(CongNgheChongNuoc::class, 'MCN', 'MCN');
    }
    public function mauSac()
    {
        return $this->belongsTo(MauSac::class, 'MMS', 'MMS');
    }
    public function chucNang()
    {
        return $this->belongsTo(CacChucNang::class, 'MCNANG', 'MCNANG');
    }

    // Quan hệ với Đánh giá (1 SP có nhiều đánh giá)
    public function danhGia()
    {
        return $this->hasMany(DanhGia::class, 'MASP', 'MASP')->orderBy('NGAYDANHGIA', 'desc');
    }

    // Quan hệ với Khuyến mãi (Nhiều - Nhiều)
    public function khuyenMais()
    {
        return $this->belongsToMany(KhuyenMai::class, 'khuyenmai_sanpham', 'MASP', 'MAKM');
    }

    /* |--------------------------------------------------------------------------
    | ACCESSORS (LOGIC TÍNH TOÁN ẢO)
    |-------------------------------------------------------------------------- */

    /**
     * Tính giá bán thực tế (đã trừ khuyến mãi đang chạy)
     * Gọi bằng: $product->gia_ban_hien_tai
     */
    // 👇 LOGIC TÍNH GIÁ (Phải nằm ở đây)
    public function getGiaBanHienTaiAttribute()
    {
        // Lấy khuyến mãi ĐANG CHẠY
        $activePromo = $this->khuyenMais()
            ->where('TRANGTHAI', 1)
            ->whereNull('MA_CODE') // Loại giảm trực tiếp
            ->where('NGAYBATDAU', '<=', now())
            ->where('NGAYKETTHUC', '>=', now())
            ->orderBy('GIATRI', 'desc')
            ->first();

        if ($activePromo) {
            if ($activePromo->LOAIKM == 'PHAN_TRAM') {
                $giam = $this->GIABAN * ($activePromo->GIATRI / 100);
                if ($activePromo->GIAM_TOI_DA && $giam > $activePromo->GIAM_TOI_DA) {
                    $giam = $activePromo->GIAM_TOI_DA;
                }
                return (int) max(0, $this->GIABAN - $giam);
            } else {
                return (int) max(0, $this->GIABAN - $activePromo->GIATRI);
            }
        }

        return (int) $this->GIABAN;
    }


}