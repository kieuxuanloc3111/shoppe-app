<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopOrderFee extends Model
{
    protected $fillable = [
        'shop_order_id', 'commission', 'payment_fee', 'tech_fee', 'infra_fee',
        'platform_total', 'seller_earning',
    ];

    protected $casts = [
        'commission'     => 'decimal:2',
        'payment_fee'    => 'decimal:2',
        'tech_fee'       => 'decimal:2',
        'infra_fee'      => 'decimal:2',
        'platform_total' => 'decimal:2',
        'seller_earning' => 'decimal:2',
    ];

    public function shopOrder()
    {
        return $this->belongsTo(ShopOrder::class);
    }
}
