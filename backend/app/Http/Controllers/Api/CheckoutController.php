<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\ShopOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        $data = $request->validate([
            'receiver_name'    => 'required|string|max:255',
            'receiver_phone'   => 'required|string|max:20',
            'receiver_address' => 'required|string|max:500',
            'payment_method'   => 'required|in:cod,vnpay',
        ]);

        $userId = auth()->id();

        $cart = Cart::with('items')->where('user_id', $userId)->first();
        if (!$cart || $cart->items->isEmpty()) {
            return response()->json(['response' => 'error', 'message' => 'Giỏ hàng trống'], 400);
        }

        $order = DB::transaction(function () use ($cart, $data, $userId) {

            // gom theo shop; khóa variant, kiểm + trừ kho (chống oversell khi đặt đồng thời)
            $byShop = [];
            foreach ($cart->items as $ci) {
                $variant = ProductVariant::whereKey($ci->variant_id)->lockForUpdate()->first();

                if (!$variant) {
                    abort(response()->json([
                        'response' => 'error', 'message' => 'Sản phẩm không còn tồn tại',
                    ], 422));
                }
                if ($variant->stock < $ci->qty) {
                    abort(response()->json([
                        'response' => 'error',
                        'message'  => 'Sản phẩm "' . $variant->product->name . '" không đủ tồn kho (còn ' . $variant->stock . ')',
                    ], 422));
                }

                $variant->decrement('stock', $ci->qty);
                $byShop[$variant->product->shop_id][] = [$variant, $ci->qty, $variant->product];
            }

            $order = Order::create([
                'buyer_id'         => $userId,
                'receiver_name'    => $data['receiver_name'],
                'receiver_phone'   => $data['receiver_phone'],
                'receiver_address' => $data['receiver_address'],
                'payment_method'   => $data['payment_method'],
                'grand_total'      => 0,
                'placed_at'        => now(),
            ]);

            $grand = 0;
            foreach ($byShop as $shopId => $lines) {
                $shopOrder = ShopOrder::create([
                    'order_id' => $order->id,
                    'shop_id'  => $shopId,
                    'subtotal' => 0,
                ]);

                $subtotal = 0;
                foreach ($lines as [$variant, $qty, $product]) {
                    $price = $variant->price;               // GIÁ LẤY TỪ DB, không tin client
                    $lineTotal = $price * $qty;
                    $subtotal += $lineTotal;

                    OrderItem::create([
                        'shop_order_id' => $shopOrder->id,
                        'product_id'    => $product->id,
                        'variant_id'    => $variant->id,
                        'name'          => $this->itemName($product, $variant),
                        'unit_price'    => $price,
                        'qty'           => $qty,
                        'line_total'    => $lineTotal,
                    ]);
                }

                $shopOrder->update(['subtotal' => $subtotal]);
                $grand += $subtotal;
            }

            $order->update(['grand_total' => $grand]);

            $cart->items()->delete(); // dọn giỏ

            return $order;
        });

        return response()->json([
            'response' => 'success',
            'data'     => $order->load('shopOrders.items'),
        ], 201);
    }

    private function itemName($product, ProductVariant $variant): string
    {
        $opts = $variant->option_values
            ? ' (' . implode(' / ', array_values($variant->option_values)) . ')'
            : '';
        return $product->name . $opts;
    }
}
