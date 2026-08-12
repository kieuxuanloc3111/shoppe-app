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

class EscrowHoldTest extends TestCase
{
    use RefreshDatabase;

    private function order(string $method = 'vnpay', string $status = 'confirmed'): array
    {
        $sellerUser = User::factory()->create();
        $shop = Shop::create(['user_id' => $sellerUser->id, 'name' => 'S', 'slug' => 's' . uniqid(), 'status' => 'active']);
        $cat = Category::create(['name' => 'Áo' . uniqid(), 'commission_rate' => 4]);
        $product = Products::create(['shop_id' => $shop->id, 'category_id' => $cat->id, 'brand_id' => Brand::create(['name' => 'B' . uniqid()])->id, 'name' => 'P']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'price' => 500000, 'stock' => 10]);

        $order = Order::create(['buyer_id' => User::factory()->create()->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => 500000, 'payment_method' => $method]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 500000, 'status' => $status]);
        OrderItem::create(['shop_order_id' => $so->id, 'product_id' => $product->id, 'variant_id' => $variant->id, 'name' => 'P', 'unit_price' => 500000, 'qty' => 1, 'line_total' => 500000]);

        return [$order, $so, $sellerUser];
    }

    public function test_paid_giu_tien_vao_pending(): void
    {
        [$order, $so] = $this->order();
        (new PaymentService())->settleOrderPaid($order);

        $this->assertSame('paid', $order->fresh()->payment_status);
        $this->assertDatabaseHas('shop_order_fees', ['shop_order_id' => $so->id, 'seller_earning' => 427000]);
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 427000, 'available' => 0]);
    }

    public function test_settle_idempotent(): void
    {
        [$order, $so] = $this->order();
        (new PaymentService())->settleOrderPaid($order);
        (new PaymentService())->settleOrderPaid($order);
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 427000]);
    }

    public function test_vnpay_chua_paid_khong_ship_duoc(): void
    {
        [, $so, $seller] = $this->order('vnpay', 'confirmed');
        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/ship")->assertStatus(422);
    }

    public function test_vnpay_paid_thi_ship_duoc(): void
    {
        [$order, $so, $seller] = $this->order('vnpay', 'confirmed');
        (new PaymentService())->settleOrderPaid($order);
        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/ship")->assertStatus(200);
        $this->assertSame('shipping', $so->fresh()->status);
    }

    public function test_cod_ship_khong_can_paid(): void
    {
        [, $so, $seller] = $this->order('cod', 'confirmed');
        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/ship")->assertStatus(200);
    }
}
