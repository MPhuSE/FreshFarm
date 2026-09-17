@extends('layouts.storefront')

@section('title', 'Đánh giá sản phẩm')
@section('page', 'review')

@section('content')

<div class="container section">
    <nav class="breadcrumbs">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span>/</span>
        <a href="{{ url('/profile') }}">Tài khoản</a>
        <span>/</span>
        <span>Đánh giá</span>
    </nav>
</div>

<main id="live-main">
    <div class="container section text-center py-12">
        <p class="text-slate-500">Đang tải...</p>
    </div>
</main>

@endsection
