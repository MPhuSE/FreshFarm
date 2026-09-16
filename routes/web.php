<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('AThu.login');
});

Route::get('/register', function () {
    return view('AThu.register');
});