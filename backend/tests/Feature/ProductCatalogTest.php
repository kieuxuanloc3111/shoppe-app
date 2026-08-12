<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function seller(): User
    {
        $u = User::factory()->create();
        Shop::create(['user_id' => $u->id, 'name' => 'S' . $u->id, 'slug' => 's' . $u->id, 'status' => 'active']);
        return $u;
    }

    private function payload(): array
    {
        $cat = Category::create(['name' => 'Áo']);
        $brand = Brand::create(['name' => 'Nike']);
        return [
            'name'        => 'Áo thun',
            'category_id' => $cat->id,
            'brand_id'    => $brand->id,
            'description' => 'mô tả',
            'options'     => [['name' => 'Size', 'values' => ['S', 'M']]],
            'variants'    => [
                ['price' => 100, 'stock' => 5, 'option_values' => ['Size' => 'S']],
                ['price' => 150, 'stock' => 3, 'option_values' => ['Size' => 'M']],
            ],
        ];
    }

    public function test_seller_tao_san_pham_co_variant(): void
    {
        Sanctum::actingAs($this->seller());

        $this->postJson('/api/v1/user/product/add', $this->payload())
            ->assertStatus(201)
            ->assertJsonPath('data.price_min', '100.00')
            ->assertJsonPath('data.price_max', '150.00');

        $this->assertDatabaseCount('product_variants', 2);
        $this->assertDatabaseCount('product_options', 1);
    }

    public function test_thieu_variant_thi_422(): void
    {
        Sanctum::actingAs($this->seller());
        $p = $this->payload();
        unset($p['variants']);
        $this->postJson('/api/v1/user/product/add', $p)->assertStatus(422);
    }

    public function test_category_khong_ton_tai_422(): void
    {
        Sanctum::actingAs($this->seller());
        $p = $this->payload();
        $p['category_id'] = 99999;
        $this->postJson('/api/v1/user/product/add', $p)->assertStatus(422);
    }

    public function test_khong_phai_seller_thi_bi_chan(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/user/product/add', $this->payload())->assertStatus(403);
    }

    public function test_khong_sua_duoc_san_pham_shop_khac(): void
    {
        $a = $this->seller();
        Sanctum::actingAs($a);
        $pid = $this->postJson('/api/v1/user/product/add', $this->payload())->json('data.id');

        Sanctum::actingAs($this->seller());
        $this->postJson("/api/v1/user/product/update/{$pid}", $this->payload())->assertStatus(403);
    }

    public function test_danh_sach_tra_ve_khoang_gia(): void
    {
        Sanctum::actingAs($this->seller());
        $this->postJson('/api/v1/user/product/add', $this->payload());

        $this->getJson('/api/v1/product')
            ->assertStatus(200)
            ->assertJsonPath('data.0.price_min', '100.00');
    }
}
