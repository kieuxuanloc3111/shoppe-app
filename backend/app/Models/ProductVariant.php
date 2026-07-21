<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ['product_id', 'sku', 'price', 'stock', 'option_values'];

    protected $casts = [
        'option_values' => 'array',
        'price'         => 'decimal:2',
    ];

    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}
