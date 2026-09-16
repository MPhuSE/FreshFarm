<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    #[OA\Post(
        path: '/api/v1/payment/vnpay/{order_code}',
        summary: 'Tạo URL thanh toán VNPay',
        tags: ['Payment'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(
                name: 'order_code',
                in: 'path',
                required: true,
                description: 'Mã đơn hàng cần thanh toán',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Tạo URL thành công',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'payment_url', type: 'string', example: 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html?...'),
                    ]
                )
            ),
            new OA\Response(response: 400, description: 'Đơn hàng đã được thanh toán'),
            new OA\Response(response: 404, description: 'Không tìm thấy đơn hàng'),
        ]
    )]
    public function createPaymentUrl($order_code)
    {
        $order = Order::where('order_code', $order_code)->where('user_id', auth()->id())->firstOrFail();

        if ($order->payment_status === 'paid') {
            return response()->json(['success' => false, 'message' => 'Đơn hàng đã được thanh toán.'], 400);
        }

        $url = $this->paymentService->createVnPayUrl($order);

        return response()->json([
            'success' => true,
            'payment_url' => $url,
        ]);
    }

    #[OA\Get(
        path: '/api/v1/payment/vnpay/ipn',
        summary: 'VNPay IPN Webhook (Hệ thống VNPay tự động gọi, FE không cần quan tâm)',
        tags: ['Payment'],
        responses: [
            new OA\Response(response: 200, description: 'Cập nhật trạng thái thành công'),
        ]
    )]
    public function vnpayIpn(Request $request)
    {
        $inputData = $request->all();
        $isValid = $this->paymentService->verifyIpn($inputData);

        if (! $isValid) {
            return response()->json(['RspCode' => '97', 'Message' => 'Invalid signature']);
        }

        $order = Order::where('order_code', $inputData['vnp_TxnRef'])->first();
        if (! $order) {
            return response()->json(['RspCode' => '01', 'Message' => 'Order not found']);
        }

        if ($order->payment_status === 'paid') {
            return response()->json(['RspCode' => '02', 'Message' => 'Order already confirmed']);
        }

        if ($inputData['vnp_ResponseCode'] == '00') {
            $order->payment_status = 'paid';
            $order->save();

            return response()->json(['RspCode' => '00', 'Message' => 'Confirm Success']);
        }

        return response()->json(['RspCode' => '00', 'Message' => 'Payment failed']);
    }
}
