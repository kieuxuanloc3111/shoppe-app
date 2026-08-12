<?php

namespace App\Services;

use App\Models\SellerWallet;
use App\Models\WalletLedger;
use Illuminate\Support\Facades\DB;

/**
 * Ví người bán + sổ cái bất biến.
 * Mỗi thay đổi số dư ghi 1 dòng wallet_ledger kèm balance_after (đối soát được).
 * P1 dùng `available` trực tiếp; `pending` (escrow giữ tiền) để dành P2.
 */
class WalletService
{
    public function wallet(int $shopId): SellerWallet
    {
        return SellerWallet::firstOrCreate(['shop_id' => $shopId]);
    }

    /**
     * Ghi 1 bút toán vào available. amount > 0 = cộng, < 0 = trừ.
     * Khóa ví (lockForUpdate) chống race khi cộng tiền đồng thời.
     */
    public function apply(int $shopId, string $type, float $amount, ?string $refType = null, ?int $refId = null): WalletLedger
    {
        $this->wallet($shopId); // đảm bảo ví tồn tại

        return DB::transaction(function () use ($shopId, $type, $amount, $refType, $refId) {
            $wallet = SellerWallet::where('shop_id', $shopId)->lockForUpdate()->first();

            $wallet->available = round((float) $wallet->available + $amount, 2);
            $wallet->save();

            return WalletLedger::create([
                'shop_id'       => $shopId,
                'type'          => $type,
                'amount'        => round($amount, 2),
                'ref_type'      => $refType,
                'ref_id'        => $refId,
                'balance_after' => $wallet->available,
            ]);
        });
    }

    // escrow: giữ tiền vào `pending` (khi đơn paid, chưa hoàn tất)
    public function hold(int $shopId, float $amount, ?string $refType = null, ?int $refId = null): WalletLedger
    {
        $this->wallet($shopId);

        return DB::transaction(function () use ($shopId, $amount, $refType, $refId) {
            $wallet = SellerWallet::where('shop_id', $shopId)->lockForUpdate()->first();
            $wallet->pending = round((float) $wallet->pending + $amount, 2);
            $wallet->save();

            return WalletLedger::create([
                'shop_id' => $shopId, 'type' => 'hold', 'amount' => round($amount, 2),
                'ref_type' => $refType, 'ref_id' => $refId, 'balance_after' => $wallet->available,
            ]);
        });
    }

    // escrow: nhả pending → available (khi đơn completed)
    public function release(int $shopId, float $amount, ?string $refType = null, ?int $refId = null): WalletLedger
    {
        $this->wallet($shopId);

        return DB::transaction(function () use ($shopId, $amount, $refType, $refId) {
            $wallet = SellerWallet::where('shop_id', $shopId)->lockForUpdate()->first();
            $wallet->pending   = round((float) $wallet->pending - $amount, 2);
            $wallet->available = round((float) $wallet->available + $amount, 2);
            $wallet->save();

            return WalletLedger::create([
                'shop_id' => $shopId, 'type' => 'sale_earning', 'amount' => round($amount, 2),
                'ref_type' => $refType, 'ref_id' => $refId, 'balance_after' => $wallet->available,
            ]);
        });
    }

    // escrow: hoàn tiền — rút khỏi pending (đảo hold khi hủy đơn đã paid)
    public function refundHold(int $shopId, float $amount, ?string $refType = null, ?int $refId = null): WalletLedger
    {
        $this->wallet($shopId);

        return DB::transaction(function () use ($shopId, $amount, $refType, $refId) {
            $wallet = SellerWallet::where('shop_id', $shopId)->lockForUpdate()->first();
            $wallet->pending = round((float) $wallet->pending - $amount, 2);
            $wallet->save();

            return WalletLedger::create([
                'shop_id' => $shopId, 'type' => 'refund', 'amount' => round(-$amount, 2),
                'ref_type' => $refType, 'ref_id' => $refId, 'balance_after' => $wallet->available,
            ]);
        });
    }

    // tiện ích: cộng thẳng available (dùng cho COD trước đây — giờ COD cũng escrow, xem PaymentService)
    public function creditSale(int $shopId, float $amount, int $shopOrderId): WalletLedger
    {
        return $this->apply($shopId, 'sale_earning', $amount, 'shop_order', $shopOrderId);
    }

    // tiện ích: trừ tiền khi rút (payout)
    public function debitPayout(int $shopId, float $amount, int $payoutId): WalletLedger
    {
        return $this->apply($shopId, 'payout', -abs($amount), 'payout', $payoutId);
    }
}
