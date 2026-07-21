<?php

namespace App\Services;

use App\Models\FeeSetting;
use App\Models\ShopOrder;
use App\Models\ShopOrderFee;

/**
 * Bóc phí sàn cho 1 shop_order (mô hình escrow).
 * commission: theo % ngành hàng (category.commission_rate) từng item.
 * payment/tech: % trên tổng; infra: tiền cố định/đơn.
 * seller_earning = gross - tổng phí sàn.
 */
class FeeCalculator
{
    public function calculate(ShopOrder $shopOrder): ShopOrderFee
    {
        $shopOrder->loadMissing('items.product.category');
        $settings = FeeSetting::current();

        $gross = (float) $shopOrder->subtotal;

        // hoa hồng: mỗi item theo ngành hàng của nó
        $commission = 0;
        foreach ($shopOrder->items as $item) {
            $rate = (float) ($item->product?->category?->commission_rate ?? 0);
            $commission += (float) $item->line_total * $rate / 100;
        }

        $paymentFee = $gross * (float) $settings->payment_fee_rate / 100;
        $techFee    = $gross * (float) $settings->tech_fee_rate / 100;
        $infraFee   = (float) $settings->infra_fee_amount;

        $platformTotal = $commission + $paymentFee + $techFee + $infraFee;
        $sellerEarning = $gross - $platformTotal;

        return ShopOrderFee::updateOrCreate(
            ['shop_order_id' => $shopOrder->id],
            [
                'commission'     => round($commission, 2),
                'payment_fee'    => round($paymentFee, 2),
                'tech_fee'       => round($techFee, 2),
                'infra_fee'      => round($infraFee, 2),
                'platform_total' => round($platformTotal, 2),
                'seller_earning' => round($sellerEarning, 2),
            ]
        );
    }
}
