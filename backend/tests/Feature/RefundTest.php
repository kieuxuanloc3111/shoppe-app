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

class RefundTest extends TestCase
{
    use RefreshDatabase;

    // đơn 500k, hoa hồng 4% → seller_earning 427k. trả [order, shopOrder, variant, buyer]
    private function order(string $method, int $stock = 8, int $qty = 2): array
    {
        $shop = Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'S', 'slug' => 's' . uniqid(), 'status' => 'active']);
        $cat = Category::create(['name' => 'Áo' . uniqid(), 'commission_rate' => 4]);
        $product = Products::create(['shop_id' => $shop->id, 'category_id' => $cat->id, 'brand_id' => Brand::create(['name' => 'B' . uniqid()])->id, 'name' => 'P']);
        $variant = ProductVariant::create(['product_id' => $product->id, 'price' => 250000, 'stock' => $stock]);
        $buyer = User::factory()->create();

        $order = Order::create(['buyer_id' => $buyer->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => 500000, 'payment_method' => $method]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 500000, 'status' => 'confirmed']);
        OrderItem::create(['shop_order_id' => $so->id, 'product_id' => $product->id, 'variant_id' => $variant->id, 'name' => 'P', 'unit_price' => 250000, 'qty' => $qty, 'line_total' => 500000]);

        return [$order, $so, $variant, $buyer];
    }

    public function test_huy_don_vnpay_da_tra_thi_hoan_tien(): void
    {
        [$order, $so, $variant, $buyer] = $this->order('vnpay');
        (new PaymentService())->settleOrderPaid($order); // đã trả → pending 427k
        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 427000]);

        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$so->id}/cancel")->assertStatus(200);

        $this->assertDatabaseHas('seller_wallets', ['shop_id' => $so->shop_id, 'pending' => 0]); // đảo hold
        $this->assertSame('refunded', $order->fresh()->payment_status);
        $this->assertSame('cancelled', $so->fresh()->status);
        $this->assertSame(10, $variant->fresh()->stock);                       // hoàn kho
        $this->assertDatabaseHas('wallet_ledger', ['shop_id' => $so->shop_id, 'type' => 'refund']);
    }

    public function test_huy_don_cod_chua_giu_tien_thi_hoan_thuong(): void
    {
        [$order, $so, $variant, $buyer] = $this->order('cod'); // COD confirmed → chưa hold
        Sanctum::actingAs($buyer);

        $this->postJson("/api/v1/orders/{$so->id}/cancel")->assertStatus(200);

        $this->assertSame('cancelled', $so->fresh()->status);
        $this->assertSame('pending', $order->fresh()->payment_status);         // không refunded (chưa thu tiền)
        $this->assertSame(10, $variant->fresh()->stock);
        $this->assertDatabaseMissing('wallet_ledger', ['shop_id' => $so->shop_id, 'type' => 'refund']);
    }
}
