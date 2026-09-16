<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderPaymentRequest;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Inventory;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use OpenApi\Attributes as OA;

class OrderController extends Controller
{
    #[OA\Get(
        path: '/api/v1/admin/orders',
        summary: 'Admin - danh sách đơn hàng',
        tags: ['Admin Orders'],
        security: [['bearerAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'status', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'payment_status', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'page', in: 'query', required: false, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [new OA\Response(response: 200, description: 'Danh sách đơn thành công')]
    )]
    public function index(Request $request): JsonResponse
    {
        $query = Order::query()->orderBy('created_at', 'desc');

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $orders = $query->paginate($request->input('per_page', 20));

        return response()->json([
            'success' => true,
            'message' => 'Admin - danh sách đơn thành công.',
            'data' => $orders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'per_page' => $orders->perPage(),
                'total' => $orders->total(),
            ],
            'errors' => null,
        ]);
    }

    #[OA\Get(
        path: '/api/v1/admin/orders/{order_code}',
        summary: 'Admin - chi tiết đơn hàng',
        tags: ['Admin Orders'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'order_code', in: 'path', required: true, schema: new OA\Schema(type: 'string'))],
        responses: [new OA\Response(response: 200, description: 'Chi tiết đơn hàng')]
    )]
    public function show(string $order_code): JsonResponse
    {
        $order = Order::with('items')->where('order_code', $order_code)->first();

        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng.', 'error_code' => 'ORDER_NOT_FOUND'], 404);
        }

        return response()->json(['success' => true, 'message' => 'Admin - chi tiết đơn thành công.', 'data' => $order]);
    }

    #[OA\Patch(
        path: '/api/v1/admin/orders/{id}/status',
        summary: 'Admin - cập nhật trạng thái đơn',
        tags: ['Admin Orders'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'status', type: 'string', example: 'confirmed'),
                new OA\Property(property: 'note', type: 'string', example: 'Khách đã chuyển khoản'),
            ])
        ),
        responses: [
            new OA\Response(response: 200, description: 'Cập nhật thành công'),
            new OA\Response(response: 409, description: 'Trạng thái không hợp lệ'),
        ]
    )]
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): JsonResponse
    {
        $order = Order::with('items')->find($id);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Lỗi', 'error_code' => 'ORDER_NOT_FOUND'], 404);
        }

        $newStatus = $request->status;
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['shipping', 'cancelled'],
            'shipping' => ['delivered'],
            'delivered' => ['returned'],
        ];

        if (! isset($validTransitions[$order->status]) || ! in_array($newStatus, $validTransitions[$order->status])) {
            return response()->json(['success' => false, 'message' => 'Trạng thái không hợp lệ.', 'error_code' => 'INVALID_ORDER_TRANSITION'], 409);
        }

        DB::transaction(function () use ($order, $newStatus, $request) {
            $order->status = $newStatus;
            if ($request->has('note')) {
                $order->note = rtrim($order->note.' | '.$request->note, ' | ');
            }
            $order->save();

            // nếu Admin hủy đơn, hoàn kho
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    $inventory = Inventory::where('product_id', $item->product_id)->lockForUpdate()->first();
                    if ($inventory) {
                        $inventory->increment('quantity_on_hand', $item->quantity);
                    }
                }
            }
        });

        return response()->json(['success' => true, 'message' => 'Cập nhật trạng thái thành công.', 'data' => $order->fresh()]);
    }

    #[OA\Patch(
        path: '/api/v1/admin/orders/{id}/payment-status',
        summary: 'Admin - cập nhật thanh toán',
        tags: ['Admin Orders'],
        security: [['bearerAuth' => []]],
        parameters: [new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer'))],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(properties: [
                new OA\Property(property: 'payment_status', type: 'string', example: 'paid'),
                new OA\Property(property: 'transaction_ref', type: 'string', example: 'VCB-123456'),
            ])
        ),
        responses: [new OA\Response(response: 200, description: 'Cập nhật thanh toán thành công')]
    )]
    public function updatePaymentStatus(UpdateOrderPaymentRequest $request, int $id): JsonResponse
    {
        $order = Order::find($id);
        if (! $order) {
            return response()->json(['success' => false, 'message' => 'Lỗi', 'error_code' => 'ORDER_NOT_FOUND'], 404);
        }

        if ($order->payment_status === 'paid' && $request->payment_status !== 'refunded') {
            return response()->json(['success' => false, 'message' => 'Đã thanh toán.', 'error_code' => 'INVALID_PAYMENT_STATE'], 409);
        }

        $order->payment_status = $request->payment_status;
        if ($request->has('transaction_ref')) {
            $order->transaction_ref = $request->transaction_ref;
        }
        $order->save();

        return response()->json(['success' => true, 'message' => 'Cập nhật thanh toán thành công.', 'data' => $order]);
    }
}
