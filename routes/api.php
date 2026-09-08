<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\CartController;

Route::prefix('v1') -> group(function () {
    Route::middleware(['auth']) -> group(function () { 
        Route::get('cart', [CartController::class, 'index']);
        Route::post('/cart/items', [CartController::class, 'store']);
        Route::patch('/cart/items/{id}', [CartController::class, 'update']);
        Route::delete('/cart/items/{id}', [CartController::class, 'destroy']);
    });
}); 