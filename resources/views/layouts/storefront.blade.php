<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>@yield("title") | FreshFarm</title>

    <link
        rel="icon"
        type="image/svg+xml"
        href="{{ asset('tv4/assets/images/favicon.svg') }}"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="{{ asset('tv4/assets/css/style.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('tv4/assets/css/layout-fixes.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('tv4/assets/css/integration.css') }}"
    >
</head>

<body data-page="@yield('page')">

    @include('partials.storefront-header')

    @if($tv4Preview)

        <div
            class="preview-notice"
            role="status"
        >
            Bản xem thử · Dữ liệu mẫu, không tạo đơn thật.

            <a href="{{ url()->current() }}">
                Chuyển sang API
            </a>
        </div>

    @endif

    <div id="live-main">
        @yield('content')
    </div>

    @include('partials.storefront-footer')

    <div
        id="toast"
        class="toast"
        role="status"
        aria-live="polite"
    ></div>

    <script>
        window.FF = {
            preview: @json($tv4Preview),
            staticPreview: false,
            base: @json(url('/')),
            assetBase: @json(asset('tv4/assets')),
            slug: @json($slug ?? null),
            orderCode: @json($order_code ?? null)
        };
    </script>

    <script
        src="{{ asset('tv4/assets/vendor/feather/feather.min.js') }}"
    ></script>

    <script
        src="{{ asset('tv4/assets/js/config.js') }}"
    ></script>

    @if($tv4Preview)

        <script
            src="{{ asset('tv4/assets/js/demo.js') }}"
        ></script>

    @else
        {{-- Shell chạy trước API để cung cấp icon, toast, header... --}}
        <script
            src="{{ asset('tv4/assets/js/shell.js') }}"
        ></script>
        {{-- API đã tách thành ES Modules --}}
        <script
            type="module"
            src="{{ asset('tv4/assets/js/api.js') }}"
        ></script>
    @endif
</body>
</html>