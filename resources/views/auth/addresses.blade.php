@extends('layouts.storefront')
@section('title', 'Địa chỉ')
@section('page', 'addresses')

@section('content')
<main id="live-main">

    <section class="page-hero page-hero--compact">
        <div class="container">
            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <a href="{{ url('/profile') }}">Tài khoản</a>
                <span>/</span>
                <span>Địa chỉ</span>
            </nav>

            <h1>Địa chỉ giao hàng</h1>
        </div>
    </section>


    <section class="section container account-layout">

        <aside data-account-nav></aside>

        <div class="account-content">

            <div class="panel-heading">

                <div>
                    <span class="eyebrow">Sổ địa chỉ</span>
                    <h2>Chọn nơi nhận hàng</h2>
                </div>

                <button
                    class="btn btn--primary"
                    data-open-address
                >
                    <i data-feather="plus" aria-hidden="true"></i>
                    Thêm địa chỉ
                </button>

            </div>


            {{-- Address grid —  API populates this --}}
            <div class="address-grid" id="address-list">

                {{-- Loading skeleton --}}
                <div class="address-card" style="min-height:200px">
                    <div>
                        <div class="skeleton-line skeleton-line--short" style="margin-bottom:12px"></div>
                        <div class="skeleton-line" style="margin-bottom:8px"></div>
                        <div class="skeleton-line skeleton-line--mid" style="margin-bottom:8px"></div>
                        <div class="skeleton-line skeleton-line--mid"></div>
                    </div>
                </div>

                <div class="address-card" style="min-height:200px">
                    <div>
                        <div class="skeleton-line skeleton-line--short" style="margin-bottom:12px"></div>
                        <div class="skeleton-line" style="margin-bottom:8px"></div>
                        <div class="skeleton-line skeleton-line--mid" style="margin-bottom:8px"></div>
                        <div class="skeleton-line skeleton-line--mid"></div>
                    </div>
                </div>

            </div>


            {{-- Empty state --}}
            <div class="empty-state" id="addresses-empty" hidden>
                <span>
                    <i data-feather="map-pin" aria-hidden="true"></i>
                </span>
                <h2>Chưa có địa chỉ</h2>
                <p>Thêm địa chỉ giao hàng để thanh toán nhanh hơn.</p>
                <button
                    class="btn btn--primary"
                    data-open-address
                >
                    <i data-feather="plus" aria-hidden="true"></i>
                    Thêm địa chỉ đầu tiên
                </button>
            </div>

        </div>

    </section>

</main>


{{-- Modal thêm/sửa địa chỉ --}}
<dialog class="modal" id="address-modal">

    <form method="dialog" class="modal__card" data-address-form>

        <div class="modal__head">
            <div>
                <span class="eyebrow" id="address-modal-eyebrow">Địa chỉ mới</span>
                <h2 id="address-modal-title">Thông tin nhận hàng</h2>
            </div>

            <button value="cancel" aria-label="Đóng">
                <i data-feather="x" aria-hidden="true"></i>
            </button>
        </div>


        <input type="hidden" name="address_id" id="address-id">


        <div class="form-grid">

            <label>
                Người nhận
                <input
                    name="recipient_name"
                    required
                    placeholder="Họ và tên"
                >
            </label>

            <label>
                Số điện thoại
                <input
                    name="phone"
                    type="tel"
                    required
                    placeholder="0901 234 567"
                >
            </label>

        </div>


        <label>
            Địa chỉ đầy đủ
            <textarea
                name="address"
                rows="3"
                required
                placeholder="Số nhà, đường, phường/xã, tỉnh/thành phố"
            ></textarea>
        </label>


        <label class="check-inline">
            <input
                type="checkbox"
                name="is_default"
            >
            Đặt làm địa chỉ mặc định
        </label>


        <div
            class="alert alert--error"
            data-form-error
            hidden
        ></div>


        <div class="form-actions">

            <button
                class="btn btn--outline"
                value="cancel"
            >
                Hủy
            </button>

            <button
                class="btn btn--primary"
                type="submit"
            >
                Lưu địa chỉ
            </button>

        </div>

    </form>

</dialog>

@endsection
