<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\CheckoutRequest;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;

class CheckoutController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    #[OA\Post(
        path: '/api/v1/checkout/preview',
        summary: 'Xem trước kết quả checkout',
        tags: ['Checkout'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['address_id', 'payment_method'],
                properties: [
                    new OA\Property(property: 'address_id', type: 'integer', example: 4),
                    new OA\Property(property: 'payment_method', type: 'string', example: 'cod'),
                    new OA\Property(property: 'coupon_code', type: 'string', example: 'XANH10', nullable: true)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Xem trước kết quả checkout thành công'),
            new OA\Response(response: 400, description: 'Giỏ hàng trống hoặc thay đổi'),
            new OA\Response(response: 422, description: 'Mã giảm giá không hợp lệ')
        ]
    )]

    public function preview(CheckoutRequest $request): JsonResponse
    {
        try {
            $userId = auth () -> id() ?? auth ('sanctum') -> id ();
            $validated = $request -> validated (); 
            $data = $this->checkoutService->preview(
                $userId,
                $validated['address_id'],
                $validated['payment_method'],
                $validated['coupon_code'] ?? null
            );

            return response ( ) -> json ([
                'success' => true,
                'message' => 'Xem trước thành công.',
                'data' => $data,
                'meta' => null,
                'errors' => null
            ], 200);
        } catch (\Exception $e) {
            $errorCode = $e->getCode()  == 422 ? 'INVALID_COUPON' : 'CART_CHANGED';
            $status = is_int($e->getCode()) && $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'error_code' => $errorCode,
                'errors' => null
            ], $e->getCode() ?: 400);

        } 
    }

}
