<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SellerWallet extends Model
{
    protected $fillable = ['shop_id', 'available', 'pending'];

    protected $casts = [
        'available' => 'decimal:2',
        'pending'   => 'decimal:2',
    ];

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function ledger()
    {
        return $this->hasMany(WalletLedger::class, 'shop_id', 'shop_id');
    }
}
