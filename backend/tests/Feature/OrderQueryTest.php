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

class OrderQueryTest extends TestCase
{
    use RefreshDatabase;

    private function order(User $buyer, Shop $shop): array
    {
        $product = Products::create(['shop_id' => $shop->id, 'category_id' => Category::create(['name' => 'C' . uniqid()])->id, 'brand_id' => Brand::create(['name' => 'B' . uniqid()])->id, 'name' => 'P']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'price' => 100, 'stock' => 5]);
        $order = Order::create(['buyer_id' => $buyer->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => 200]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 200]);
        OrderItem::create(['shop_order_id' => $so->id, 'product_id' => $product->id, 'variant_id' => $variant->id, 'name' => 'P', 'unit_price' => 100, 'qty' => 2, 'line_total' => 200]);
        return [$order, $so];
    }

    private function shop(): Shop
    {
        $u = User::factory()->create();
        return Shop::create(['user_id' => $u->id, 'name' => 'S' . $u->id, 'slug' => 's' . $u->id, 'status' => 'active']);
    }

    public function test_buyer_chi_thay_don_minh(): void
    {
        $shop = $this->shop();
        $me = User::factory()->create();
        $this->order($me, $shop);
        $this->order(User::factory()->create(), $shop);

        Sanctum::actingAs($me);
        $res = $this->getJson('/api/v1/orders')->assertStatus(200);

        $this->assertCount(1, $res->json('data'));
        $this->assertArrayHasKey('shop_orders', $res->json('data.0'));
        $this->assertArrayNotHasKey('created_at', $res->json('data.0'));
    }

    public function test_buyer_khong_xem_don_nguoi_khac(): void
    {
        $shop = $this->shop();
        [$order] = $this->order(User::factory()->create(), $shop);
        Sanctum::actingAs(User::factory()->create());
        $this->getJson("/api/v1/orders/{$order->id}")->assertStatus(403);
    }

    public function test_seller_chi_thay_don_shop_minh(): void
    {
        $shopA = $this->shop();
        $shopB = $this->shop();
        $this->order(User::factory()->create(), $shopA);
        $this->order(User::factory()->create(), $shopB);

        Sanctum::actingAs($shopA->user);
        $res = $this->getJson('/api/v1/seller/orders')->assertStatus(200);

        $this->assertCount(1, $res->json('data'));
        $this->assertSame($shopA->id, $res->json('data.0.shop_id'));
    }
}
