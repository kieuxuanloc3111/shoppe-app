<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\ShopOrder;
use App\Http\Resources\OrderResource;
use App\Http\Resources\ShopOrderResource;
use App\Services\PaymentService;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /* ===== XEM ĐƠN ===== */

    // buyer: đơn của mình
    public function myOrders()
    {
        $orders = Order::where('buyer_id', auth()->id())
            ->with(['shopOrders.items', 'shopOrders.fee'])
            ->latest('id')
            ->get();

        return response()->json(['response' => 'success', 'data' => OrderResource::collection($orders)]);
    }

    // buyer: chi tiết 1 đơn của mình
    public function show(Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'Không phải đơn của bạn');
        }
        $order->load(['shopOrders.items', 'shopOrders.fee']);

        return response()->json(['response' => 'success', 'data' => new OrderResource($order)]);
    }

    // seller: đơn của shop mình (nhiều buyer)
    public function sellerOrders()
    {
        $shop = auth()->user()->shop;

        $orders = ShopOrder::where('shop_id', $shop->id)
            ->with(['items', 'fee', 'order'])
            ->latest('id')
            ->get();

        return response()->json(['response' => 'success', 'data' => ShopOrderResource::collection($orders)]);
    }

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

        // VNPay: phải thanh toán trước mới cho giao (COD giao trước, trả khi nhận)
        $order = $shopOrder->order;
        if ($order->payment_method === 'vnpay' && $order->payment_status !== 'paid') {
            abort(response()->json([
                'response' => 'error', 'message' => 'Đơn VNPay chưa thanh toán, không thể giao',
            ], 422));
        }

        $this->transition($shopOrder, 'confirmed', 'shipping');
        return $this->ok($shopOrder);
    }

    // seller/shipper xác nhận đã giao. COD: thu tiền lúc này → giữ tiền (escrow hold).
    public function deliver(ShopOrder $shopOrder)
    {
        $this->authorizeSeller($shopOrder);
        $this->transition($shopOrder, 'shipping', 'delivered');

        // COD thu tiền khi giao; VNPay đã hold từ callback
        if ($shopOrder->order->payment_method === 'cod') {
            (new PaymentService())->holdShopOrder($shopOrder);
        }

        return $this->ok($shopOrder);
    }

    /* ===== NGƯỜI MUA ===== */

    // xác nhận đã nhận → completed → nhả tiền pending → available (escrow release)
    public function received(ShopOrder $shopOrder)
    {
        $this->authorizeBuyer($shopOrder);

        if ($shopOrder->status !== 'delivered') {
            abort(response()->json([
                'response' => 'error',
                'message'  => "Chưa thể xác nhận nhận hàng khi đơn đang '{$shopOrder->status}'",
            ], 422));
        }

        DB::transaction(function () use ($shopOrder) {
            $shopOrder->update(['status' => 'completed']);
            (new PaymentService())->releaseShopOrder($shopOrder);
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
            // nếu đã giữ tiền (VNPay trả trước) → hoàn: đảo pending + đánh dấu refunded
            if ($shopOrder->fee) {
                (new PaymentService())->refundShopOrder($shopOrder);
                $shopOrder->order->update(['payment_status' => 'refunded']);
            }

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
