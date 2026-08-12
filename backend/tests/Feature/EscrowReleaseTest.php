<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class EscrowReleaseTest extends TestCase
{
    use RefreshDatabase;

    // đơn 500k, hoa hồng 4% → seller_earning 427k. trả [order, shopOrder, sellerUser, buyer]
    private function order(string $method, string $status = 'shipping'): array
    {
        $sellerUser = User::factory()->create();
        $shop = Shop::create(['user_id' => $sellerUser->id, 'name' => 'S', 'slug' => 's' . uniqid(), 'status' => 'active']);
        $cat = Category::create(['name' => 'Áo' . uniqid(), 'commission_rate' => 4]);
        $product = Products::create(['shop_id' => $shop->id, 'category_id' => $cat->id, 'brand_id' => Brand::create(['name' => 'B' . uniqid()])->id, 'name' => 'P']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'price' => 500000, 'stock' => 10]);
        $buyer = User::factory()->create();

        $order = Order::create(['buyer_id' => $buyer->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => 500000, 'payment_method' => $method]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 500000, 'status' => $status]);
        OrderItem::create(['shop_order_id' => $so->id, 'product_id' => $product->id, 'variant_id' => $variant->id, 'name' => 'P', 'unit_price' => 500000, 'qty' => 1, 'line_total' => 500000]);

        return [$order, $so, $sellerUser, $buyer];
    }

    public function test_cod_deliver_giu_pending_received_nha_available(): void
    {
        [, $so, $seller, $buyer] = $this->order('cod', 'shipping');

        // seller giao (COD thu tiền) → pending
        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/deliver")->assertStatus(200);
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 427000, 'available' => 0]);

        // buyer nhận → nhả available
        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/received")->assertStatus(200);
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 0, 'available' => 427000]);
    }

    public function test_vnpay_paid_hold_roi_received_nha(): void
    {
        [$order, $so, $seller, $buyer] = $this->order('vnpay', 'confirmed');

        // VNPay đã trả → hold pending
        (new PaymentService())->settleOrderPaid($order);
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 427000]);

        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/ship")->assertStatus(200);
        $this->postJson("/api/v1/seller/orders/{$so->id}/deliver")->assertStatus(200);
        // VNPay không hold lần 2 lúc deliver
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 427000]);

        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/received")->assertStatus(200);
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 0, 'available' => 427000]);
    }
}
