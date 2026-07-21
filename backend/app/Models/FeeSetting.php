<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeSetting extends Model
{
    protected $fillable = ['payment_fee_rate', 'tech_fee_rate', 'infra_fee_amount'];

    protected $casts = [
        'payment_fee_rate' => 'decimal:2',
        'tech_fee_rate'    => 'decimal:2',
        'infra_fee_amount' => 'decimal:2',
    ];

    // luôn lấy dòng cấu hình hiện hành (tạo mặc định theo Shopee VN nếu chưa có)
    public static function current(): self
    {
        return static::firstOrCreate([], [
            'payment_fee_rate' => 5,    // %
            'tech_fee_rate'    => 5,    // %
            'infra_fee_amount' => 3000, // đ/đơn
        ]);
    }
}
