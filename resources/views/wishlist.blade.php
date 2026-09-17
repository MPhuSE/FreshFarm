@extends('layouts.storefront')

@section('title', 'Sản phẩm yêu thích')
@section('page', 'wishlist')

@section('content')

<div class="container section">
    <nav class="breadcrumbs">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span>/</span>
        <a href="{{ url('/profile') }}">Tài khoản</a>
        <span>/</span>
        <span>Yêu thích</span>
    </nav>

    <div class="account-layout">
        <div data-account-nav></div>
        
        <div class="account-content">
            <h1>Sản phẩm yêu thích</h1>
            <p>Các sản phẩm bạn đã lưu để mua sau.</p>
            
            <div id="wishlist-results" aria-live="polite">
                <div class="product-grid">
                    @for($i=0; $i<4; $i++)
                    <div class="skeleton-product">
                        <div class="skeleton-product__img"></div>
                        <div class="skeleton-product__body">
                            <div class="skeleton-line skeleton-line--short"></div>
                            <div class="skeleton-line"></div>
                            <div class="skeleton-line skeleton-line--mid"></div>
                            <div class="skeleton-line skeleton-line--price"></div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
