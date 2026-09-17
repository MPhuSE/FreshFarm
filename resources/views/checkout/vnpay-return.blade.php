@extends('layouts.storefront')

@section('title', 'Kết quả thanh toán VNPay')
@section('page', 'checkout-result')

@section('content')
<main id="live-main">
    <section class="page-hero page-hero--compact">
        <div class="container">
            <div class="page-hero__row">
                <h1>Kết quả thanh toán</h1>
            </div>
        </div>
    </section>

    <div class="container section">
        <div class="panel" style="max-w-md; margin: 0 auto; text-align: center; padding: 3rem 2rem;">
            @php
                $vnp_ResponseCode = request('vnp_ResponseCode');
                $vnp_TxnRef = request('vnp_TxnRef');
                $isSuccess = $vnp_ResponseCode === '00';
            @endphp

            @if($isSuccess)
                <div style="color: #10b981; margin-bottom: 1rem; display: flex; justify-content: center;">
                    <i data-feather="check-circle" style="width: 64px; height: 64px;"></i>
                </div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Thanh toán thành công!</h2>
                <p style="color: #64748b; margin-bottom: 2rem;">
                    Đơn hàng <strong>{{ $vnp_TxnRef }}</strong> của bạn đã được thanh toán thành công qua VNPay.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ url('/orders/' . $vnp_TxnRef) }}" class="btn btn--primary">Xem đơn hàng</a>
                    <a href="{{ url('/products') }}" class="btn btn--outline">Tiếp tục mua sắm</a>
                </div>
            @else
                <div style="color: #ef4444; margin-bottom: 1rem; display: flex; justify-content: center;">
                    <i data-feather="x-circle" style="width: 64px; height: 64px;"></i>
                </div>
                <h2 style="font-size: 1.5rem; margin-bottom: 1rem;">Thanh toán thất bại!</h2>
                <p style="color: #64748b; margin-bottom: 2rem;">
                    Đơn hàng <strong>{{ $vnp_TxnRef }}</strong> chưa được thanh toán thành công. Vui lòng thử lại.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center;">
                    <a href="{{ url('/orders/' . $vnp_TxnRef) }}" class="btn btn--primary">Thử lại</a>
                    <a href="{{ url('/products') }}" class="btn btn--outline">Về trang chủ</a>
                </div>
            @endif
        </div>
    </div>
</main>
@endsection
