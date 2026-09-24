@extends('layouts.storefront')

@section('title', 'Thanh toán')
@section('page', 'checkout')

@section('content')
<main id="live-main">

    <section class="page-hero page-hero--compact">
        <div class="container">

            <nav class="breadcrumbs">
                <a href="{{ url('/cart') }}">Giỏ hàng</a>
                <span>/</span>
                <span>Thanh toán</span>
            </nav>

            <div class="page-hero__row">
                <h1>Thanh toán an toàn</h1>

                <div class="checkout-steps">
                    <span class="is-done">1 Giỏ hàng</span>
                    <span class="is-active">2 Thanh toán</span>
                    <span>3 Hoàn tất</span>
                </div>
            </div>

        </div>
    </section>


    <form class="section container checkout-layout" id="checkout-form">

        <div class="checkout-main">

            <!-- Địa chỉ -->
            <section class="panel checkout-section">

                <div class="panel-heading">
                    <div>
                        <span class="step-number">1</span>
                        <h2>Địa chỉ nhận hàng</h2>
                    </div>

                    <a class="text-link" href="{{ url('/addresses') }}">
                        Quản lý địa chỉ
                    </a>
                </div>

                <div id="address-list">
                    <!-- Địa chỉ sẽ được API đưa vào đây -->
                    <div class="empty-state">
                        <p>Địa chỉ nhận hàng</p>
                    </div>
                </div>

            </section>


            <!-- Thanh toán -->
            <section class="panel checkout-section">

                <div class="panel-heading">
                    <div>
                        <span class="step-number">2</span>
                        <h2>Phương thức thanh toán</h2>
                    </div>
                </div>

                <div id="payment-methods">

                    <label class="choice-card">
                        <input type="radio"
                               name="payment_method"
                               value="cod"
                               checked>

                        <span>
                            <strong>Thanh toán khi nhận hàng</strong>
                            <small>COD</small>
                        </span>
                    </label>


                    <label class="choice-card">
                        <input type="radio"
                               name="payment_method"
                               value="bank_transfer">

                        <span>
                            <strong>Chuyển khoản ngân hàng</strong>
                            <small>Chuyển khoản</small>
                        </span>
                    </label>

                    <label class="choice-card">
                        <input type="radio"
                               name="payment_method"
                               value="vnpay">

                        <span>
                            <strong>Thanh toán qua VNPay</strong>
                            <small>ATM/Visa/MasterCard/QRCode</small>
                        </span>
                    </label>

                </div>

            </section>


            <!-- Ghi chú -->
            <section class="panel checkout-section">

                <div class="panel-heading">
                    <div>
                        <span class="step-number">3</span>
                        <h2>Ghi chú giao hàng</h2>
                    </div>
                </div>

                <textarea
                    name="note"
                    rows="4"
                    placeholder="Ghi chú cho đơn hàng..."></textarea>

            </section>

        </div>


        <!-- Tổng đơn -->
        <aside class="order-summary panel checkout-summary">

            <h2>Đơn hàng của bạn</h2>

            <div id="checkout-products"
                 class="summary-products">

                <!-- Sản phẩm sẽ được API đưa vào đây -->

            </div>

            <!-- Áp dụng mã giảm giá -->
            <div class="coupon-section" style="margin-top: 1.5rem; margin-bottom: 1.5rem; display: flex; gap: 0.5rem;">
                <input type="text" id="coupon_code_input" placeholder="Mã giảm giá" class="input" style="flex: 1; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.5rem 1rem; outline: none;">
                <button type="button" id="apply_coupon_btn" class="btn btn--outline" style="white-space: nowrap;">Áp dụng</button>
            </div>


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
                    <dt>Tổng thanh toán</dt>
                    <dd data-grand-total>---</dd>
                </div>

            </dl>


            <div id="checkout-alert"
                 class="alert alert--warning"
                 hidden>
            </div>


            <button class="btn btn--primary btn--block"
                    type="submit">
                Đặt hàng
            </button>


            <label class="check-inline terms">
                <input type="checkbox" required>
                Tôi đồng ý với chính sách mua hàng.
            </label>

        </aside>

    </form>


    <!-- Thông báo thành công -->
    <dialog class="modal" id="success-modal">

        <div class="modal__card success-card">

            <span class="success-icon">
                <i data-feather="check"></i>
            </span>

            <span class="eyebrow">
                Đặt hàng thành công
            </span>

            <h2>
                Đơn hàng đã được ghi nhận.
            </h2>

            <p>
                Thông tin đơn hàng sẽ được hiển thị sau khi API trả về kết quả.
            </p>

            <div>

                <a class="btn btn--primary"
                   href="{{ url('/orders') }}">
                    Xem đơn hàng
                </a>

                <a class="btn btn--outline"
                   href="{{ url('/products') }}">
                    Tiếp tục mua
                </a>

            </div>

        </div>

    </dialog>

</main>
@endsection