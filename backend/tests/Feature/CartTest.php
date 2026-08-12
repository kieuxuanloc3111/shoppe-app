<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Products;
use App\Models\ProductVariant;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    private function variant(int $price = 100, int $stock = 5): ProductVariant
    {
        $u = User::factory()->create();
        $shop = Shop::create(['user_id' => $u->id, 'name' => 'S' . $u->id, 'slug' => 's' . $u->id, 'status' => 'active']);
        $product = Products::create([
            'shop_id'     => $shop->id,
            'category_id' => Category::create(['name' => 'C' . $u->id])->id,
            'brand_id'    => Brand::create(['name' => 'B' . $u->id])->id,
            'name'        => 'P' . $u->id,
        ]);
        return ProductVariant::create(['product_id' => $product->id, 'price' => $price, 'stock' => $stock]);
    }

    public function test_them_gio_dung_gia_db(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $v = $this->variant(100, 5);

        $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 2])
            ->assertStatus(200)
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.total', 200)
            ->assertJsonPath('data.items.0.price', 100);
    }

    public function test_khong_nhan_gia_client(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $v = $this->variant(100, 5);

        $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 1, 'price' => 1])
            ->assertJsonPath('data.total', 100);
    }

    public function test_chan_vuot_ton_kho(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $v = $this->variant(100, 3);
        $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 5])->assertStatus(422);
    }

    public function test_variant_khong_ton_tai_422(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/cart', ['variant_id' => 99999, 'qty' => 1])->assertStatus(422);
    }

    public function test_them_trung_variant_thi_gop(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $v = $this->variant(100, 10);

        $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 2]);
        $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 3])
            ->assertJsonPath('data.count', 1)
            ->assertJsonPath('data.items.0.qty', 5);
    }

    public function test_khong_dung_gio_nguoi_khac(): void
    {
        $a = User::factory()->create();
        Sanctum::actingAs($a);
        $v = $this->variant(100, 5);
        $itemId = $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 1])->json('data.items.0.id');

        Sanctum::actingAs(User::factory()->create());
        $this->putJson("/api/v1/cart/{$itemId}", ['qty' => 1])->assertStatus(403);
    }

    public function test_update_vuot_kho_422(): void
    {
        $u = User::factory()->create();
        Sanctum::actingAs($u);
        $v = $this->variant(100, 3);
        $itemId = $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 1])->json('data.items.0.id');

        $this->putJson("/api/v1/cart/{$itemId}", ['qty' => 5])->assertStatus(422);
    }

    public function test_xoa_item(): void
    {
        $u = User::factory()->create();
        Sanctum::actingAs($u);
        $v = $this->variant(100, 5);
        $itemId = $this->postJson('/api/v1/cart', ['variant_id' => $v->id, 'qty' => 1])->json('data.items.0.id');

        $this->deleteJson("/api/v1/cart/{$itemId}")->assertStatus(200)->assertJsonPath('data.count', 0);
    }
}
