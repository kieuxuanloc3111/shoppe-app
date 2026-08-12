<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_tra_ve_cay_danh_muc_long_nhau(): void
    {
        $a = Category::create(['name' => 'Thời trang']);
        $b = Category::create(['name' => 'Áo', 'parent_id' => $a->id]);
        Category::create(['name' => 'Áo thun', 'parent_id' => $b->id]);

        $res = $this->getJson('/api/v1/categories');

        $res->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['name' => 'Áo'])
            ->assertJsonFragment(['name' => 'Áo thun']);
    }

    public function test_slug_tu_sinh_va_khong_trung(): void
    {
        $x = Category::create(['name' => 'Đồ ăn']);
        $y = Category::create(['name' => 'Đồ ăn']);

        $this->assertSame('do-an', $x->slug);
        $this->assertSame('do-an-2', $y->slug);
    }

    public function test_commission_rate_luu_dung(): void
    {
        $c = Category::create(['name' => 'Điện tử', 'commission_rate' => 4.5]);
        $this->assertEquals('4.50', $c->fresh()->commission_rate);
    }

    public function test_khong_danh_muc_tra_mang_rong(): void
    {
        $this->getJson('/api/v1/categories')->assertStatus(200)->assertJsonCount(0, 'data');
    }
}
