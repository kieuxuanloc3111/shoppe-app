<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Products;

class CartController extends Controller
{
    public function add(Request $request)
    {
        $id = $request->product_id;

        // lấy product
        $product = Products::find($id);

        if (!$product) {
            return response()->json(['status' => 'error']);
        }

        // lấy hình đại diện
        $images = json_decode($product->image, true);
        $image  = $images[0] ?? null;

        // lấy cart từ session
        $cart = session()->get('cart', []);

        // nếu đã có sp → tăng số lượng
        if (isset($cart[$id])) {
            $cart[$id]['qty'] += 1;
        } else {
            $cart[$id] = [
                'id'    => $product->id,
                'name'  => $product->name,
                'price' => $product->sale == 1
                            ? $product->sale_price
                            : $product->price,
                'image' => $image,
                'qty'   => 1
            ];
        }

        // lưu lại session
        session()->put('cart', $cart);

        // tổng số item trong cart
        $totalQty = array_sum(array_column($cart, 'qty'));

        return response()->json([
            'status' => 'success',
            'total'  => $totalQty
        ]);
    }
    public function index()
    {
        $cart = session()->get('cart', []);

        return view('frontend.cart.index', compact('cart'));
    }
        public function update(Request $request)
    {
        $id  = $request->id;
        $qty = (int)$request->qty;

        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            if ($qty <= 0) {
                unset($cart[$id]);
            } else {
                $cart[$id]['qty'] = $qty;
            }
        }

        session()->put('cart', $cart);

        return response()->json([
            'cart'  => $cart,
            'total' => $this->total($cart),
            'count' => array_sum(array_column($cart, 'qty'))
        ]);
    }

    // delete
    public function delete(Request $request)
    {
        $id = $request->id;

        $cart = session()->get('cart', []);

        unset($cart[$id]);

        session()->put('cart', $cart);

        return response()->json([
            'cart'  => $cart,
            'total' => $this->total($cart),
            'count' => array_sum(array_column($cart, 'qty'))
        ]);
    }

    private function total($cart)
    {
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['qty'];
        }
        return $total;
    }
}
