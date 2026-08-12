<?php

namespace Tests\Feature;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Products;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\User;
use App\Services\FeeCalculator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeeCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private function shopOrder(int $subtotal, int $commission): ShopOrder
    {
        $cat = Category::create(['name' => 'Áo' . uniqid(), 'commission_rate' => $commission]);
        $shop = Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'S', 'slug' => 's' . uniqid(), 'status' => 'active']);
        $product = Products::create(['shop_id' => $shop->id, 'category_id' => $cat->id, 'brand_id' => Brand::create(['name' => 'B' . uniqid()])->id, 'name' => 'P']);
        $order = Order::create(['buyer_id' => User::factory()->create()->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => $subtotal]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => $subtotal]);
        OrderItem::create(['shop_order_id' => $so->id, 'product_id' => $product->id, 'name' => 'P', 'unit_price' => $subtotal, 'qty' => 1, 'line_total' => $subtotal]);
        return $so;
    }

    public function test_boc_phi_khop_vi_du_design_doc(): void
    {
        $so = $this->shopOrder(500000, 4);
        $fee = (new FeeCalculator())->calculate($so);

        $this->assertEquals('20000.00', $fee->commission);
        $this->assertEquals('25000.00', $fee->payment_fee);
        $this->assertEquals('25000.00', $fee->tech_fee);
        $this->assertEquals('3000.00', $fee->infra_fee);
        $this->assertEquals('73000.00', $fee->platform_total);
        $this->assertEquals('427000.00', $fee->seller_earning);
    }

    public function test_goi_lai_khong_tao_trung(): void
    {
        $so = $this->shopOrder(1000, 2);
        (new FeeCalculator())->calculate($so);
        (new FeeCalculator())->calculate($so);
        $this->assertDatabaseCount('shop_order_fees', 1);
    }
}
