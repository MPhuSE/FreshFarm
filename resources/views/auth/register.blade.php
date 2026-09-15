@extends('layouts.storefront')

@section('title', 'Đăng ký')
@section('page', 'register')
@section('content')
<main class="auth-page">

    <section class="auth-card">

        <div class="auth-card__intro auth-card__intro--register">

            <span class="eyebrow">
                Bắt đầu cùng Nông Sản Xanh
            </span>

            <h1>
                Mua nông sản tươi dễ dàng hơn.
            </h1>

            <p>
                Tạo tài khoản để quản lý đơn hàng,
                địa chỉ và trải nghiệm mua sắm.
            </p>

        </div>


        <form class="auth-form" data-register-form>

            <div>

                <h2>Tạo tài khoản</h2>

                <p>
                    Đã là thành viên?
                    <a href="{{ url('/login') }}">
                        Đăng nhập
                    </a>
                </p>

            </div>


            <label>
                Họ và tên

                <input
                    type="text"
                    name="full_name"
                    placeholder="Nhập họ và tên"
                    required>
            </label>


            <div class="form-grid">

                <label>
                    Email

                    <input
                        type="email"
                        name="email"
                        placeholder="Nhập email"
                        required>
                </label>


                <label>
                    Số điện thoại

                    <input
                        type="tel"
                        name="phone"
                        placeholder="Nhập số điện thoại"
                        required>
                </label>

            </div>


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


            <label>
                Xác nhận mật khẩu

                <input
                    type="password"
                    name="password_confirmation"
                    placeholder="Nhập lại mật khẩu"
                    required
                    minlength="8">
            </label>


            <label class="check-inline">

                <input
                    type="checkbox"
                    required>

                Tôi đồng ý với điều khoản sử dụng
                và chính sách bảo mật.

            </label>


            <div
                class="alert alert--error"
                data-form-error
                hidden>
            </div>


            <button
                class="btn btn--primary btn--block"
                type="submit">

                Tạo tài khoản

            </button>

        </form>

    </section>

</main>
@endsection