@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'presupuesto_banner')->first();
$bannerMedia = $banner?->activeMedia->first();
$bannerMeta = $banner?->meta ?? [];
$content = $page?->sections->where('section_key', 'presupuesto_content')->first();
$contentMeta = $content?->meta ?? [];
$pageTitle = $banner?->title ?? 'Presupuesto';
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? 'Inicio';
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? 'Presupuesto';
$contactBlockTitle = $content?->title ?? 'Datos de contacto';
$consultaBlockTitle = $content?->subtitle ?? 'Consulta';
$subjectLabel = $subjectType === 'equipment'
    ? ($contentMeta['equipment_label'] ?? 'Equipos')
    : ($contentMeta['service_label'] ?? 'Servicio');
$submitText = $contentMeta['button_text'] ?? 'Solicitar presupuesto';
@endphp

@section('content')
@if($banner && $bannerMedia)
<section class="presupuesto-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img class="presupuesto-banner__img" src="{{ $bannerMedia->file_url }}" alt="{{ $bannerMedia->alt_text ?? 'Presupuesto Calderas Hercal' }}" aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video class="presupuesto-banner__video" autoplay muted loop playsinline aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="presupuesto-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="presupuesto-banner__overlay" aria-hidden="true"></div>

    <div class="presupuesto-banner__content">
        <h1 class="presupuesto-banner__title">{{ $pageTitle }}</h1>
        <nav class="presupuesto-banner__breadcrumb" aria-label="Migas de pan">
            <a href="{{ route('home') }}">{{ $breadcrumbHomeText }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 15L12.5 10L7.5 5" stroke="#F5FBFE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ $breadcrumbCurrentText }}</span>
        </nav>
    </div>
</section>
@endif

@if($content)
<section class="presupuesto-page">
    <div class="hc-container">
        <form action="{{ route('presupuesto.store') }}" method="POST" class="presupuesto-form">
            @csrf
            <input type="text" name="{{ config('security.forms.honeypot_field', 'company') }}" value="" tabindex="-1" autocomplete="off" class="sr-only" aria-hidden="true">
            <input type="hidden" name="subject_type" value="{{ old('subject_type', $subjectType) }}">

            <div class="presupuesto-form__grid">
                <section class="presupuesto-form__block">
                    <h2 class="presupuesto-form__title">{{ $contactBlockTitle }}</h2>

                    <div class="presupuesto-field">
                        <label class="presupuesto-field__label" for="quote-name">{{ $contentMeta['name_label'] ?? 'Nombre*' }}</label>
                        <input class="presupuesto-field__input" id="quote-name" name="name" type="text" value="{{ old('name') }}" required>
                        <span class="presupuesto-field__line presupuesto-field__line--half" aria-hidden="true"></span>
                    </div>

                    <div class="presupuesto-field">
                        <label class="presupuesto-field__label" for="quote-email">{{ $contentMeta['email_label'] ?? 'Email*' }}</label>
                        <input class="presupuesto-field__input" id="quote-email" name="email" type="email" value="{{ old('email') }}" required>
                        <span class="presupuesto-field__line presupuesto-field__line--half" aria-hidden="true"></span>
                    </div>

                    <div class="presupuesto-field">
                        <label class="presupuesto-field__label" for="quote-last-name">{{ $contentMeta['last_name_label'] ?? 'Apellido*' }}</label>
                        <input class="presupuesto-field__input" id="quote-last-name" name="last_name" type="text" value="{{ old('last_name') }}" required>
                        <span class="presupuesto-field__line presupuesto-field__line--half" aria-hidden="true"></span>
                    </div>

                    <div class="presupuesto-field">
                        <label class="presupuesto-field__label" for="quote-phone">{{ $contentMeta['phone_label'] ?? 'Teléfono*' }}</label>
                        <input class="presupuesto-field__input" id="quote-phone" name="phone" type="text" value="{{ old('phone') }}" required>
                        <span class="presupuesto-field__line presupuesto-field__line--half" aria-hidden="true"></span>
                    </div>
                </section>

                <section class="presupuesto-form__block">
                    <h2 class="presupuesto-form__title">{{ $consultaBlockTitle }}</h2>

                    <div class="presupuesto-select">
                        <label class="presupuesto-select__label" for="quote-subject">{{ $subjectLabel }}</label>
                        <div class="presupuesto-select__field">
                            <select id="quote-subject" name="subject_slug">
                                @if(!old('subject_slug', $selectedSlug))
                                <option value=""></option>
                                @endif
                                @foreach($options as $option)
                                @php($optionValue = $option->slug ?: (string) $option->id)
                                <option value="{{ $optionValue }}" {{ old('subject_slug', $selectedSlug) === $optionValue ? 'selected' : '' }}>
                                    {{ $option->title }}
                                </option>
                                @endforeach
                            </select>
                            <svg class="presupuesto-select__arrow" xmlns="http://www.w3.org/2000/svg" width="29" height="29" viewBox="0 0 29 29" fill="none" aria-hidden="true">
                                <path d="M14.1466 18.1151C14.0227 18.1151 13.9066 18.0959 13.7983 18.0575C13.6899 18.0191 13.5893 17.9532 13.4964 17.8597L9.22416 13.5874C9.05389 13.4171 8.96875 13.2004 8.96875 12.9373C8.96875 12.6741 9.05389 12.4574 9.22416 12.2871C9.39443 12.1169 9.61114 12.0317 9.87429 12.0317C10.1374 12.0317 10.3541 12.1169 10.5244 12.2871L14.1466 15.9093L17.7687 12.2871C17.939 12.1169 18.1557 12.0317 18.4188 12.0317C18.682 12.0317 18.8987 12.1169 19.069 12.2871C19.2392 12.4574 19.3244 12.6741 19.3244 12.9373C19.3244 13.2004 19.2392 13.4171 19.069 13.5874L14.7967 17.8597C14.7038 17.9526 14.6032 18.0185 14.4949 18.0575C14.3865 18.0965 14.2704 18.1157 14.1466 18.1151Z" fill="#AEAEB2"/>
                            </svg>
                        </div>
                        <span class="presupuesto-field__line presupuesto-field__line--select" aria-hidden="true"></span>
                    </div>

                    <div class="presupuesto-field presupuesto-field--message">
                        <label class="presupuesto-field__label" for="quote-message">{{ $contentMeta['message_label'] ?? 'Consulta' }}</label>
                        <textarea class="presupuesto-field__textarea" id="quote-message" name="message" required>{{ old('message') }}</textarea>
                        <span class="presupuesto-field__line presupuesto-field__line--message" aria-hidden="true"></span>
                    </div>

                    <div class="presupuesto-form__actions">
                        <span class="presupuesto-form__required">*Campos obligatorios</span>
                        <button type="submit" class="presupuesto-form__btn">
                            <span class="presupuesto-form__btn-text">{{ $submitText }}</span>
                            <svg class="presupuesto-form__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M5.83301 5.8335H14.1663M14.1663 5.8335V14.1668M14.1663 5.8335L5.83301 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </section>
            </div>
        </form>
    </div>
</section>
@endif
@endsection
