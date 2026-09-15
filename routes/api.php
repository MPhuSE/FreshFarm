<?php

use App\Http\Controllers\Api\V1\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\V1\Admin\ProductImageController;
use App\Http\Controllers\Api\V1\Admin\ReportController;
use App\Http\Controllers\Api\V1\Admin\UserController as AdminUserController;
use App\Http\Controllers\Api\V1\Public\CategoryController;
use App\Http\Controllers\Api\V1\Public\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    /*
    |--------------------------------------------------------------------
    | CATALOG CÔNG KHAI — TV2 (bạn)
    |--------------------------------------------------------------------
    */
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/products', [ProductController::class, 'index']);
    Route::get('/products/{slug}', [ProductController::class, 'show']);

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