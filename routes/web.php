<?php

use App\Models\Category;
use App\Models\Page;
use Illuminate\Support\Facades\Route;

// TV4: display routes only. API controllers and middleware remain unchanged.
Route::get('/', function () {
    $categories = Category::where('status', 'active')->orderBy('sort_order')->take(5)->get();

    return view('home', [
        'tv4Preview' => app()->environment('local') && request()->boolean('preview'),
        'categories' => $categories,
    ]);
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

Route::get('/payment/vnpay/return', function () {
    return view('checkout.vnpay-return', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.vnpay-return');

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

Route::get('/review', function () {
    return view('review', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.review');

Route::get('/ui-states', function () {
    return view('ui-states', ['tv4Preview' => app()->environment('local') && request()->boolean('preview')]);
})->name('storefront.ui-states');

Route::view('/gioi-thieu', 'about')->name('storefront.about');

Route::get('/tin-tuc', function () {
    $posts = App\Models\Post::latest()->paginate(12);
    return view('posts.index', ['posts' => $posts]);
})->name('storefront.news');

Route::get('/tin-tuc/{slug}', function ($slug) {
    $post = App\Models\Post::where('slug', $slug)->firstOrFail();
    return view('posts.show', ['post' => $post]);
})->name('storefront.news.show');

Route::get('/p/{slug}', function ($slug) {
    $page = Page::where('slug', $slug)->where('is_published', true)->firstOrFail();

    return view('page', ['page' => $page]);
})->name('storefront.page');

Route::get('/admin/orders', function () {
    return view('admin.orders.index');
})->name('admin.orders.index');

Route::get('/admin/reviews', function () {
    return view('admin.reviews.index');
})->name('admin.reviews.index');

Route::get('/admin/inventory', function () {
    return view('admin.inventory.index');
})->name('admin.inventory.index');

Route::get('/admin/system/permissions', function () {
    return view('admin.system.permissions');
})->name('admin.system.permissions');

Route::get('/admin/system/settings', function () {
    return view('admin.system.settings');
})->name('admin.system.settings');

Route::get('/admin/system/audit-logs', function () {
    return view('admin.system.audit-logs');
})->name('admin.system.audit-logs');

Route::get('/admin/system/pages', function () {
    return view('admin.system.pages');
})->name('admin.system.pages');

Route::get('/admin/system/reports', function () {
    return view('admin.system.reports');
})->name('admin.system.reports');

Route::get('/admin/system/users', function () {
    return view('admin.system.users');
})->name('admin.system.users');

Route::get('/admin/orders/{id}', function ($id) {
    return view('admin.orders.detail', ['id' => $id]);
})->name('admin.orders.detail');
Route::view('/wishlist', 'wishlist')->name('storefront.wishlist');
