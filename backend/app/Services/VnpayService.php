<?php

namespace App\Services;

use App\Models\Payment;

/**
 * Tích hợp VNPay (chuẩn 2.1.0): tạo URL thanh toán + verify chữ ký callback.
 * Chữ ký = HMAC-SHA512 trên chuỗi param đã sort + urlencode.
 */
class VnpayService
{
    public function createPaymentUrl(Payment $payment, string $ip): string
    {
        $cfg = config('vnpay');

        $data = [
            'vnp_Version'   => '2.1.0',
            'vnp_Command'   => 'pay',
            'vnp_TmnCode'   => $cfg['tmn_code'],
            'vnp_Amount'    => (int) round((float) $payment->amount * 100), // VNPay nhân 100
            'vnp_CurrCode'  => 'VND',
            'vnp_TxnRef'    => (string) $payment->id,   // để callback tra ngược
            'vnp_OrderInfo' => 'Thanh toan don hang ' . $payment->order_id,
            'vnp_OrderType' => 'other',
            'vnp_Locale'    => 'vn',
            'vnp_ReturnUrl' => $cfg['return_url'],
            'vnp_IpAddr'    => $ip,
            'vnp_CreateDate' => now()->format('YmdHis'),
        ];

        ksort($data);
        $hashData = $this->buildHashData($data);
        $secureHash = hash_hmac('sha512', $hashData, $cfg['hash_secret']);

        return $cfg['url'] . '?' . $hashData . '&vnp_SecureHash=' . $secureHash;
    }

    // verify chữ ký từ callback/IPN
    public function validSignature(array $params): bool
    {
        $received = $params['vnp_SecureHash'] ?? '';
        unset($params['vnp_SecureHash'], $params['vnp_SecureHashType']);

        ksort($params);
        $calc = hash_hmac('sha512', $this->buildHashData($params), config('vnpay.hash_secret'));

        return hash_equals($calc, $received);
    }

    private function buildHashData(array $data): string
    {
        $parts = [];
        foreach ($data as $k => $v) {
            $parts[] = urlencode($k) . '=' . urlencode($v);
        }
        return implode('&', $parts);
    }
}
