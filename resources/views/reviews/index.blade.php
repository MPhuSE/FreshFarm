@extends('layouts.storefront')

@section('title', 'Đánh giá sản phẩm')
@section('page', 'review')

@section('content')
<main id="live-main">

    <section class="page-hero page-hero--compact">
        <div class="container">

            <nav class="breadcrumbs">
                <a href="{{ url('/orders') }}">Đơn hàng</a>
                <span>/</span>
                <span>Đánh giá</span>
            </nav>

            <h1>Chia sẻ trải nghiệm</h1>

        </div>
    </section>


    <section class="section container review-page">

        <form class="panel review-form" data-review-form>

            <!-- Sản phẩm được đánh giá -->
            <div class="review-product">

                <img
                    src="{{ asset('tv4/assets/images/product-01.png') }}"
                    alt="Sản phẩm">

                <div>

                    <span class="eyebrow">
                        Thông tin đơn hàng
                    </span>

                    <h2>
                        Sản phẩm
                    </h2>

                    <p>
                        Thông tin sản phẩm sẽ được tải từ hệ thống.
                    </p>

                </div>

            </div>


            <!-- Đánh giá -->
            <fieldset>

                <legend>
                    Sản phẩm có làm bạn hài lòng?
                </legend>

                <div class="rating-input"
                     aria-label="Chọn số sao">

                    <input
                        id="star5"
                        name="rating"
                        type="radio"
                        value="5">

                    <label for="star5"
                           aria-label="5 sao">
                        <i data-feather="star"></i>
                    </label>


                    <input
                        id="star4"
                        name="rating"
                        type="radio"
                        value="4">

                    <label for="star4"
                           aria-label="4 sao">
                        <i data-feather="star"></i>
                    </label>


                    <input
                        id="star3"
                        name="rating"
                        type="radio"
                        value="3">

                    <label for="star3"
                           aria-label="3 sao">
                        <i data-feather="star"></i>
                    </label>


                    <input
                        id="star2"
                        name="rating"
                        type="radio"
                        value="2">

                    <label for="star2"
                           aria-label="2 sao">
                        <i data-feather="star"></i>
                    </label>


                    <input
                        id="star1"
                        name="rating"
                        type="radio"
                        value="1"
                        required>

                    <label for="star1"
                           aria-label="1 sao">
                        <i data-feather="star"></i>
                    </label>

                </div>

            </fieldset>


            <!-- Nhận xét -->
            <label>

                Nhận xét của bạn

                <textarea
                    name="comment"
                    rows="6"
                    maxlength="500"
                    placeholder="Chia sẻ trải nghiệm của bạn..."></textarea>

                <small>
                    Tối đa 500 ký tự
                </small>

            </label>


            <div class="alert alert--info">

                <strong>
                    Đánh giá sẽ được duyệt trước khi hiển thị.
                </strong>

                <span>
                    Thông tin đánh giá sẽ được xử lý bởi hệ thống.
                </span>

            </div>


            <div class="form-actions">

                <a
                    class="btn btn--outline"
                    href="{{ url('/orders') }}">
                    Để sau
                </a>

                <button
                    class="btn btn--primary"
                    type="submit">
                    Gửi đánh giá
                </button>

            </div>

        </form>

    </section>

</main>
@endsection