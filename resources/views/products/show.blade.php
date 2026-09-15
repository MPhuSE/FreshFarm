@extends('layouts.storefront')

@section('title', 'Chi tiết sản phẩm')
@section('page', 'product')

@section('content')
<main id="live-main">

    <div class="container">
        <nav class="breadcrumbs breadcrumbs--page" aria-label="Đường dẫn">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span>/</span>
            <a href="{{ url('/products') }}">Sản phẩm</a>
            <span>/</span>
            <span>Chi tiết sản phẩm</span>
        </nav>

        <section class="product-detail">

            <div class="gallery">
                <div class="gallery__thumbs">
                    <button class="is-active">
                        <img src="{{ asset('tv4/assets/images/product-detail-01.png') }}"
                             alt="Hình sản phẩm">
                    </button>

                    <button>
                        <img src="{{ asset('tv4/assets/images/product-detail-02.png') }}"
                             alt="Hình sản phẩm">
                    </button>

                    <button>
                        <img src="{{ asset('tv4/assets/images/product-detail-03.png') }}"
                             alt="Hình sản phẩm">
                    </button>

                    <button>
                        <img src="{{ asset('tv4/assets/images/product-detail-04.png') }}"
                             alt="Hình sản phẩm">
                    </button>
                </div>

                <div class="gallery__main">
                    <img id="main-product-image"
                         src="{{ asset('tv4/assets/images/product-detail-01.png') }}"
                         alt="Sản phẩm">
                </div>
            </div>

            <div class="product-info">

                <span class="stock-pill">
                    <i data-feather="check-circle"></i>
                    Trạng thái sản phẩm
                </span>

                <h1>Tên sản phẩm</h1>

                <div class="product-meta">
                    <span class="stars">
                        <i data-feather="star"></i>
                        <i data-feather="star"></i>
                        <i data-feather="star"></i>
                        <i data-feather="star"></i>
                        <i data-feather="star"></i>
                    </span>

                    <a href="#reviews">Đánh giá</a>
                    <span>Mã sản phẩm</span>
                </div>

                <div class="product-price">
                    <strong>Giá sản phẩm</strong>
                    <span>/ đơn vị</span>
                </div>

                <p class="lead">
                    Mô tả sản phẩm sẽ được lấy từ API.
                </p>

                <dl class="facts">
                    <div>
                        <dt>Nguồn gốc</dt>
                        <dd>---</dd>
                    </div>

                    <div>
                        <dt>Tiêu chuẩn</dt>
                        <dd>---</dd>
                    </div>

                    <div>
                        <dt>Đóng gói</dt>
                        <dd>---</dd>
                    </div>
                </dl>

                <div class="buy-row">

                    <div class="qty-control" aria-label="Số lượng">
                        <button data-qty-minus>
                            <i data-feather="minus"></i>
                        </button>

                        <input id="product-qty"
                               value="1"
                               inputmode="numeric"
                               aria-label="Số lượng">

                        <button data-qty-plus>
                            <i data-feather="plus"></i>
                        </button>
                    </div>

                    <button class="btn btn--primary btn--grow"
                            data-add-cart>
                        Thêm vào giỏ
                    </button>

                </div>

            </div>

        </section>

        <section class="product-tabs" id="reviews">

            <div class="tab-list" role="tablist">
                <button class="is-active" data-tab="description">
                    Mô tả
                </button>

                <button data-tab="reviews">
                    Đánh giá
                </button>

                <button data-tab="origin">
                    Nguồn gốc
                </button>
            </div>

            <div class="tab-panel is-active" data-panel="description">
                <div class="prose">
                    <h2>Mô tả sản phẩm</h2>
                    <p>
                        Nội dung chi tiết sản phẩm sẽ được lấy từ API.
                    </p>
                </div>
            </div>

            <div class="tab-panel" data-panel="reviews">
                <div class="review-list">
                    <!-- Dữ liệu đánh giá sẽ được API đưa vào đây -->
                </div>
            </div>

            <div class="tab-panel" data-panel="origin">
                <div class="prose">
                    <h2>Nguồn gốc</h2>
                    <p>
                        Thông tin nguồn gốc sẽ được lấy từ API.
                    </p>
                </div>
            </div>
        </section>
    </div>
</main>
@endsection