@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'normas_banner')->first();
$bannerMedia = $banner?->activeMedia->first();
$bannerMeta = $banner?->meta ?? [];

$contentSection = $page?->sections->where('section_key', 'normas_content')->first();
$contentMeta = $contentSection?->meta ?? [];
$contentMedia = $contentSection?->activeMedia ?? collect();
$mainImage = $contentMedia->first();
$logos = $contentMedia->slice(1);

$pageTitle = $banner?->title ?? 'Normas';
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? 'Inicio';
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? 'Normas';
$buttonText = $contentMeta['button_text'] ?? 'Más información';
$buttonUrl = $contentMeta['button_url'] ?? '/contacto';
@endphp

@section('content')
@if($banner && $bannerMedia)
<section class="normas-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img
                class="normas-banner__img"
                src="{{ $bannerMedia->file_url }}"
                alt="{{ $bannerMedia->alt_text ?? 'Normas Calderas Hercal' }}"
                aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video
                class="normas-banner__video"
                autoplay muted loop playsinline
                aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="normas-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="normas-banner__overlay" aria-hidden="true"></div>

    <div class="normas-banner__content">
        <h1 class="normas-banner__title">{{ $pageTitle }}</h1>
        <nav class="normas-banner__breadcrumb" aria-label="Migas de pan">
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
<section class="normas-content">
    <div class="hc-container">
        <div class="normas-content__grid">
            <div class="normas-content__image-wrap">
                @if($mainImage && $mainImage->type === 'image' && $mainImage->file_url)
                    <img
                        class="normas-content__image"
                        src="{{ $mainImage->file_url }}"
                        alt="{{ $mainImage->alt_text ?? $contentSection?->title ?? 'Normas' }}">
                @elseif($mainImage && $mainImage->type === 'video' && $mainImage->file_url)
                    <video class="normas-content__image normas-content__image--video" autoplay muted loop playsinline>
                        <source src="{{ $mainImage->file_url }}" type="video/mp4">
                    </video>
                @elseif($mainImage && $mainImage->type === 'youtube' && $mainImage->youtube_embed)
                    <iframe
                        class="normas-content__iframe"
                        src="{{ $mainImage->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($mainImage->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                        allow="autoplay; encrypted-media"
                        allowfullscreen
                        title="{{ $contentSection?->title ?? 'Normas' }}">
                    </iframe>
                @endif
            </div>

            <div class="normas-content__text">
                <h2 class="normas-content__title">{{ $contentSection?->title }}</h2>

                @if($contentSection?->description)
                    <div class="normas-content__desc">
                        {!! $contentSection->description !!}
                    </div>
                @endif

                @if($logos->count())
                    <div class="normas-content__logos">
                        @foreach($logos as $logo)
                            @if($logo->type === 'image' && $logo->file_url)
                                <img
                                    class="normas-content__logo"
                                    src="{{ $logo->file_url }}"
                                    alt="{{ $logo->alt_text ?? 'Logo norma' }}">
                            @endif
                        @endforeach
                    </div>
                @endif

                <a href="{{ url($buttonUrl) }}" class="normas-content__btn">
                    <span class="normas-content__btn-text">{{ $buttonText }}</span>
                    <svg class="normas-content__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5.83301 5.8335H14.1663M14.1663 5.8335V14.1668M14.1663 5.8335L5.83301 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>
@endif
@endsection
