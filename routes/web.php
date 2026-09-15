<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/admin/orders', function () {
    return view('admin.orders.index');
})->name('admin.orders.index');

Route::get('/admin/system/permissions', function () {
    return view('admin.system.permissions');
})->name('admin.system.permissions');

Route::get('/admin/system/reports', function () {
    return view('admin.system.reports');
})->name('admin.system.reports');

Route::get('/admin/system/users', function () {
    return view('admin.system.users');
})->name('admin.system.users');

Route::get('/admin/orders/{id}', function ($id) {
    return view('admin.orders.detail', ['id' => $id]);
})->name('admin.orders.detail');