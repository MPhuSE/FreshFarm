<?php

use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 'Admin Dashboard';
});

Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
