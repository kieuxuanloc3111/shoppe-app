<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

/**
 * Xử lý kết quả thanh toán VNPay (return + IPN dùng chung).
 * Trả về mã RspCode theo chuẩn VNPay để IPN phản hồi.
 */
class PaymentService
{
    public function __construct(private VnpayService $vnpay = new VnpayService()) {}

    public function handleVnpayCallback(array $params): array
    {
        if (!$this->vnpay->validSignature($params)) {
            return ['RspCode' => '97', 'Message' => 'Invalid signature'];
        }

        $payment = Payment::find($params['vnp_TxnRef'] ?? 0);
        if (!$payment) {
            return ['RspCode' => '01', 'Message' => 'Order not found'];
        }

        // đối chiếu số tiền (VNPay nhân 100)
        if ((int) ($params['vnp_Amount'] ?? 0) !== (int) round((float) $payment->amount * 100)) {
            return ['RspCode' => '04', 'Message' => 'Invalid amount'];
        }

        // idempotent: đã xử lý thành công rồi
        if ($payment->status === 'success') {
            return ['RspCode' => '02', 'Message' => 'Order already confirmed'];
        }

        if (($params['vnp_ResponseCode'] ?? '') === '00') {
            $this->markPaid($payment, $params);
        } else {
            $payment->update(['status' => 'failed', 'raw' => $params]);
        }

        return ['RspCode' => '00', 'Message' => 'Confirm Success'];
    }

    private function markPaid(Payment $payment, array $params): void
    {
        DB::transaction(function () use ($payment, $params) {
            $payment->update([
                'status'         => 'success',
                'gateway_txn_id' => $params['vnp_TransactionNo'] ?? null,
                'raw'            => $params,
            ]);

            $this->settleOrderPaid($payment->order);
        });
    }

    /**
     * Sự kiện "đơn đã thanh toán" (dùng chung VNPay callback + COD delivered).
     * Escrow: set paid → mỗi shop_order tính phí + giữ seller_earning vào `pending`.
     * Idempotent: đơn đã paid thì bỏ qua (không hold 2 lần).
     */
    public function settleOrderPaid(Order $order): void
    {
        if ($order->payment_status === 'paid') {
            return;
        }

        DB::transaction(function () use ($order) {
            $order->update(['payment_status' => 'paid']);

            $fee = new FeeCalculator();
            $wallet = new WalletService();

            foreach ($order->shopOrders as $shopOrder) {
                $f = $fee->calculate($shopOrder);
                $wallet->hold($shopOrder->shop_id, (float) $f->seller_earning, 'shop_order', $shopOrder->id);
            }
        });
    }
}
