<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function variant(int $price, int $stock): ProductVariant
    {
        $u = User::factory()->create();
        $shop = Shop::create(['user_id' => $u->id, 'name' => 'S' . $u->id, 'slug' => 's' . $u->id, 'status' => 'active']);
        $p = Products::create(['shop_id' => $shop->id, 'category_id' => Category::create(['name' => 'C' . $u->id])->id, 'brand_id' => Brand::create(['name' => 'B' . $u->id])->id, 'name' => 'P' . $u->id]);
        return ProductVariant::create(['product_id' => $p->id, 'price' => $price, 'stock' => $stock]);
    }

    private function cartFor(User $buyer, array $lines): void
    {
        $cart = Cart::create(['user_id' => $buyer->id]);
        foreach ($lines as [$variant, $qty]) {
            CartItem::create(['cart_id' => $cart->id, 'variant_id' => $variant->id, 'qty' => $qty]);
        }
    }

    private function payload(array $extra = []): array
    {
        return array_merge(['receiver_name' => 'A', 'receiver_phone' => '0900', 'receiver_address' => 'HN', 'payment_method' => 'cod'], $extra);
    }

    public function test_tach_don_theo_shop_va_tru_kho(): void
    {
        $buyer = User::factory()->create();
        $vA = $this->variant(100, 10);
        $vB = $this->variant(50, 10);
        $this->cartFor($buyer, [[$vA, 2], [$vB, 1]]);
        Sanctum::actingAs($buyer);

        $res = $this->postJson('/api/v1/checkout', $this->payload())->assertStatus(201);
        $res->assertJsonPath('data.grand_total', '250.00');
        $this->assertCount(2, $res->json('data.shop_orders'));
        $this->assertSame(8, $vA->fresh()->stock);
        $this->assertSame(9, $vB->fresh()->stock);
        $this->assertDatabaseCount('cart_items', 0);
    }

    public function test_gia_tu_db_bo_qua_gia_client(): void
    {
        $buyer = User::factory()->create();
        $v = $this->variant(100, 5);
        $this->cartFor($buyer, [[$v, 2]]);
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/checkout', $this->payload(['price' => 1]))
            ->assertStatus(201)
            ->assertJsonPath('data.grand_total', '200.00');
    }

    public function test_oversell_bi_chan_va_rollback(): void
    {
        $buyer = User::factory()->create();
        $v = $this->variant(100, 2);
        $this->cartFor($buyer, [[$v, 2]]);
        $v->update(['stock' => 1]);
        Sanctum::actingAs($buyer);

        $this->postJson('/api/v1/checkout', $this->payload())->assertStatus(422);
        $this->assertSame(1, $v->fresh()->stock);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_gio_trong_thi_400(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/checkout', $this->payload())->assertStatus(400);
    }

    public function test_thieu_receiver_422(): void
    {
        $buyer = User::factory()->create();
        $this->cartFor($buyer, [[$this->variant(100, 5), 1]]);
        Sanctum::actingAs($buyer);
        $this->postJson('/api/v1/checkout', ['payment_method' => 'cod'])->assertStatus(422);
    }

    public function test_payment_method_sai_422(): void
    {
        $buyer = User::factory()->create();
        $this->cartFor($buyer, [[$this->variant(100, 5), 1]]);
        Sanctum::actingAs($buyer);
        $this->postJson('/api/v1/checkout', $this->payload(['payment_method' => 'bitcoin']))->assertStatus(422);
    }
}
