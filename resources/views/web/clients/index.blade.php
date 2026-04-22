@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'clientes_banner')->first();
$bannerMedia = $banner?->media?->where('is_active', true)->sortBy('updated_at')->last();
$bannerMeta = $banner?->meta ?? [];
$contentSection = $page?->sections->where('section_key', 'clientes_content')->first();
$pageTitle = $banner?->title ?? 'Clientes';
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? 'Inicio';
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? 'Clientes';
$contentTitle = $contentSection?->title ?? 'Brindamos atención a:';
@endphp

@section('content')
@if($banner && $bannerMedia)
<section class="clientes-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img
                class="clientes-banner__img"
                src="{{ $bannerMedia->file_url }}"
                alt="{{ $bannerMedia->alt_text ?? 'Clientes Calderas Hercal' }}"
                aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video
                class="clientes-banner__video"
                autoplay muted loop playsinline
                aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="clientes-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="clientes-banner__overlay" aria-hidden="true"></div>

    <div class="clientes-banner__content">
        <h1 class="clientes-banner__title">{{ $pageTitle }}</h1>
        <nav class="clientes-banner__breadcrumb" aria-label="Migas de pan">
            <a href="{{ route('home') }}">{{ $breadcrumbHomeText }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 15L12.5 10L7.5 5" stroke="#F5FBFE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ $breadcrumbCurrentText }}</span>
        </nav>
    </div>
</section>
@endif

@if($contentSection)
<section class="clientes-page">
    <div class="hc-container">
        <h2 class="clientes-page__title">{{ $contentTitle }}</h2>

        <div class="clientes-page__grid">
            @forelse($clients as $client)
            <article class="clientes-card">
                <div class="clientes-card__icon-circle" aria-hidden="true">
                    @if($client->icon_url)
                        <img
                            class="clientes-card__icon"
                            src="{{ $client->icon_url }}"
                            alt="{{ $client->name }}"
                            loading="lazy"
                            @if($client->icon_width && $client->icon_height)
                                style="width: {{ $client->icon_width }}px; height: {{ $client->icon_height }}px;"
                            @endif
                        >
                    @endif
                </div>
                <h3 class="clientes-card__title">{{ $client->name }}</h3>
            </article>
            @empty
            <p class="hc-empty">No hay clientes disponibles.</p>
            @endforelse
        </div>
    </div>
</section>
@endif
@endsection
