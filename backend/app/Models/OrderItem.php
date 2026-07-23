<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'shop_order_id', 'product_id', 'variant_id', 'name', 'unit_price', 'qty', 'line_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];

    public function shopOrder()
    {
        return $this->belongsTo(ShopOrder::class);
    }

    // FK chỉ định rõ (model Products số nhiều / ProductVariant)
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
