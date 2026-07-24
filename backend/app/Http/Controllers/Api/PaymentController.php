<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Services\PaymentService;
use App\Services\VnpayService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    // Buyer bấm thanh toán VNPay → trả URL redirect
    public function pay(Request $request, Order $order)
    {
        if ($order->buyer_id !== auth()->id()) {
            abort(403, 'Không phải đơn của bạn');
        }
        if ($order->payment_status === 'paid') {
            return response()->json(['response' => 'error', 'message' => 'Đơn đã thanh toán'], 422);
        }
        if ($order->payment_method !== 'vnpay') {
            return response()->json(['response' => 'error', 'message' => 'Đơn này không thanh toán online'], 422);
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'gateway'  => 'vnpay',
            'amount'   => $order->grand_total,
            'status'   => 'pending',
        ]);

        $url = (new VnpayService())->createPaymentUrl($payment, $request->ip());

        return response()->json(['response' => 'success', 'pay_url' => $url]);
    }

    // VNPay redirect trình duyệt buyer về đây sau khi trả
    public function vnpayReturn(Request $request)
    {
        $result = (new PaymentService())->handleVnpayCallback($request->query());

        return response()->json([
            'response' => $result['RspCode'] === '00' ? 'success' : 'error',
            'paid'     => $result['RspCode'] === '00',
            'message'  => $result['Message'],
        ]);
    }

    // VNPay server gọi server (nguồn sự thật) — phải trả đúng format RspCode
    public function vnpayIpn(Request $request)
    {
        return response()->json((new PaymentService())->handleVnpayCallback($request->query()));
    }
}
