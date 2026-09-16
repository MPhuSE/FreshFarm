<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\PlaceOrderRequest;
use App\Models\Order;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    protected CheckoutService $checkoutService;

    public function __construct(CheckoutService $checkoutService)
    {
        $this->checkoutService = $checkoutService;
    }

    #[OA\Post(
        path: '/api/v1/orders',
        summary: 'Tạo đơn hàng mới',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['address_id', 'payment_method', 'idempotency_key'],
                properties: [
                    new OA\Property(property: 'address_id', type: 'integer', example: 4),
                    new OA\Property(property: 'payment_method', type: 'string', example: 'cod'),
                    new OA\Property(property: 'coupon_code', type: 'string', example: 'XANH10', nullable: true),
                    new OA\Property(property: 'note', type: 'string', example: 'Giao giờ hành chính', nullable: true),
                    new OA\Property(property: 'idempotency_key', type: 'string', example: '41d3345c-8731-4ca6-8b8d-22c899b5a100'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Tạo đơn hàng thành công'),
            new OA\Response(response: 409, description: 'Sản phẩm hết hàng / Lỗi giỏ hàng'),
        ]
    )]
    public function store(PlaceOrderRequest $request): JsonResponse
    {
        try {
            $userId = auth()->id() ?? auth('sanctum')->id();
            $validated = $request->validated();

            $data = $this->checkoutService->placeOrder($userId, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Tạo đơn hàng thành công.',
                'data' => $data,
                'meta' => null,
                'errors' => null,
            ], 201);
        } catch (\Throwable $e) {
            $errorCode = $e->getCode() == 409 ? 'OUT_OF_STOCK' : 'VALIDATION_ERROR';
            $status = is_int($e->getCode()) && $e->getCode() >= 400 && $e->getCode() < 600 ? $e->getCode() : 400;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'data' => null,
                'error_code' => $errorCode,
                'errors' => null,
            ], $e->getCode() ?: 400);
        }
    }

    #[OA\Get(
        path: '/api/v1/orders',
        summary: 'Danh sách đơn hàng của khách',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string', example: 'pending')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 1)),
            new OA\Parameter(name: 'per_page', in: 'query', required: false, schema: new OA\Schema(type: 'integer', example: 20)),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Danh sách đơn của khách thành công'),
        ]
    )]
    public function index(Request $request): JsonResponse
    {
        $userId = auth()->id() ?? auth('sanctum')->id();
        $query = Order::where('user_id', $userId)->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $perPage = $request->input('per_page', 20);
        $orders = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'message' => 'Danh sách đơn của khách thành công.',
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
                'last_page' => $orders->lastPage(),
                'from' => $orders->firstItem(),
                'to' => $orders->lastItem(),
            ],
            'errors' => null,
        ]);
    }

    #[OA\Get(
        path: '/api/v1/orders/{order_code}',
        summary: 'Chi tiết đơn hàng của khách',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'order_code', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Thành công'),
            new OA\Response(response: 404, description: 'Không tìm thấy đơn hàng'),
        ]
    )]
    public function show($order_code): JsonResponse
    {
        $userId = auth()->id() ?? auth('sanctum')->id();
        $order = Order::with('items')->where('user_id', $userId)->where('order_code', $order_code)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.',
                'data' => null,
                'error_code' => 'ORDER_NOT_FOUND',
                'errors' => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Chi tiết đơn của khách thành công.',
            'data' => $order,
            'meta' => null,
            'errors' => null,
        ]);
    }

    #[OA\Post(
        path: '/api/v1/orders/{order_code}/cancel',
        summary: 'Khách tự hủy đơn hàng',
        tags: ['Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'order_code', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: false,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'reason', type: 'string', example: 'Tôi muốn thay đổi sản phẩm'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Khách hủy đơn thành công'),
            new OA\Response(response: 404, description: 'Không tìm thấy đơn hàng'),
            new OA\Response(response: 409, description: 'Trạng thái đơn không cho phép hủy'),
        ]
    )]
    public function cancel(Request $request, $order_code): JsonResponse
    {
        $userId = auth()->id() ?? auth('sanctum')->id();
        $order = Order::with('items')->where('user_id', $userId)->where('order_code', $order_code)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng.',
                'data' => null,
                'error_code' => 'ORDER_NOT_FOUND',
                'errors' => null,
            ], 404);
        }

        // Quy tắc: Khách chỉ được hủy khi đơn ở trạng thái pending hoặc confirmed
        if (! in_array($order->status, ['pending', 'confirmed'])) {
            return response()->json([
                'success' => false,
                'message' => 'Trạng thái đơn không cho phép hủy.',
                'data' => null,
                'error_code' => 'INVALID_ORDER_TRANSITION',
                'errors' => null,
            ], 409);
        }

        $order->status = 'cancelled';
        $order->save();

        return response()->json([
            'success' => true,
            'message' => 'Khách hủy đơn thành công.',
            'data' => $order,
            'meta' => null,
            'errors' => null,
        ]);
    }
}
