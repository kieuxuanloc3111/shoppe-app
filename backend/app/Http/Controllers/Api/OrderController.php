<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductVariant;
use App\Models\ShopOrder;
use App\Services\FeeCalculator;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /* ===== NGƯỜI BÁN ===== */

    public function confirm(ShopOrder $shopOrder)
    {
        $this->authorizeSeller($shopOrder);
        $this->transition($shopOrder, 'pending', 'confirmed');
        return $this->ok($shopOrder);
    }

    public function ship(ShopOrder $shopOrder)
    {
        $this->authorizeSeller($shopOrder);
        $this->transition($shopOrder, 'confirmed', 'shipping');
        return $this->ok($shopOrder);
    }

    /* ===== NGƯỜI MUA ===== */

    // xác nhận đã nhận → completed → bóc phí + cộng ví người bán (escrow release)
    public function received(ShopOrder $shopOrder)
    {
        $this->authorizeBuyer($shopOrder);

        if ($shopOrder->status !== 'shipping') {
            abort(response()->json([
                'response' => 'error',
                'message'  => "Không thể xác nhận nhận hàng khi đơn đang '{$shopOrder->status}'",
            ], 422));
        }

        DB::transaction(function () use ($shopOrder) {
            $shopOrder->update(['status' => 'completed']);
            $fee = (new FeeCalculator())->calculate($shopOrder);
            (new WalletService())->creditSale(
                $shopOrder->shop_id,
                (float) $fee->seller_earning,
                $shopOrder->id
            );
        });

        return $this->ok($shopOrder);
    }

    // hủy khi chưa giao → hoàn kho
    public function cancel(ShopOrder $shopOrder)
    {
        $this->authorizeBuyer($shopOrder);

        if (!in_array($shopOrder->status, ['pending', 'confirmed'])) {
            abort(response()->json([
                'response' => 'error',
                'message'  => 'Không thể hủy đơn đã giao/hoàn tất',
            ], 422));
        }

        DB::transaction(function () use ($shopOrder) {
            foreach ($shopOrder->items as $item) {
                if ($item->variant_id) {
                    ProductVariant::where('id', $item->variant_id)->increment('stock', $item->qty);
                }
            }
            $shopOrder->update(['status' => 'cancelled']);
        });

        return $this->ok($shopOrder);
    }

    /* ===== helper ===== */

    private function transition(ShopOrder $so, string $from, string $to): void
    {
        if ($so->status !== $from) {
            abort(response()->json([
                'response' => 'error',
                'message'  => "Không thể chuyển từ '{$so->status}' sang '{$to}'",
            ], 422));
        }
        $so->update(['status' => $to]);
    }

    private function authorizeSeller(ShopOrder $so): void
    {
        if ($so->shop->user_id !== auth()->id()) {
            abort(403, 'Không phải đơn của shop bạn');
        }
    }

    private function authorizeBuyer(ShopOrder $so): void
    {
        if ($so->order->buyer_id !== auth()->id()) {
            abort(403, 'Không phải đơn của bạn');
        }
    }

    private function ok(ShopOrder $so)
    {
        return response()->json([
            'response' => 'success',
            'data'     => $so->fresh()->load('items', 'fee'),
        ]);
    }
}
