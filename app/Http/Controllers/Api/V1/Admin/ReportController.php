<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function summary(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'from' => ['required', 'date'],
            'to' => ['required', 'date', 'after_or_equal:from'],
        ]);

        $totalUsers = User::query()->count();
        $totalOrders = 0;
        $totalRevenue = 0;

        if (Schema::hasTable('orders')) {
            $orders = app('db')->table('orders')
                ->whereBetween('created_at', [$validated['from'].' 00:00:00', $validated['to'].' 23:59:59']);

            $totalOrders = (clone $orders)->count();
            $totalRevenue = (float) (clone $orders)->sum('grand_total');
        }

        return response()->json([
            'success' => true,
            'message' => 'Báo cáo tổng hợp thành công.',
            'data' => [
                'total_users' => $totalUsers,
                'total_orders' => $totalOrders,
                'total_revenue' => $totalRevenue,
                'revenue_series' => [],
                'top_products' => [],
            ],
            'meta' => null,
            'errors' => null,
        ]);
    }
}
