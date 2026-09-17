@extends('layouts.storefront')

@section('title', 'Chi tiết đơn hàng')
@section('page', 'order-detail')

@section('content')
<section class="page-hero page-hero--compact">
        <div class="container">

            <nav class="breadcrumbs">
                <a href="{{ url('/orders') }}">Đơn hàng</a>
                <span>/</span>
                <span>Chi tiết đơn hàng</span>
            </nav>

            <div class="page-hero__row">

                <div>
                    <h1>Chi tiết đơn hàng</h1>
                    <p>Thông tin đơn hàng</p>
                </div>

                <span class="status-badge">
                    Trạng thái
                </span>

            </div>

        </div>
    </section>


    <section class="section container detail-layout">

        <div class="detail-main">

            <!-- Tiến trình -->
            <section class="panel tracking">

                <div class="panel-heading">
                    <h2>Tiến trình đơn hàng</h2>
                    <span>Dự kiến giao hàng</span>
                </div>

                <ol>

                    <li class="is-done">
                        <i>
                            <i data-feather="check"></i>
                        </i>

                        <div>
                            <strong>Đã đặt hàng</strong>
                            <small>Đang cập nhật</small>
                        </div>
                    </li>

                    <li>
                        <i>2</i>

                        <div>
                            <strong>Đã xác nhận</strong>
                            <small>Đang cập nhật</small>
                        </div>
                    </li>

                    <li>
                        <i>3</i>

                        <div>
                            <strong>Đang giao hàng</strong>
                            <small>Đang cập nhật</small>
                        </div>
                    </li>

                    <li>
                        <i>4</i>

                        <div>
                            <strong>Đã giao</strong>
                            <small>Đang cập nhật</small>
                        </div>
                    </li>

                </ol>

            </section>


            <!-- Sản phẩm -->
            <section class="panel">

                <div class="panel-heading">
                    <h2>Sản phẩm</h2>
                    <span>Sản phẩm trong đơn</span>
                </div>

                <div id="order-items">

                    <!-- Dữ liệu sản phẩm sẽ được API đưa vào đây -->

                </div>

            </section>

        </div>


        <aside class="detail-side">

            <!-- Thông tin giao hàng -->
            <section class="panel info-panel">

                <h2>Thông tin giao hàng</h2>

                <div id="shipping-info">

                    <p>Thông tin người nhận</p>
                    <p>Số điện thoại</p>
                    <p>Địa chỉ giao hàng</p>

                </div>

                <hr>

                <span>Phương thức thanh toán</span>

                <strong id="payment-method">
                    ---
                </strong>

                <span class="status-badge">
                    Trạng thái thanh toán
                </span>

            </section>


            <!-- Thanh toán -->
            <section class="panel order-summary">

                <h2>Thanh toán</h2>

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

                <button
                    class="btn btn--outline btn--block"
                    data-cancel-order>
                    Hủy đơn hàng
                </button>

            </section>

        </aside>

    </section>


<!-- Modal hủy đơn -->
<dialog class="modal" id="cancel-modal">

    <form method="dialog" class="modal__card">

        <div class="modal__head">

            <div>
                <span class="eyebrow">Xác nhận hủy</span>

                <h2>
                    Bạn muốn hủy đơn hàng?
                </h2>
            </div>

            <button value="cancel" aria-label="Đóng">
                <i data-feather="x"></i>
            </button>

        </div>


        <label>
            Lý do hủy

            <select required>
                <option value="">
                    Chọn lý do
                </option>

                <option>
                    Muốn thay đổi sản phẩm
                </option>

                <option>
                    Thông tin giao hàng chưa đúng
                </option>

                <option>
                    Không còn nhu cầu
                </option>
            </select>
        </label>


        <label>
            Ghi chú

            <textarea
                rows="3"
                placeholder="Thông tin thêm">
            </textarea>
        </label>


        <div class="alert alert--warning">

            <strong>
                Không thể hoàn tác
            </strong>

            <span>
                Đơn hàng sẽ được xử lý theo trạng thái từ hệ thống.
            </span>

        </div>


        <div class="form-actions">

            <button
                class="btn btn--outline"
                value="cancel">
                Giữ đơn
            </button>

            <button
                class="btn btn--danger"
                value="confirm"
                data-confirm-cancel>
                Hủy đơn
            </button>

        </div>

    </form>

</dialog>

@endsection