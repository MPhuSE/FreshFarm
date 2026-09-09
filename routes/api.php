<?php

use App\Http\Controllers\Api\V1\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Api\V1\Admin\PostController;
use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\ProductImageController;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Customer\CartController;
use App\Http\Controllers\Api\V1\Customer\CheckoutController;
use App\Http\Controllers\Api\V1\Customer\OrderController;
use App\Http\Controllers\Api\V1\Customer\ReviewController as CustomerReviewController;
use App\Http\Controllers\Api\V1\Public\AuthController;
use App\Http\Controllers\Api\V1\Public\CategoryController;
use App\Http\Controllers\Api\V1\Public\ProductController;
use App\Http\Controllers\Api\V1\Public\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------
    | AUTH — TV1 (chưa thuộc scope của bạn, để placeholder)
    |--------------------------------------------------------------------
    */
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);

        /*
        |----------------------------------------------------------------
        | CART / CHECKOUT / ORDERS (Customer) — TV3
        |----------------------------------------------------------------
        */
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'store']);
        Route::patch('/cart/items/{id}', [CartController::class, 'update']);
        Route::delete('/cart/items/{id}', [CartController::class, 'destroy']);

        Route::post('/checkout/preview', [CheckoutController::class, 'preview']);

        Route::post('/orders', [OrderController::class, 'store']);
        Route::get('/orders', [OrderController::class, 'index']);
        Route::get('/orders/{order_code}', [OrderController::class, 'show']);
        Route::post('/orders/{order_code}/cancel', [OrderController::class, 'cancel']);

        /*
        |----------------------------------------------------------------
        | REVIEW — TV2 (bạn) — tạo đánh giá
        |----------------------------------------------------------------
        */
        Route::post('/reviews', [CustomerReviewController::class, 'store']);
    });

    /*
    |--------------------------------------------------------------------
    | CATALOG CÔNG KHAI — TV2 (bạn)
    |--------------------------------------------------------------------
    */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);
    Route::get('/products/{id}/reviews', [ReviewController::class, 'index']);

    /*
    |--------------------------------------------------------------------
    | ADMIN — cần auth:sanctum + role:staff,admin
    |--------------------------------------------------------------------
    */
    Route::middleware(['auth:sanctum', 'role:staff,admin'])->prefix('admin')->group(function () {

        // --- Sản phẩm & Ảnh — TV2 (bạn) ---
        Route::get('/products', [AdminProductController::class, 'index']);
        Route::post('/products', [AdminProductController::class, 'store']);
        Route::get('/products/{id}', [AdminProductController::class, 'show']);
        Route::patch('/products/{id}', [AdminProductController::class, 'update']);
        Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
        Route::post('/products/{id}/images', [ProductImageController::class, 'store']);
        Route::patch('/products/{id}/images/reorder', [ProductImageController::class, 'reorder']);

        // --- Bài viết (Posts) — TV2 (bạn, theo phân công thực tế) ---
        Route::get('/posts', [PostController::class, 'index']);
        Route::post('/posts', [PostController::class, 'store']);
        Route::patch('/posts/{id}', [PostController::class, 'update']);

        // --- Đơn hàng — TV3 ---
        Route::get('/orders', [AdminOrderController::class, 'index']);
        Route::get('/orders/{id}', [AdminOrderController::class, 'show']);
        Route::patch('/orders/{id}/status', [AdminOrderController::class, 'updateStatus']);
        Route::patch('/orders/{id}/payment-status', [AdminOrderController::class, 'updatePaymentStatus']);

        // --- Người dùng — TV1 (chỉ Admin) ---
        Route::middleware('role:admin')->group(function () {
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::patch('/users/{id}/status', [AdminUserController::class, 'updateStatus']);
            Route::patch('/users/{id}/role', [AdminUserController::class, 'updateRole']);
        });

        // --- Báo cáo — TV1 ---
        Route::get('/reports/summary', [ReportController::class, 'summary']);
    });
});