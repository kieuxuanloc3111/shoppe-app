<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'buyer_id', 'receiver_name', 'receiver_phone', 'receiver_address',
        'grand_total', 'payment_status', 'payment_method', 'placed_at',
    ];

    protected $casts = [
        'grand_total' => 'decimal:2',
        'placed_at'   => 'datetime',
    ];

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }

    public function shopOrders()
    {
        return $this->hasMany(ShopOrder::class);
    }
}
