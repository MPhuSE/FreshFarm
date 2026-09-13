@extends('layouts.storefront')

@section('title', 'Đăng nhập')
@section('page', 'login')

@section('content')
<main class="auth-page">

    <section class="auth-card">

        <div class="auth-card__intro">

            <span class="eyebrow">
                Chào mừng bạn quay lại
            </span>

            <h1>
                Đăng nhập để tiếp tục mua sắm.
            </h1>

            <p>
                Theo dõi đơn hàng, lưu địa chỉ giao nhận
                và đánh giá sản phẩm đã mua.
            </p>

            <ul>

                <li>
                    <i data-feather="check"></i>
                    Lịch sử đơn hàng
                </li>

                <li>
                    <i data-feather="check"></i>
                    Thanh toán nhanh hơn
                </li>

                <li>
                    <i data-feather="check"></i>
                    Quản lý tài khoản
                </li>

            </ul>

        </div>


        <form class="auth-form" data-login-form>

            <div>

                <h2>Đăng nhập</h2>

                <p>
                    Chưa có tài khoản?
                    <a href="{{ url('/register') }}">
                        Đăng ký ngay
                    </a>
                </p>

            </div>


            <label>
                Email

                <input
                    type="email"
                    name="email"
                    placeholder="Nhập email"
                    required>
            </label>


            <label>
                Mật khẩu

                <div class="password-field">

                    <input
                        type="password"
                        name="password"
                        placeholder="Nhập mật khẩu"
                        required
                        minlength="8">

                    <button
                        type="button"
                        data-toggle-password
                        aria-label="Hiện mật khẩu">

                        <i data-feather="eye"></i>

                    </button>

                </div>

            </label>


            <div class="form-row">

                <label class="check-inline">

                    <input
                        type="checkbox"
                        name="remember">

                    Ghi nhớ đăng nhập
                </label>
                <a class="text-link" href="#">
                    Quên mật khẩu?
                </a>

            </div>
            <div
                class="alert alert--error"
                data-form-error
                hidden>
            </div>
            <button
                class="btn btn--primary btn--block"
                type="submit">

                Đăng nhập

            </button>

        </form>

    </section>

</main>
@endsection