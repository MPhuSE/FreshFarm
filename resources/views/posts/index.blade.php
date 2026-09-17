@extends('layouts.storefront')
@section('title', 'Tin tức - Nông Sản Xanh')
@section('page', 'news')
@section('content')

<main id="live-main">
    <section class="page-hero page-hero--compact">
        <div class="container">
            <nav class="breadcrumbs">
                <a href="{{ url('/') }}">Trang chủ</a>
                <span>/</span>
                <span>Tin tức</span>
            </nav>
            <div class="page-hero__row">
                <div>
                    <h1>Tin tức & Mẹo hay</h1>
                    <p>Cập nhật những thông tin mới nhất về nông sản và kiến thức dinh dưỡng</p>
                </div>
            </div>
        </div>
    </section>

    <div class="container section">
        @if($posts->count() > 0)
            <div class="product-grid" style="grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 30px;">
                @foreach($posts as $post)
                    <article class="product-card" style="display: flex; flex-direction: column;">
                        <a href="{{ url('/tin-tuc/' . $post->slug) }}" class="product-card__media" style="aspect-ratio: 16/9; padding: 0;">
                            <img src="{{ $post->image_url ?: 'https://placehold.co/600x400/eef7eb/2d6a4f?text=Tin+tuc' }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
                        </a>
                        <div class="product-card__body" style="flex: 1; display: flex; flex-direction: column;">
                            <div class="product-card__cat" style="margin-bottom: 8px;">{{ $post->created_at->format('d/m/Y') }}</div>
                            <h3 style="font-size: 1.1rem; line-height: 1.4; margin-bottom: 12px; font-weight: 700;">
                                <a href="{{ url('/tin-tuc/' . $post->slug) }}">{{ $post->title }}</a>
                            </h3>
                            <p style="color: var(--muted); font-size: 0.875rem; flex: 1;">{{ Str::limit(strip_tags($post->content), 120) }}</p>
                            <div style="margin-top: 16px;">
                                <a href="{{ url('/tin-tuc/' . $post->slug) }}" class="text-link" style="display: inline-flex; align-items: center; gap: 4px;">Đọc tiếp <i data-feather="arrow-right" style="width: 14px; height: 14px;"></i></a>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
            
            <div class="pagination" style="margin-top: 40px;">
                {{ $posts->links('pagination::bootstrap-4') }}
            </div>
        @else
            <div class="empty-state">
                <span><i data-feather="file-text"></i></span>
                <h2>Chưa có bài viết nào</h2>
                <p>Nội dung đang được chúng tôi cập nhật. Vui lòng quay lại sau.</p>
                <a href="{{ url('/') }}" class="btn btn--outline">Về trang chủ</a>
            </div>
        @endif
    </div>
</main>

@endsection
