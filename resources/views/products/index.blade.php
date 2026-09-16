@extends('layouts.storefront')
@section('title', 'Sản phẩm')
@section('page', 'shop')
@section('content')
<main id="live-main">

    <section class="page-hero page-hero--compact">
        <div class="container">
            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <span>Sản phẩm</span>
            </nav>

            <div class="page-hero__row">
                <div>
                    <h1>Cửa hàng</h1>
                    <p>Nông sản tươi sạch — từ vườn đến bàn ăn</p>
                </div>
            </div>
        </div>
    </section>

    <section class="section container shop-layout">

        {{-- Sidebar Filters --}}
        <aside class="filters" id="shop-filters">

            <div class="filter-head">
                <h2>Bộ lọc</h2>
                <button class="link-button" data-clear-filters>Xóa tất cả</button>
            </div>

            {{-- Danh mục --}}
            <div class="filter-group">
                <h3>Danh mục</h3>
                <div id="category-filters">
                    <div class="skeleton-line" style="margin:8px 0"></div>
                    <div class="skeleton-line skeleton-line--mid" style="margin:8px 0"></div>
                    <div class="skeleton-line skeleton-line--short" style="margin:8px 0"></div>
                </div>
            </div>

            {{-- Khoảng giá --}}
            <div class="filter-group">
                <h3>Khoảng giá</h3>
                <div class="price-range">
                    <input type="number" placeholder="Từ" id="min-price" min="0">
                    <span>–</span>
                    <input type="number" placeholder="Đến" id="max-price" min="0">
                </div>
            </div>

            {{-- Còn hàng --}}
            <div class="filter-group">
                <label class="switch-row">
                    <span>
                        <strong>Chỉ còn hàng</strong>
                        <small>Ẩn sản phẩm hết hàng</small>
                    </span>
                    <input type="checkbox" id="in-stock-filter">
                    <i></i>
                </label>
            </div>

            <button class="btn btn--primary btn--block" data-apply-filters>
                Áp dụng
            </button>

        </aside>

        {{-- Main Content --}}
        <div class="catalog">

            {{-- Toolbar --}}
            <div class="catalog-toolbar">
                <button class="btn btn--outline filter-toggle" id="filter-toggle">
                    <i data-feather="sliders" aria-hidden="true"></i>
                    Bộ lọc
                </button>

                <p id="product-count">Đang tải sản phẩm...</p>

                <label class="inline-select">
                    Sắp xếp

                    <select id="sort-select">
                        <option value="">Mới nhất</option>
                        <option value="price_asc">Giá tăng dần</option>
                        <option value="price_desc">Giá giảm dần</option>
                        <option value="name_asc">Tên A → Z</option>
                    </select>
                </label>
            </div>

            {{-- Category chips (mobile-friendly) --}}
            <div class="category-chips" id="category-chips"></div>

            {{-- Product grid with skeleton --}}
            <div class="product-grid product-grid--catalog" id="product-grid">
                {{-- Skeleton cards while loading --}}
                @for ($i = 0; $i < 6; $i++)
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

            {{-- Pagination --}}
            <div class="pagination" id="pagination"></div>

        </div>

    </section>

</main>
@endsection