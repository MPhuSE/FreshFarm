@extends('layouts.storefront')

@section('title', 'Giỏ hàng')
@section('page', 'cart')

@section('content')
<main id="live-main">

    <section class="page-hero page-hero--compact">
        <div class="container">
            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <span>Giỏ hàng</span>
            </nav>

            <div class="page-hero__row">
                <h1>Giỏ hàng của bạn</h1>
                <p>Kiểm tra sản phẩm trước khi thanh toán</p>
            </div>
        </div>
    </section>

    <section class="section container cart-layout">

        <div class="cart-list panel">

            <div class="cart-list__head">
                <span>Sản phẩm</span>
                <span>Đơn giá</span>
                <span>Số lượng</span>
                <span>Thành tiền</span>
            </div>

            <!-- Sản phẩm sẽ được API đưa vào đây -->
            <div id="cart-items">
                <div class="empty-state">
                    <h2>Giỏ hàng</h2>
                    <p>Dữ liệu sản phẩm sẽ được tải từ API.</p>
                </div>
            </div>

            <div class="cart-list__footer">
                <a class="btn btn--outline" href="{{ url('/products') }}">
                    <i data-feather="arrow-left" aria-hidden="true"></i>
                    Tiếp tục mua
                </a>

                <button class="link-button" data-update-cart>
                    Cập nhật giỏ hàng
                </button>
            </div>

        </div>

        <aside class="order-summary panel">

            <h2>Tóm tắt đơn hàng</h2>

            <dl>

                <div>
                    <dt>Tạm tính</dt>
                    <dd data-subtotal>---</dd>
                </div>

                <div>
                    <dt>Giảm giá</dt>
                    <dd data-discount>---</dd>
                </div>

                <div>
                    <dt>Phí giao hàng</dt>
                    <dd data-shipping-fee>---</dd>
                </div>

                <div class="order-total">
                    <dt>Tổng cộng</dt>
                    <dd data-grand-total>---</dd>
                </div>

            </dl>

            <div class="coupon">
                <input id="coupon-code" placeholder="Mã giảm giá">
                <button class="btn btn--outline" data-apply-coupon>
                    Áp dụng
                </button>
            </div>

            <a class="btn btn--primary btn--block"
               href="{{ url('/checkout') }}">
                Tiến hành thanh toán
            </a>

            <p class="summary-note">
                Giá và tồn kho sẽ được kiểm tra lại ở bước tiếp theo.
            </p>

        </aside>

    </section>

</main>
@endsection