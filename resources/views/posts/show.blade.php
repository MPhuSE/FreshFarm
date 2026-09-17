@extends('layouts.storefront')
@section('title', $post->title . ' - Tin tức - Nông Sản Xanh')
@section('page', 'news')
@section('content')

<main id="live-main">
    <div class="container section">
        <nav class="breadcrumbs mb-8">
            <a href="{{ url('/') }}">Trang chủ</a>
            <span>/</span>
            <a href="{{ url('/tin-tuc') }}">Tin tức</a>
            <span>/</span>
            <span>{{ Str::limit($post->title, 40) }}</span>
        </nav>
        
        <article class="prose max-w-4xl mx-auto bg-white p-6 md:p-10 rounded-2xl shadow-sm border" style="border-color: var(--line);">
            <div style="margin-bottom: 24px; color: var(--muted); font-size: 0.9rem; display: flex; align-items: center; gap: 8px;">
                <i data-feather="calendar" style="width: 16px; height: 16px;"></i> {{ $post->created_at->format('d/m/Y H:i') }}
            </div>
            
            <h1 class="text-3xl md:text-4xl font-bold mb-8 text-green-900" style="color: var(--green-900); margin-bottom: 30px; line-height: 1.3;">{{ $post->title }}</h1>
            
            @if($post->image_url)
            <div style="margin-bottom: 40px; border-radius: 12px; overflow: hidden;">
                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" style="width: 100%; height: auto;">
            </div>
            @endif
            
            <div class="html-content text-slate-700 leading-relaxed" style="font-size: 1.1rem; line-height: 1.8;">
                {!! $post->content !!}
            </div>
            
            <div style="margin-top: 50px; padding-top: 24px; border-top: 1px solid var(--line); display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ url('/tin-tuc') }}" class="btn btn--outline" style="display: inline-flex; align-items: center; gap: 8px;">
                    <i data-feather="arrow-left" style="width: 16px; height: 16px;"></i> Quay lại
                </a>
            </div>
        </article>
    </div>
</main>

@endsection
