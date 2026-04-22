@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'equipos_banner')->first();
$bannerMedia = $banner?->activeMedia->first();
$bannerMeta = $banner?->meta ?? [];
$pageTitle = $banner?->title;
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? null;
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? null;
$cardButtonText = $bannerMeta['card_button_text'] ?? null;
@endphp

@section('content')
@if($banner && $bannerMedia)
<section class="equipos-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img
                class="equipos-banner__img"
                src="{{ $bannerMedia->file_url }}"
                alt="{{ $bannerMedia->alt_text ?? 'Equipos Calderas Hercal' }}"
                aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video
                class="equipos-banner__video"
                autoplay muted loop playsinline
                aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="equipos-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="equipos-banner__overlay" aria-hidden="true"></div>

    <div class="equipos-banner__content">
        <h1 class="equipos-banner__title">{{ $pageTitle }}</h1>
        <nav class="equipos-banner__breadcrumb" aria-label="Migas de pan">
            <a href="{{ route('home') }}">{{ $breadcrumbHomeText }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 15L12.5 10L7.5 5" stroke="#F5FBFE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ $breadcrumbCurrentText }}</span>
        </nav>
    </div>
</section>
@endif

<section class="equipos-page">
    <div class="hc-container">
        <div class="equipos-page__grid">
            @forelse($equipments as $equipment)
            <article class="equipos-card">
                <div class="equipos-card__img-wrap">
                    <img src="{{ $equipment->main_image_url }}" alt="{{ $equipment->title }}" loading="lazy">
                </div>
                <div class="equipos-card__body">
                    <svg class="equipos-card__divider" xmlns="http://www.w3.org/2000/svg" width="307" height="1" viewBox="0 0 307 1" fill="none" aria-hidden="true">
                        <path d="M0 0.5L307 0.500027" stroke="#C5C5C5"/>
                    </svg>
                    <h2 class="equipos-card__title">{{ $equipment->title }}</h2>
                    <a href="{{ route('equipos.show', $equipment->slug ?: $equipment->id) }}" class="equipos-card__btn">
                        <span class="equipos-card__btn-text">{{ $cardButtonText }}</span>
                        <svg class="equipos-card__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5.83325 5.8335H14.1666M14.1666 5.8335V14.1668M14.1666 5.8335L5.83325 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </div>
            </article>
            @empty
            <p class="hc-empty">No hay equipos disponibles.</p>
            @endforelse
        </div>
    </div>
</section>
@endsection
