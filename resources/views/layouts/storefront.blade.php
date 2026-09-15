<!doctype html>
<html lang="vi">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield("title") | FreshFarm</title>

    <!-- Đã chuẩn hóa đường dẫn Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/favicon.svg') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Sử dụng Vite để nạp CSS chuẩn Laravel -->
    @vite([
        'resources/css/style.css',
        'resources/css/layout-fixes.css',
        'resources/css/integration.css',
        'resources/js/app.js',
        'resources/js/config.js'
    ])
</head>

<body data-page="@yield('page')">

    @include('partials.storefront-header')

    @if($tv4Preview)
        <div class="preview-notice" role="status">
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

    <div id="toast" class="toast" role="status" aria-live="polite"></div>

    <script>
        window.FF = {
            preview: @json($tv4Preview),
            staticPreview: false,
            base: @json(url('/')),
            // Đã sửa assetBase để trỏ về thư mục public gốc
            assetBase: @json(asset('')),
            slug: @json($slug ?? null),
            orderCode: @json($order_code ?? null)
        };
    </script>

    <!-- Thư viện ngoài nên đặt ở thư mục public/vendor -->
    <script src="{{ asset('vendor/feather/feather.min.js') }}"></script>

    <!-- Sử dụng Vite để nạp JS -->
    @if($tv4Preview)
        @vite(['resources/js/demo.js'])
    @else
        @vite([
            'resources/js/shell.js',
            'resources/js/api.js'
        ])
    @endif
</body>
</html>