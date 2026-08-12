<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\User;
use App\Services\WalletService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WalletServiceTest extends TestCase
{
    use RefreshDatabase;

    private function shopId(): int
    {
        return Shop::create(['user_id' => User::factory()->create()->id, 'name' => 'S', 'slug' => 's' . uniqid(), 'status' => 'active'])->id;
    }

    public function test_cong_tien_ban_va_ghi_so_cai(): void
    {
        $svc = new WalletService();
        $shopId = $this->shopId();

        $svc->creditSale($shopId, 427000, 1);
        $svc->creditSale($shopId, 100000, 2);

        $wallet = $svc->wallet($shopId);
        $this->assertEquals('527000.00', $wallet->available);
        $this->assertSame(2, $wallet->ledger()->count());
        $this->assertEquals('527000.00', $wallet->ledger()->latest('id')->first()->balance_after);
    }

    public function test_rut_tien_tru_available(): void
    {
        $svc = new WalletService();
        $shopId = $this->shopId();

        $svc->creditSale($shopId, 500000, 1);
        $ledger = $svc->debitPayout($shopId, 200000, 1);

        $this->assertEquals('-200000.00', $ledger->amount);
        $this->assertEquals('300000.00', $ledger->balance_after);
        $this->assertEquals('300000.00', $svc->wallet($shopId)->available);
    }

    public function test_hold_va_release(): void
    {
        $svc = new WalletService();
        $shopId = $this->shopId();

        $svc->hold($shopId, 427000, 'shop_order', 1);
        $this->assertEquals('427000.00', $svc->wallet($shopId)->pending);
        $this->assertEquals('0.00', $svc->wallet($shopId)->available);

        $svc->release($shopId, 427000, 'shop_order', 1);
        $this->assertEquals('0.00', $svc->wallet($shopId)->pending);
        $this->assertEquals('427000.00', $svc->wallet($shopId)->available);
    }
}
