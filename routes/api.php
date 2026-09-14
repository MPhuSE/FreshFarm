<?php

use App\Http\Controllers\Api\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\Admin\PostController;
use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\ProductImageController;
use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Customer\CartController;
use App\Http\Controllers\Api\Customer\CheckoutController;
use App\Http\Controllers\Api\Customer\OrderController;
use App\Http\Controllers\Api\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\Public\CategoryController;
use App\Http\Controllers\Api\Public\ProductController;
use App\Http\Controllers\Api\Public\ReviewController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Controllers\Api\BenchmarkController;
use Illuminate\Support\Facades\Route;

// ==========================================
// BENCHMARK & TEST ROUTES
// ==========================================
Route::get('/v1/benchmark/redis-vs-db', [BenchmarkController::class, 'redisVsDb']);

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------
    */
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:auth');
        Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:auth');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    /*
    |--------------------------------------------------------------------
    | CATALOG CÔNG KHAI
    |--------------------------------------------------------------------
    */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);

        /*
        |----------------------------------------------------------------
        | USER PROFILE
        |----------------------------------------------------------------
        */
        Route::prefix('user')->group(function () {
            Route::put('/profile', [ProfileController::class, 'update']);
            Route::apiResource('addresses', UserAddressController::class)->except(['show']);
        });

        /*
        |----------------------------------------------------------------
        | CART / CHECKOUT / ORDERS / REVIEWS (Customer)
        |----------------------------------------------------------------
        */
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'store'])->middleware('throttle:cart');
        Route::patch('/cart/items/{id}', [CartController::class, 'update']);
        Route::delete('/cart/items/{id}', [CartController::class, 'destroy']);

        Route::post('/checkout/preview', [CheckoutController::class, 'preview']);

        Route::post('/orders', [OrderController::class, 'store'])->middleware('throttle:checkout');
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order_code}', [OrderController::class, 'show']);
        Route::post('/orders/{order_code}/cancel', [OrderController::class, 'cancel']);

        Route::post('/reviews', [CustomerReviewController::class, 'store']);

        /*
        |--------------------------------------------------------------------
        | ADMIN
        |--------------------------------------------------------------------
        */
        Route::prefix('admin')->middleware([EnsureUserIsAdmin::class])->group(function () {

            // --- Sản phẩm & Ảnh ---
            Route::get('/products', [AdminProductController::class, 'index']);
            Route::post('/products', [AdminProductController::class, 'store']);
            Route::get('/products/{id}', [AdminProductController::class, 'show']);
            Route::patch('/products/{id}', [AdminProductController::class, 'update']);
            Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
            Route::post('/products/{id}/images', [ProductImageController::class, 'store']);
            Route::patch('/products/{id}/images/reorder', [ProductImageController::class, 'reorder']);

            // --- Bài viết (Posts) ---
            Route::get('/posts', [PostController::class, 'index']);
            Route::post('/posts', [PostController::class, 'store']);
            Route::patch('/posts/{id}', [PostController::class, 'update']);

            // --- Đơn hàng ---
            Route::get('/orders', [AdminOrderController::class, 'index']);
            Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
            Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
            Route::patch('/orders/{id}/payment-status', [AdminOrderController::class, 'updatePaymentStatus']);

            // --- Người dùng ---
            Route::patch('/users/{user}/status', [UserController::class, 'lock']);
            Route::patch('/users/{user}/role', [UserController::class, 'changeRole']);

            // --- Báo cáo ---
            Route::get('/reports/summary', [ReportController::class, 'summary']);
        });
    });
});
