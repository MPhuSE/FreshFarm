@extends('layouts.storefront')

@section('title', $page->title)

@section('content')

<div class="container section">
    <nav class="breadcrumbs mb-8">
        <a href="{{ url('/') }}">Trang chủ</a>
        <span>/</span>
        <span>{{ $page->title }}</span>
    </nav>
    
    <article class="prose max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-sm ring-1 ring-slate-200">
        <h1 class="text-3xl font-bold mb-6 text-emerald-800">{{ $page->title }}</h1>
        <div class="html-content text-slate-700 leading-relaxed">
            {!! $page->content !!}
        </div>
    </article>
</div>

@endsection
