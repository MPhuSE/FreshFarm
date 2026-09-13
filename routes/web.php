<?php

use Illuminate\Support\Facades\Route;

// TV4: display routes only. API controllers and middleware remain unchanged.
Route::get('/', function () {
    return view('home', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.index');

Route::get('/products', function () {
    return view('products.index', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.shop');

Route::get('/products/{slug}', function ($slug) {
    return view('products.show', ['tv4Preview' => app()->environment('local') && request()->boolean('preview'), 'slug' => $slug]);
})->name('storefront.product');

Route::get('/cart', function () {
    return view('cart.index', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.cart');

Route::get('/checkout', function () {
    return view('checkout.index', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.checkout');

Route::get('/orders', function () {
    return view('orders.index', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.orders');

Route::get('/orders/{order_code}', function ($order_code) {
    return view('orders.show', ['tv4Preview' => app()->environment('local') && request()->boolean('preview'), 'order_code' => $order_code]);
})->name('storefront.order-detail');

Route::get('/login', function () {
    return view('auth.login', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.login');

Route::get('/register', function () {
    return view('auth.register', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.register');

Route::get('/profile', function () {
    return view('auth.profile', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.account');

Route::get('/addresses', function () {
    return view('auth.addresses', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.addresses');

Route::get('/reviews', function () {
    return view('reviews.index', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.review');

Route::get('/ui-states', function () {
    return view('ui-states', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.ui-states');

