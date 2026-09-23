<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class PerformanceController extends Controller
{
    public function index()
    {
        return view('admin.performance');
    }

    public function runTest(Request $request)
    {
        $type = $request->input('type');
        
        DB::enableQueryLog();
        DB::flushQueryLog();
        
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        if ($type === 'n_plus_one') {
            $products = Product::limit(10)->get();
            $data = [];
            foreach ($products as $product) {
                $data[] = $product->category ? $product->category->name : 'None';
            }
        } elseif ($type === 'eager_loading') {
            $products = Product::with('category')->limit(10)->get();
            $data = [];
            foreach ($products as $product) {
                $data[] = $product->category ? $product->category->name : 'None';
            }
        } elseif ($type === 'no_cache') {
            for ($i = 0; $i < 50; $i++) {
                $products = Product::limit(10)->get();
            }
        } elseif ($type === 'with_cache') {
            for ($i = 0; $i < 50; $i++) {
                $products = Cache::remember('test_products_cache', 60, function () {
                    return Product::limit(10)->get();
                });
            }
        } else {
            return response()->json(['error' => 'Invalid test type'], 400);
        }

        $endMemory = memory_get_usage();
        $endTime = microtime(true);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        return response()->json([
            'execution_time_ms' => round(($endTime - $startTime) * 1000, 2),
            'memory_used_kb' => round(($endMemory - $startMemory) / 1024, 2),
            'queries_count' => count($queries)
        ]);
    }
}
