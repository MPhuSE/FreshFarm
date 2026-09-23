<?php

use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\PerformanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('admin.dashboard.index');
})->name('admin.dashboard.index');

Route::get('/products', [ProductController::class, 'index'])->name('admin.products.index');
Route::get('/products/create', [ProductController::class, 'create'])->name('admin.products.create');
Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('admin.products.edit');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('admin.products.show');
Route::get('/products/{product}/images', [ProductController::class, 'images'])->name('admin.products.images');
Route::get('/categories', function () {
    return view('admin.catalog.categories');
})->name('admin.categories.index');

Route::get('/coupons', function () {
    return view('admin.catalog.coupons');
})->name('admin.coupons.index');

Route::get('/posts', function () {
    return view('admin.posts.index');
})->name('admin.posts.index');

Route::get('/posts/create', function () {
    return view('admin.posts.form');
})->name('admin.posts.create');

Route::get('/posts/{id}/edit', function ($id) {
    $post = App\Models\Post::findOrFail($id);
    return view('admin.posts.form', compact('post'));
})->name('admin.posts.edit');

Route::get('/performance', [PerformanceController::class, 'index'])->name('admin.performance.index');
Route::post('/performance/run', [PerformanceController::class, 'runTest'])->name('admin.performance.run');
