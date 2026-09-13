@extends('layouts.storefront')

@section('title', 'Hồ sơ')
@section('page', 'account')

@section('content')
<main id="live-main">

    <section class="page-hero page-hero--compact">
        <div class="container">

            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <span>Tài khoản</span>
            </nav>

            <h1>Tài khoản của tôi</h1>

        </div>
    </section>


    <section class="section container account-layout">

        <aside data-account-nav></aside>


        <div class="account-content">

            <div class="panel-heading">

                <div>
                    <span class="eyebrow">Thông tin cá nhân</span>
                    <h2>Hồ sơ của bạn</h2>
                </div>

                <span class="status-badge">
                    Trạng thái tài khoản
                </span>

            </div>


            <form class="panel form-panel" data-save-form>

                <div class="profile-top">

                    <div class="avatar avatar--large">
                        --
                    </div>

                    <div>
                        <strong>Họ và tên</strong>
                        <p>Thông tin tài khoản</p>
                    </div>

                </div>


                <div class="form-grid">

                    <label>
                        Họ và tên

                        <input
                            type="text"
                            id="full_name"
                            placeholder="Họ và tên">
                    </label>


                    <label>
                        Số điện thoại

                        <input
                            type="tel"
                            id="phone"
                            placeholder="Số điện thoại">
                    </label>

                </div>


                <label>
                    Email

                    <input
                        type="email"
                        id="email"
                        placeholder="Email"
                        disabled>

                    <small>
                        Email dùng để đăng nhập.
                    </small>

                </label>


                <div class="form-actions">

                    <button
                        class="btn btn--primary"
                        type="submit">
                        Lưu thay đổi
                    </button>

                    <button
                        class="btn btn--outline"
                        type="button">
                        Hủy
                    </button>

                </div>

            </form>

        </div>

    </section>

</main>
@endsection