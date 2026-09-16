@extends('layouts.storefront')

@section('title', 'Nông Sản Xanh')
@section('page', 'home')

@section('content')

<main class="home-page-main">

    <!-- Hero Section -->
    <section class="home-hero">
        <div class="container home-hero__inner">
            <div class="home-hero__content">
                <span class="eyebrow">NÔNG SẢN SẠCH - CUỘC SỐNG LÀNH MẠNH</span>
                <h1>
                    Tươi từ vườn.<br>
                    Lành mỗi ngày.
                </h1>
                <p>Nông sản tươi sạch cho bữa ăn gia đình<br>và một tương lai xanh hơn.</p>
                <div class="home-hero__actions">
                    <a href="{{ url('/products') }}" class="btn btn--primary">Khám phá sản phẩm <i data-feather="arrow-right"></i></a>
                    <a href="#story" class="btn btn--outline" style="background:#fff;">Xem nguồn gốc</a>
                </div>
                <div class="home-hero__badges">
                    <span><i data-feather="sun"></i> Thu hoạch trong ngày</span>
                    <span><i data-feather="shield"></i> Truy xuất nguồn gốc</span>
                    <span><i data-feather="truck"></i> Giao nhanh 2-4h</span>
                </div>
            </div>
            <div class="home-hero__image">
                <img src="{{ asset('tv4/assets/images/hero_crate.jpg') }}" alt="Nông sản tươi">
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="home-categories container section">
        <div class="section-heading">
            <h2>Khám phá danh mục</h2>
            <a href="{{ url('/products') }}" class="text-link">Xem tất cả danh mục <i data-feather="arrow-right"></i></a>
        </div>
        <div class="categories-grid">
            <a href="{{ url('/products?category_id=1') }}" class="cat-card" style="background: #eef7eb;">
                <div class="cat-card__img"><img src="{{ asset('tv4/assets/images/placeholder.svg') }}" alt="Rau củ"></div>
                <div class="cat-card__content">
                    <h3>Rau củ <i data-feather="arrow-right"></i></h3>
                    <p>Tươi ngon mỗi ngày</p>
                </div>
            </a>
            <a href="{{ url('/products?category_id=2') }}" class="cat-card" style="background: #fff3e0;">
                <div class="cat-card__img"><img src="{{ asset('tv4/assets/images/placeholder.svg') }}" alt="Trái cây"></div>
                <div class="cat-card__content">
                    <h3>Trái cây <i data-feather="arrow-right"></i></h3>
                    <p>Ngọt lành tự nhiên</p>
                </div>
            </a>
            <a href="{{ url('/products?category_id=3') }}" class="cat-card" style="background: #ffebee;">
                <div class="cat-card__img"><img src="{{ asset('tv4/assets/images/placeholder.svg') }}" alt="Thịt & trứng"></div>
                <div class="cat-card__content">
                    <h3>Thịt & trứng <i data-feather="arrow-right"></i></h3>
                    <p>An toàn, chất lượng</p>
                </div>
            </a>
            <a href="{{ url('/products?category_id=4') }}" class="cat-card" style="background: #fdf8e8;">
                <div class="cat-card__img"><img src="{{ asset('tv4/assets/images/placeholder.svg') }}" alt="Gạo & hạt"></div>
                <div class="cat-card__content">
                    <h3>Gạo & hạt <i data-feather="arrow-right"></i></h3>
                    <p>Tinh hoa nông sản Việt</p>
                </div>
            </a>
            <a href="{{ url('/products?category_id=5') }}" class="cat-card" style="background: #f0f4ec;">
                <div class="cat-card__img"><img src="{{ asset('tv4/assets/images/placeholder.svg') }}" alt="Đặc sản"></div>
                <div class="cat-card__content">
                    <h3>Đặc sản <i data-feather="arrow-right"></i></h3>
                    <p>Hương vị vùng miền</p>
                </div>
            </a>
        </div>
    </section>

    <!-- Products Section -->
    <section class="home-products container section">
        <div class="section-heading">
            <div>
                <h2>Nông sản hôm nay</h2>
                <p style="color:var(--text-light); margin-top:4px;">Nông sản tươi ngon, được tuyển chọn mỗi ngày từ các nông trại đối tác của Nông Sản Xanh.</p>
            </div>
            <a href="{{ url('/products') }}" class="text-link">Xem tất cả sản phẩm <i data-feather="arrow-right"></i></a>
        </div>
        
        <!-- JavaScript will inject products here -->
        <div id="home-catalog-results" aria-live="polite">
            <!-- Skeletons while loading -->
            @for ($i = 0; $i < 4; $i++)
            <div class="skeleton-product">
                <div class="skeleton-product__img"></div>
                <div class="skeleton-product__body">
                    <div class="skeleton-line skeleton-line--short"></div>
                    <div class="skeleton-line"></div>
                    <div class="skeleton-line skeleton-line--mid"></div>
                    <div class="skeleton-line skeleton-line--price"></div>
                </div>
            </div>
            @endfor
        </div>
    </section>

    <!-- Story/Banner Section -->
    <section id="story" class="home-story container section">
        <div class="home-story__inner">
            <img src="{{ asset('tv4/assets/images/farmer_banner.jpg') }}" alt="Từ nông trại đến bàn ăn" class="home-story__bg">
            <div class="home-story__content">
                <span class="eyebrow eyebrow--light">TRUY XUẤT NGUỒN GỐC MINH BẠCH</span>
                <h2>Từ nông trại đến bàn ăn<br>Luôn rõ ràng, luôn an tâm.</h2>
                <p>Mỗi sản phẩm đều có nguồn gốc rõ ràng, đồng hành cùng<br>nông dân Việt vì một nền nông nghiệp bền vững.</p>
                <a href="{{ url('/products') }}" class="btn btn--primary">Xem câu chuyện của chúng tôi <i data-feather="arrow-right"></i></a>
            </div>
        </div>
    </section>

    <!-- Trust Badges Strip -->
    <section class="home-trust-strip">
        <div class="container home-trust-strip__inner">
            <article>
                <span class="trust-icon"><i data-feather="check-circle"></i></span>
                <div>
                    <strong>Nguồn gốc rõ ràng</strong>
                    <small>Truy xuất minh bạch từng sản phẩm</small>
                </div>
            </article>
            <article>
                <span class="trust-icon"><i data-feather="shield"></i></span>
                <div>
                    <strong>Canh tác an toàn</strong>
                    <small>Vì sức khỏe gia đình bạn</small>
                </div>
            </article>
            <article>
                <span class="trust-icon"><i data-feather="truck"></i></span>
                <div>
                    <strong>Giao hàng lạnh toàn trình</strong>
                    <small>Giữ trọn độ tươi ngon</small>
                </div>
            </article>
            <article>
                <span class="trust-icon"><i data-feather="box"></i></span>
                <div>
                    <strong>Đổi trả dễ dàng</strong>
                    <small>Hỗ trợ nhanh chóng</small>
                </div>
            </article>
        </div>
    </section>

</main>

@endsection