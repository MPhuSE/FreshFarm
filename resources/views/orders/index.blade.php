@extends('layouts.storefront')

@section('title', 'Đơn hàng')
@section('page', 'orders')

@section('content')
<section class="page-hero page-hero--compact">
        <div class="container">

            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <span>Đơn hàng</span>
            </nav>

            <h1>Lịch sử đơn hàng</h1>

        </div>
    </section>

    <section class="section container account-layout">

        <aside data-account-nav></aside>

        <div class="account-content">

            <div class="panel-heading">

                <div>
                    <span class="eyebrow">Theo dõi mua sắm</span>
                    <h2>Đơn hàng của tôi</h2>
                </div>

                <label class="inline-select">
                    Trạng thái

                    <select id="order-filter">
                        <option value="all">Tất cả</option>
                        <option value="pending">Chờ xác nhận</option>
                        <option value="shipping">Đang giao</option>
                        <option value="delivered">Đã giao</option>
                        <option value="cancelled">Đã hủy</option>
                    </select>
                </label>

            </div>

            <!-- Danh sách đơn hàng sẽ được API đưa vào đây -->
            <div class="order-list" id="order-list">

                <div class="empty-state">
                    <i data-feather="package" aria-hidden="true"></i>

                    <h2>Đơn hàng</h2>

                    <p>
                        Dữ liệu đơn hàng sẽ được tải từ API.
                    </p>
                </div>

            </div>

            <div class="empty-state" id="orders-empty" hidden>
                <span>
                    <i data-feather="package" aria-hidden="true"></i>
                </span>
                <h2>Không có đơn hàng</h2>
                <p>
                    Chưa có đơn hàng phù hợp với trạng thái đã chọn.
                </p>

            </div>

        </div>

    </section>
@endsection