<?php

namespace Tests\Feature;

use App\Models\FeeSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\SellerWallet;
use App\Models\Shop;
use App\Models\ShopOrder;
use App\Models\ShopOrderFee;
use App\Models\User;
use App\Models\WalletLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderSchemaTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_long_shop_order_va_item(): void
    {
        $buyer = User::factory()->create();
        $shop = Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'S', 'slug' => 's', 'status' => 'active']);

        $order = Order::create(['buyer_id' => $buyer->id, 'receiver_name' => 'A', 'receiver_phone' => '0900', 'receiver_address' => 'HN', 'grand_total' => 250]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 250]);
        OrderItem::create(['shop_order_id' => $so->id, 'name' => 'Áo (Đỏ/S)', 'unit_price' => 125, 'qty' => 2, 'line_total' => 250]);

        $this->assertSame(1, $order->shopOrders()->count());
        $this->assertSame(1, $so->items()->count());
        $this->assertSame('pending', $order->fresh()->payment_status);
        $this->assertSame('pending', $so->fresh()->status);
        $this->assertSame($order->id, $so->order->id);
    }

    public function test_vi_so_cai_va_phi(): void
    {
        $shop = Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'S', 'slug' => 's', 'status' => 'active']);

        $wallet = SellerWallet::create(['shop_id' => $shop->id, 'available' => 0]);
        WalletLedger::create(['shop_id' => $shop->id, 'type' => 'sale_earning', 'amount' => 427, 'balance_after' => 427]);
        $this->assertSame(1, $wallet->ledger()->count());

        $order = Order::create(['buyer_id' => User::factory()->create()->id, 'receiver_name' => 'A', 'receiver_phone' => '0900', 'receiver_address' => 'HN', 'grand_total' => 500]);
        $so = ShopOrder::create(['order_id' => $order->id, 'shop_id' => $shop->id, 'subtotal' => 500]);
        ShopOrderFee::create(['shop_order_id' => $so->id, 'commission' => 20, 'payment_fee' => 25, 'tech_fee' => 25, 'infra_fee' => 3, 'platform_total' => 73, 'seller_earning' => 427]);

        $this->assertEquals('427.00', $so->fee->seller_earning);
    }

    public function test_fee_setting_current_idempotent(): void
    {
        $s = FeeSetting::current();
        $this->assertNotNull($s->id);
        $this->assertSame($s->id, FeeSetting::current()->id);
    }
}
