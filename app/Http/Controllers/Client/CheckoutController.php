<?php

namespace App\Http\Controllers\Client;

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
