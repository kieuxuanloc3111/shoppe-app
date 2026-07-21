<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index()
    {
        return response()->json([
            'response' => 'success',
            'data'     => $this->cartPayload($this->cart()),
        ]);
    }

    // thêm vào giỏ: chỉ nhận variant_id + qty, GIÁ luôn lấy từ DB
    public function add(Request $request)
    {
        $data = $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'qty'        => 'required|integer|min:1',
        ]);

        $variant = ProductVariant::findOrFail($data['variant_id']);
        $cart = $this->cart();

        $item = $cart->items()->where('variant_id', $variant->id)->first();
        $newQty = ($item->qty ?? 0) + $data['qty'];

        if ($newQty > $variant->stock) {
            return response()->json([
                'response' => 'error',
                'message'  => 'Vượt quá tồn kho (còn ' . $variant->stock . ')',
            ], 422);
        }

        $cart->items()->updateOrCreate(
            ['variant_id' => $variant->id],
            ['qty' => $newQty]
        );

        return response()->json(['response' => 'success', 'data' => $this->cartPayload($cart)]);
    }

    // đặt lại số lượng 1 dòng
    public function update(Request $request, $itemId)
    {
        $data = $request->validate(['qty' => 'required|integer|min:1']);

        $item = $this->ownItem($itemId);

        if ($data['qty'] > $item->variant->stock) {
            return response()->json([
                'response' => 'error',
                'message'  => 'Vượt quá tồn kho (còn ' . $item->variant->stock . ')',
            ], 422);
        }

        $item->update(['qty' => $data['qty']]);

        return response()->json(['response' => 'success', 'data' => $this->cartPayload($item->cart)]);
    }

    public function remove($itemId)
    {
        $item = $this->ownItem($itemId);
        $cart = $item->cart;
        $item->delete();

        return response()->json(['response' => 'success', 'data' => $this->cartPayload($cart)]);
    }

    /* ===== helper ===== */

    private function cart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => auth()->id()]);
    }

    // đảm bảo item thuộc giỏ của user hiện tại (chống IDOR)
    private function ownItem($itemId): CartItem
    {
        $item = CartItem::with('variant')->findOrFail($itemId);

        if (!$item->cart || $item->cart->user_id !== auth()->id()) {
            abort(403, 'Không phải giỏ của bạn');
        }

        return $item;
    }

    private function cartPayload(Cart $cart): array
    {
        $cart->load(['items.variant.product.images']);

        $items = $cart->items->map(function (CartItem $item) {
            $variant = $item->variant;
            $product = $variant?->product;
            $price = (float) ($variant->price ?? 0);

            return [
                'id'         => $item->id,
                'variant_id' => $item->variant_id,
                'qty'        => $item->qty,
                'price'      => $price,                       // giá từ DB
                'line_total' => $price * $item->qty,
                'stock'      => $variant->stock ?? 0,
                'product'    => [
                    'id'    => $product->id ?? null,
                    'name'  => $product->name ?? null,
                    'image' => optional($product?->images->first())->url ?? '',
                ],
                'option_values' => $variant->option_values ?? null,
            ];
        });

        return [
            'items' => $items->values(),
            'count' => $items->count(),
            'total' => $items->sum('line_total'),
        ];
    }
}
