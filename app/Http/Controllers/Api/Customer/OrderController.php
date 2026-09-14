<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Exception;

class OrderController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        try {
            $userId = auth () -> id() ?? auth ('sanctum') -> id (); 
            $validated = $request->validated(); 
            
            $data = $this->checkoutService->placeOrder($userId, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công.',
                'data' => $data,
                'meta' => null,
                'errors' => null
            ], 201);
        } catch (\Throwable $e) {
            $errorCode = $e->getCode() == 409 ? 'OUT_OF_STOCK' : 'VALIDATION_ERROR';
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