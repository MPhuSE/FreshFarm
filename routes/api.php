<?php

use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Controllers\Client\CartController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});

Route::prefix('user')->middleware('auth:sanctum')->group(function () {
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::apiResource('addresses', UserAddressController::class)->except(['show']);
});

Route::prefix('admin')->middleware(['auth:sanctum', EnsureUserIsAdmin::class])->group(function () {
    Route::put('/users/{user}/lock', [UserController::class, 'lock']);
    Route::put('/users/{user}/role', [UserController::class, 'changeRole']);
    Route::get('/reports/summary', [ReportController::class, 'summary']);
});
Route::prefix('v1') -> group(function () {
    Route::middleware(['auth']) -> group(function () { 
        Route::get('cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'store']);
        Route::patch('/cart/items/{id}', [CartController::class, 'update']);
        Route::delete('/cart/items/{id}', [CartController::class, 'destroy']);
    });
}); 
