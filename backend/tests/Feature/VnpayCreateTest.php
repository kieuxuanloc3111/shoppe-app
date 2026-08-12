<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use App\Services\VnpayService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class VnpayCreateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['vnpay.hash_secret' => 'testsecret', 'vnpay.tmn_code' => 'TESTCODE', 'vnpay.url' => 'https://sandbox.vnpayment.vn/pay', 'vnpay.return_url' => 'https://shop.test/return']);
    }

    private function order(string $method = 'vnpay', ?int $buyerId = null): Order
    {
        return Order::create(['buyer_id' => $buyerId ?? User::factory()->create()->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => 250000, 'payment_method' => $method]);
    }

    public function test_url_co_chu_ky_va_verify_dung(): void
    {
        $order = $this->order();
        $payment = Payment::create(['order_id' => $order->id, 'gateway' => 'vnpay', 'amount' => 250000, 'status' => 'pending']);

        $url = (new VnpayService())->createPaymentUrl($payment, '127.0.0.1');
        $this->assertStringContainsString('vnp_SecureHash=', $url);

        parse_str(parse_url($url, PHP_URL_QUERY), $params);
        $this->assertTrue((new VnpayService())->validSignature($params));
    }

    public function test_chu_ky_sai_bi_tu_choi(): void
    {
        $order = $this->order();
        $payment = Payment::create(['order_id' => $order->id, 'gateway' => 'vnpay', 'amount' => 250000, 'status' => 'pending']);
        parse_str(parse_url((new VnpayService())->createPaymentUrl($payment, '127.0.0.1'), PHP_URL_QUERY), $params);
        $params['vnp_Amount'] = '1';
        $this->assertFalse((new VnpayService())->validSignature($params));
    }

    public function test_pay_tra_url_cho_don_vnpay(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order('vnpay', $buyer->id);
        Sanctum::actingAs($buyer);

        $this->postJson("/api/v1/orders/{$order->id}/pay")->assertStatus(200)->assertJsonStructure(['pay_url']);
        $this->assertDatabaseCount('payments', 1);
    }

    public function test_cod_khong_pay_online(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order('cod', $buyer->id);
        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$order->id}/pay")->assertStatus(422);
    }

    public function test_khong_pay_don_nguoi_khac(): void
    {
        $order = $this->order('vnpay');
        Sanctum::actingAs(User::factory()->create());
        $this->postJson("/api/v1/orders/{$order->id}/pay")->assertStatus(403);
    }

    public function test_don_da_paid_khong_pay_lai(): void
    {
        $buyer = User::factory()->create();
        $order = $this->order('vnpay', $buyer->id);
        $order->update(['payment_status' => 'paid']);
        Sanctum::actingAs($buyer);
        $this->postJson("/api/v1/orders/{$order->id}/pay")->assertStatus(422);
    }
}
