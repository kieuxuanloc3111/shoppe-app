<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ShopTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_mo_duoc_gian_hang(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $res = $this->postJson('/api/v1/shops', ['name' => 'Shop ABC']);

        $res->assertStatus(201)
            ->assertJsonPath('data.slug', 'shop-abc')
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('shops', ['name' => 'Shop ABC', 'slug' => 'shop-abc']);
    }

    public function test_khong_mo_duoc_hai_gian_hang(): void
    {
        $user = User::factory()->create();
        Shop::create(['user_id' => $user->id, 'name' => 'A', 'slug' => 'a']);
        Sanctum::actingAs($user);

        $this->postJson('/api/v1/shops', ['name' => 'B'])->assertStatus(409);
    }

    public function test_thieu_ten_thi_422(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->postJson('/api/v1/shops', [])->assertStatus(422);
    }

    public function test_slug_tu_dong_khong_trung(): void
    {
        Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'Shop ABC', 'slug' => 'shop-abc']);
        Sanctum::actingAs(User::factory()->create());

        $this->postJson('/api/v1/shops', ['name' => 'Shop ABC'])
            ->assertStatus(201)
            ->assertJsonPath('data.slug', 'shop-abc-2');
    }

    public function test_ai_cung_xem_duoc_gian_hang_cong_khai(): void
    {
        Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'Shop ABC', 'slug' => 'shop-abc']);

        $this->getJson('/api/v1/shops/shop-abc')
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Shop ABC');
    }

    public function test_xem_shop_khong_ton_tai_404(): void
    {
        $this->getJson('/api/v1/shops/khong-co')->assertStatus(404);
    }

    public function test_seller_sua_shop_slug_giu_nguyen(): void
    {
        $user = User::factory()->create();
        Shop::create(['user_id' => $user->id, 'name' => 'Cũ', 'slug' => 'cu', 'status' => 'active']);
        Sanctum::actingAs($user);

        $this->putJson('/api/v1/seller/shop', ['name' => 'Mới'])
            ->assertStatus(200)
            ->assertJsonPath('data.name', 'Mới')
            ->assertJsonPath('data.slug', 'cu');
    }

    public function test_nguoi_khong_co_shop_bi_chan(): void
    {
        Sanctum::actingAs(User::factory()->create());
        $this->putJson('/api/v1/seller/shop', ['name' => 'X'])->assertStatus(403);
    }

    public function test_chua_dang_nhap_khong_mo_shop(): void
    {
        $this->postJson('/api/v1/shops', ['name' => 'X'])->assertStatus(401);
    }
}
