<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = ['cart_id', 'variant_id', 'qty'];

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    // FK chỉ định rõ: model ProductVariant → Eloquent đoán 'product_variant_id', cột là 'variant_id'
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }
}
