<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VnpayCallbackTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config(['vnpay.hash_secret' => 'testsecret']);
    }

    private function payment(int $amount = 250000): Payment
    {
        $order = Order::create(['buyer_id' => User::factory()->create()->id, 'receiver_name' => 'A', 'receiver_phone' => '09', 'receiver_address' => 'H', 'grand_total' => $amount, 'payment_method' => 'vnpay']);
        return Payment::create(['order_id' => $order->id, 'gateway' => 'vnpay', 'amount' => $amount, 'status' => 'pending']);
    }

    private function sign(array $p): string
    {
        ksort($p);
        $parts = [];
        foreach ($p as $k => $v) {
            $parts[] = urlencode($k) . '=' . urlencode($v);
        }
        return hash_hmac('sha512', implode('&', $parts), 'testsecret');
    }

    private function params(Payment $p, string $code = '00'): array
    {
        $data = ['vnp_TmnCode' => 'TESTCODE', 'vnp_TxnRef' => (string) $p->id, 'vnp_Amount' => (string) ((int) round((float) $p->amount * 100)), 'vnp_ResponseCode' => $code, 'vnp_TransactionNo' => '99999'];
        $data['vnp_SecureHash'] = $this->sign($data);
        return $data;
    }

    private function ipn(array $params)
    {
        return $this->getJson('/api/v1/payment/vnpay/ipn?' . http_build_query($params));
    }

    public function test_thanh_toan_thanh_cong_mark_paid(): void
    {
        $p = $this->payment();
        $this->ipn($this->params($p, '00'))->assertJsonPath('RspCode', '00');
        $this->assertSame('success', $p->fresh()->status);
        $this->assertSame('paid', $p->order->fresh()->payment_status);
    }

    public function test_chu_ky_sai_97(): void
    {
        $p = $this->payment();
        $params = $this->params($p, '00');
        $params['vnp_Amount'] = '1';
        $this->ipn($params)->assertJsonPath('RspCode', '97');
        $this->assertSame('pending', $p->fresh()->status);
    }

    public function test_ban_lai_idempotent_02(): void
    {
        $p = $this->payment();
        $this->ipn($this->params($p, '00'))->assertJsonPath('RspCode', '00');
        $this->ipn($this->params($p, '00'))->assertJsonPath('RspCode', '02');
    }

    public function test_sai_so_tien_04(): void
    {
        $p = $this->payment(250000);
        $data = ['vnp_TmnCode' => 'TESTCODE', 'vnp_TxnRef' => (string) $p->id, 'vnp_Amount' => '100', 'vnp_ResponseCode' => '00', 'vnp_TransactionNo' => '9'];
        $data['vnp_SecureHash'] = $this->sign($data);
        $this->ipn($data)->assertJsonPath('RspCode', '04');
    }

    public function test_don_khong_ton_tai_01(): void
    {
        $data = ['vnp_TmnCode' => 'TESTCODE', 'vnp_TxnRef' => '99999', 'vnp_Amount' => '100', 'vnp_ResponseCode' => '00', 'vnp_TransactionNo' => '9'];
        $data['vnp_SecureHash'] = $this->sign($data);
        $this->ipn($data)->assertJsonPath('RspCode', '01');
    }

    public function test_thanh_toan_that_bai_khong_paid(): void
    {
        $p = $this->payment();
        $this->ipn($this->params($p, '24'))->assertJsonPath('RspCode', '00');
        $this->assertSame('failed', $p->fresh()->status);
        $this->assertSame('pending', $p->order->fresh()->payment_status);
    }
}
