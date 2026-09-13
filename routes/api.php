<?php

use App\Http\Controllers\Api\Admin\ReportController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\UserAddressController;
use App\Http\Middleware\EnsureUserIsAdmin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        
        Route::prefix('user')->group(function () {
            Route::put('/profile', [ProfileController::class, 'update']);
            Route::apiResource('addresses', UserAddressController::class)->except(['show']);
        });

        Route::prefix('admin')->middleware([EnsureUserIsAdmin::class])->group(function () {
            Route::patch('/users/{user}/status', [UserController::class, 'lock']);
            Route::patch('/users/{user}/role', [UserController::class, 'changeRole']);
            Route::get('/reports/summary', [ReportController::class, 'summary']);
        });
    });
});
