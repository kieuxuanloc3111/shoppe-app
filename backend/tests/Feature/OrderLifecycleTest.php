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
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderLifecycleTest extends TestCase
{
    use RefreshDatabase;

    // trả [buyer, sellerUser, shopOrder, variant]. cod (không chặn ship).
    private function make(string $status, int $stock = 8, int $qty = 2): array
    {
        $buyer = User::factory()->create();
        $sellerUser = User::factory()->create();
        $shop = Shop::create(['user_id' => $sellerUser->id, 'name' => 'S', 'slug' => 's' . uniqid(), 'status' => 'active']);
        $cat = Category::create(['name' => 'Áo' . uniqid(), 'commission_rate' => 4]);
        $product = Products::create(['shop_id' => $shop->id, 'category_id' => $cat->id, 'brand_id' => Brand::create(['name' => 'B' . uniqid()])->id, 'name' => 'P']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'price' => 250000, 'stock' => $stock]);

        $order = Order::create(['buyer_id' => $buyer->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => 500000, 'payment_method' => 'cod']);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 500000, 'status' => $status]);
        OrderItem::create(['shop_order_id' => $so->id, 'product_id' => $product->id, 'variant_id' => $variant->id, 'name' => 'P', 'unit_price' => 250000, 'qty' => $qty, 'line_total' => 500000]);

        return [$buyer, $sellerUser, $so, $variant];
    }

    private function makeSeller(): User
    {
        $u = User::factory()->create();
        Shop::create(['user_id' => $u->id, 'name' => 'X', 'slug' => 'x' . uniqid(), 'status' => 'active']);
        return $u;
    }

    public function test_luong_day_du_confirm_ship_deliver_received(): void
    {
        [$buyer, $seller, $so] = $this->make('pending');

        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/confirm")->assertStatus(200);
        $this->assertSame('confirmed', $so->fresh()->status);
        $this->postJson("/api/v1/seller/orders/{$so->id}/ship")->assertStatus(200);
        $this->assertSame('shipping', $so->fresh()->status);
        $this->postJson("/api/v1/seller/orders/{$so->id}/deliver")->assertStatus(200);
        $this->assertSame('delivered', $so->fresh()->status);

        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/received")->assertStatus(200);
        $this->assertSame('completed', $so->fresh()->status);
    }

    public function test_ship_truoc_confirm_bi_chan(): void
    {
        [, $seller, $so] = $this->make('pending');
        Sanctum::actingAs($seller);
        $this->postJson("/api/v1/seller/orders/{$so->id}/ship")->assertStatus(422);
    }

    public function test_received_truoc_delivered_bi_chan(): void
    {
        [$buyer, , $so] = $this->make('shipping');
        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/received")->assertStatus(422);
    }

    public function test_seller_khac_khong_dong_duoc(): void
    {
        [, , $so] = $this->make('pending');
        Sanctum::actingAs($this->makeSeller());
        $this->postJson("/api/v1/seller/orders/{$so->id}/confirm")->assertStatus(403);
    }

    public function test_buyer_khac_khong_received(): void
    {
        [, , $so] = $this->make('delivered');
        Sanctum::actingAs(User::factory()->create());
        $this->postJson("/api/v1/orders/{$so->id}/received")->assertStatus(403);
    }

    public function test_huy_don_hoan_kho(): void
    {
        [$buyer, , $so, $variant] = $this->make('pending', 8, 2);
        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/cancel")->assertStatus(200);
        $this->assertSame('cancelled', $so->fresh()->status);
        $this->assertSame(10, $variant->fresh()->stock);
    }

    public function test_khong_huy_duoc_khi_da_ship(): void
    {
        [$buyer, , $so] = $this->make('shipping');
        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/cancel")->assertStatus(422);
    }
}
