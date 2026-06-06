@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'servicios_banner')->first();
$bannerMedia = $banner?->media?->where('is_active', true)->sortBy('updated_at')->last();
$bannerMeta = $banner?->meta ?? [];
$pageTitle = $banner?->title ?? 'Servicios';
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? 'Inicio';
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? 'Servicios';
$cardButtonText = $bannerMeta['card_button_text'] ?? 'Ver todas';
@endphp

@section('content')

@if($banner && $bannerMedia)
<section class="servicios-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img
                class="servicios-banner__img"
                src="{{ $bannerMedia->file_url }}"
                alt="{{ $bannerMedia->alt_text ?? 'Servicios Calderas Hercal' }}"
                aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video
                class="servicios-banner__video"
                autoplay muted loop playsinline
                aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="servicios-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="servicios-banner__overlay" aria-hidden="true"></div>

    <div class="servicios-banner__content">
        <h1 class="servicios-banner__title">{{ $pageTitle }}</h1>
        <nav class="servicios-banner__breadcrumb" aria-label="Migas de pan">
            <a href="{{ route('home') }}">{{ $breadcrumbHomeText }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 15L12.5 10L7.5 5" stroke="#F5FBFE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ $breadcrumbCurrentText }}</span>
        </nav>
    </div>
</section>
@endif

<section class="servicios-page">
    <div class="hc-container">
        <div class="servicios-page__grid">
            @forelse($services as $service)
            <article class="servicios-card{{ $service->subtitle ? ' servicios-card--with-subtitle' : '' }}{{ $service->slug === 'soldaduras' ? ' servicios-card--soldaduras' : '' }}">
                <div class="servicios-card__img-wrap">
                    <img src="{{ $service->image_url }}" alt="{{ $service->title }}" loading="lazy">
                </div>
                <div class="servicios-card__divider" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="229" height="1" viewBox="0 0 229 1" fill="none">
                        <path d="M0 0.5L229 0.50002" stroke="#C5C5C5"/>
                    </svg>
                </div>
                <div class="servicios-card__body">
                    <h2 class="servicios-card__title">{{ $service->title }}</h2>
                    @if($service->subtitle)
                    <p class="servicios-card__subtitle">{{ $service->subtitle }}</p>
                    @endif
                    <a href="{{ route('servicios.show', $service->slug) }}" class="servicios-card__btn">
                        <span class="servicios-card__btn-text">{{ $cardButtonText }}</span>
                        <svg class="servicios-card__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5.8335 5.8335H14.1668M14.1668 5.8335V14.1668M14.1668 5.8335L5.8335 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </article>
            @empty
            <p class="hc-empty">No hay servicios disponibles.</p>
            @endforelse
        </div>
    </div>
</section>

@endsection
