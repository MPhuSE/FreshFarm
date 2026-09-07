<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\Controller;

Route::prefix('v1') -> group(function () {
    Route::middleware(['auth']) -> group(function () { 
        Route::get('cart', [Controller::class, 'index']);
        Route::post('/cart/items', [Controller::class, 'store']);
        Route::put('/cart/items/{id}', [Controller::class, 'update']);
        Route::delete('/cart/items/{id}', [Controller::class, 'destroy']);
    });
});