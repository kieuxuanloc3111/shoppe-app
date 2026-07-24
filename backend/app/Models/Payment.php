<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = ['order_id', 'gateway', 'amount', 'gateway_txn_id', 'status', 'raw'];

    protected $casts = [
        'amount' => 'decimal:2',
        'raw'    => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
