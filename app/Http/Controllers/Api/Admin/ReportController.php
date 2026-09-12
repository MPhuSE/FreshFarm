<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ReportController extends Controller
{
    #[OA\Get(
        path: '/api/admin/reports/summary',
        summary: 'Lấy báo cáo tổng quan',
        tags: ['Report'],
        security: [['bearerAuth' => []]],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Thành công',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'total_users', type: 'integer', example: 150),
                        new OA\Property(property: 'total_orders', type: 'integer', example: 320),
                        new OA\Property(property: 'total_revenue', type: 'number', format: 'float', example: 15000000),
                    ]
                )
            ),
            new OA\Response(response: 403, description: 'Không có quyền'),
        ]
    )]
    public function summary(): JsonResponse
    {
        $totalUsers = User::count();
        $totalOrders = Order::count();
        $totalRevenue = Order::where('status', 'completed')->sum('grand_total');

        return response()->json([
            'total_users' => $totalUsers,
            'total_orders' => $totalOrders,
            'total_revenue' => (float) $totalRevenue,
        ]);
    }
}
