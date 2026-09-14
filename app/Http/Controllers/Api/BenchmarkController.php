<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class BenchmarkController extends Controller
{
    public function redisVsDb(): JsonResponse
    {
        $iterations = 100;

        // 1. Test Database Query (No Cache)
        $startDb = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $categories = Category::query()
                ->active()
                ->orderBy('sort_order')
                ->get();
        }
        $endDb = microtime(true);
        $dbTimeMs = round(($endDb - $startDb) * 1000, 2);

        // Populate Cache first
        Cache::remember('catalog:categories:active:benchmark', 60, function () {
            return Category::query()
                ->active()
                ->orderBy('sort_order')
                ->get();
        });

        // 2. Test Redis Cache
        $startRedis = microtime(true);
        for ($i = 0; $i < $iterations; $i++) {
            $categories = Cache::get('catalog:categories:active:benchmark');
        }
        $endRedis = microtime(true);
        $redisTimeMs = round(($endRedis - $startRedis) * 1000, 2);

        return response()->json([
            'iterations' => $iterations,
            'results' => [
                'database_time_ms' => $dbTimeMs,
                'redis_time_ms' => $redisTimeMs,
                'difference_ms' => round($dbTimeMs - $redisTimeMs, 2),
                'speedup_factor' => $redisTimeMs > 0 ? round($dbTimeMs / $redisTimeMs, 2) . 'x' : 'N/A'
            ],
            'message' => 'Benchmark completed successfully.'
        ]);
    }
}
