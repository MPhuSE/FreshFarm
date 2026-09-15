@extends('layouts.storefront')

@section('title', 'Nông Sản Xanh')
@section('page', 'home')

@section('content')

<main id="live-main">

    <!-- Hero -->
    <section class="hero container">
        <div class="hero__content">
            <span class="eyebrow">
                Tươi mỗi ngày
            </span>
            <h1>
                Nông sản Việt
                <br>
                <span>tươi từ vườn.</span>
            </h1>
            <p>
                Chọn rau củ, trái cây và đặc sản theo mùa
                với nguồn gốc minh bạch.
            </p>
            <div class="hero__actions">
                <a class="btn btn--primary" href="{{ url('/products') }}">
                    Mua sắm ngay
                    <span aria-hidden="true">
                        <i data-feather="arrow-right"></i>
                    </span>
                </a>
                <a class="text-link" href="#featured">
                    Xem sản phẩm nổi bật
                </a>
            </div>
        </div>

        <div class="hero__visual">
            <!-- Đã chuẩn hóa đường dẫn ảnh Hero -->
            <img src="{{ asset('images/hero.png') }}" alt="Nông sản tươi">
            <div class="hero__badge">
                Nguồn gốc rõ ràng
            </div>
        </div>
    </section>

    <!-- Cam kết -->
    <section class="benefits container" aria-label="Cam kết dịch vụ">
        <article>
            <span class="benefit-icon">
                <i data-feather="truck"></i>
            </span>
            <div>
                <strong>Giao hàng nhanh</strong>
                <small>Tiện lợi cho khách hàng</small>
            </div>
        </article>

        <article>
            <span class="benefit-icon">
                <i data-feather="check"></i>
            </span>
            <div>
                <strong>Đổi trả dễ dàng</strong>
                <small>Hỗ trợ khi sản phẩm không đạt</small>
            </div>
        </article>

        <article>
            <span class="benefit-icon">
                <i data-feather="feather"></i>
            </span>
            <div>
                <strong>Tươi sạch mỗi ngày</strong>
                <small>Kiểm tra chất lượng sản phẩm</small>
            </div>
        </article>

        <article>
            <span class="benefit-icon">
                <i data-feather="shield"></i>
            </span>
            <div>
                <strong>Thanh toán an toàn</strong>
                <small>Nhiều phương thức thanh toán</small>
            </div>
        </article>
    </section>

    <!-- Sản phẩm nổi bật -->
    <section class="section container" id="featured">
        <div class="section-heading">
            <div>
                <span class="eyebrow">
                    Lựa chọn hôm nay
                </span>
                <h2>
                    Sản phẩm nổi bật
                </h2>
            </div>
            <a class="text-link" href="{{ url('/products') }}">
                Xem tất cả
                <span aria-hidden="true">
                    <i data-feather="arrow-right"></i>
                </span>
            </a>
        </div>
        <!-- Backend/API sẽ đưa sản phẩm vào đây -->
        <div class="product-grid" data-product-grid="featured">
        </div>
    </section>

    <!-- Story -->
    <section class="story-strip">
        <div class="container story-strip__inner">
            <div>
                <span class="eyebrow eyebrow--light">
                    Từ nông trại đến bàn ăn
                </span>
                <h2>
                    Mỗi sản phẩm đều có một hành trình rõ ràng.
                </h2>
            </div>
            <p>
                Thông tin sản phẩm, nguồn gốc và tồn kho
                sẽ được hiển thị từ hệ thống.
            </p>
            <a class="btn btn--light" href="{{ url('/products') }}">
                Khám phá sản phẩm
            </a>
        </div>
    </section>
</main>

@endsection