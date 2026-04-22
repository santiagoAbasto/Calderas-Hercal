@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'contacto_banner')->first();
$bannerMedia = $banner?->activeMedia->first();
$bannerMeta = $banner?->meta ?? [];

$contentSection = $page?->sections->where('section_key', 'contacto_content')->first();
$contentMeta = $contentSection?->meta ?? [];

$pageTitle = $banner?->title ?? 'Contacto';
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? 'Inicio';
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? 'Contacto';

$emailTitle = $contentMeta['email_title'] ?? 'Email';
$phoneTitle = $contentMeta['phone_title'] ?? 'Teléfono';
$formTitle = $contentSection?->title ?? 'Póngase en contacto con nosotros';
$firstNameLabel = $contentMeta['first_name_label'] ?? 'Nombre*';
$lastNameLabel = $contentMeta['last_name_label'] ?? 'Apellido*';
$emailLabel = $contentMeta['email_label'] ?? 'Email*';
$phoneLabel = $contentMeta['phone_label'] ?? 'Teléfono*';
$messageLabel = $contentMeta['message_label'] ?? 'Mensaje';
$submitText = $contentMeta['button_text'] ?? 'Enviar mensaje';
@endphp

@section('content')
@if($banner && $bannerMedia)
<section class="contacto-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img
                class="contacto-banner__img"
                src="{{ $bannerMedia->file_url }}"
                alt="{{ $bannerMedia->alt_text ?? 'Contacto Calderas Hercal' }}"
                aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video
                class="contacto-banner__video"
                autoplay muted loop playsinline
                aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="contacto-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="contacto-banner__overlay" aria-hidden="true"></div>

    <div class="contacto-banner__content">
        <h1 class="contacto-banner__title">{{ $pageTitle }}</h1>
        <nav class="contacto-banner__breadcrumb" aria-label="Migas de pan">
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
<section class="contacto-page">
    <div class="hc-container">
        <div class="contacto-page__grid">
            <aside class="contacto-card" aria-label="Datos de contacto">
                @if($contactEmails->isNotEmpty())
                    <div class="contacto-card__block">
                        <div class="contacto-card__circle" aria-hidden="true">
                            <svg class="contacto-card__icon contacto-card__icon--mail" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                                <path d="M31.1663 9.9165L18.4291 18.0298C17.9969 18.2808 17.5059 18.413 17.0061 18.413C16.5062 18.413 16.0152 18.2808 15.583 18.0298L2.83301 9.9165M5.66634 5.6665H28.333C29.8978 5.6665 31.1663 6.93503 31.1663 8.49984V25.4998C31.1663 27.0646 29.8978 28.3332 28.333 28.3332H5.66634C4.10153 28.3332 2.83301 27.0646 2.83301 25.4998V8.49984C2.83301 6.93503 4.10153 5.6665 5.66634 5.6665Z" stroke="#AA0000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="contacto-card__text">
                            <h2 class="contacto-card__title">{{ $emailTitle }}</h2>
                            <div class="contacto-card__values">
                                @foreach($contactEmails as $email)
                                    <a href="mailto:{{ $email }}" class="contacto-card__value">{{ $email }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                @if($contactPhones->isNotEmpty())
                    <div class="contacto-card__block">
                        <div class="contacto-card__circle" aria-hidden="true">
                            <svg class="contacto-card__icon contacto-card__icon--phone" xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                                <path d="M30.3666 23.9702V28.2202C30.3683 28.6148 30.2874 29.0054 30.1293 29.367C29.9712 29.7285 29.7392 30.0531 29.4486 30.3199C29.1579 30.5868 28.8144 30.79 28.4406 30.9164C28.0668 31.0428 27.6708 31.0898 27.2777 31.0543C22.9184 30.5807 18.7308 29.0911 15.0518 26.7052C11.6289 24.5311 8.72683 21.629 6.55271 18.206C4.16033 14.5093 2.67056 10.3006 2.20359 5.92104C2.16807 5.52924 2.21478 5.13435 2.34062 4.76153C2.46646 4.38872 2.66866 4.04614 2.93438 3.75579C3.2001 3.46545 3.52368 3.23345 3.88426 3.07486C4.24483 2.91627 4.63443 2.83441 5.02801 2.83433H9.27801C9.96552 2.82757 10.632 3.07103 11.1532 3.51934C11.6745 3.96764 12.0149 4.59022 12.1111 5.27183C12.2905 6.63193 12.6231 7.96738 13.1027 9.25263C13.2933 9.75969 13.3346 10.3108 13.2216 10.8406C13.1086 11.3704 12.8461 11.8567 12.4652 12.2418L10.666 14.041C12.6827 17.5878 15.6288 20.5339 19.1755 22.5506L20.9748 20.7514C21.3599 20.3705 21.8462 20.108 22.376 19.995C22.9058 19.882 23.4569 19.9233 23.9639 20.1139C25.2492 20.5935 26.5846 20.9261 27.9447 21.1056C28.6332 21.2026 29.2612 21.5496 29.7102 22.0804C30.1591 22.6113 30.3981 23.2885 30.381 23.9843L30.3666 23.9702Z" stroke="#AA0000" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <div class="contacto-card__text">
                            <h2 class="contacto-card__title">{{ $phoneTitle }}</h2>
                            <div class="contacto-card__values">
                                @foreach($contactPhones as $phone)
                                    @php
                                        $digits = preg_replace('/\D/', '', $phone['number']);
                                    @endphp
                                    <a href="tel:{{ $digits }}" class="contacto-card__value">{{ $phone['number'] }}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif
            </aside>

            <form class="contacto-form" action="{{ route('contacto.store') }}" method="POST">
                @csrf
                <input type="text" name="{{ config('security.forms.honeypot_field', 'company') }}" value="" tabindex="-1" autocomplete="off" class="sr-only" aria-hidden="true">

                <div class="contacto-form__inner">
                    <h2 class="contacto-form__title">{{ $formTitle }}</h2>

                    <div class="contacto-form__grid">
                        <div class="contacto-field">
                            <label class="contacto-field__label" for="name">{{ $firstNameLabel }}</label>
                            <input class="contacto-field__input" type="text" id="name" name="name" value="{{ old('name') }}" required>
                            <span class="contacto-field__line contacto-field__line--half" aria-hidden="true"></span>
                            @error('name')<span class="contacto-field__error">{{ $message }}</span>@enderror
                        </div>

                        <div class="contacto-field">
                            <label class="contacto-field__label" for="last_name">{{ $lastNameLabel }}</label>
                            <input class="contacto-field__input" type="text" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                            <span class="contacto-field__line contacto-field__line--half" aria-hidden="true"></span>
                            @error('last_name')<span class="contacto-field__error">{{ $message }}</span>@enderror
                        </div>

                        <div class="contacto-field">
                            <label class="contacto-field__label" for="email">{{ $emailLabel }}</label>
                            <input class="contacto-field__input" type="email" id="email" name="email" value="{{ old('email') }}" required>
                            <span class="contacto-field__line contacto-field__line--half" aria-hidden="true"></span>
                            @error('email')<span class="contacto-field__error">{{ $message }}</span>@enderror
                        </div>

                        <div class="contacto-field">
                            <label class="contacto-field__label" for="phone">{{ $phoneLabel }}</label>
                            <input class="contacto-field__input" type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                            <span class="contacto-field__line contacto-field__line--half" aria-hidden="true"></span>
                            @error('phone')<span class="contacto-field__error">{{ $message }}</span>@enderror
                        </div>

                        <div class="contacto-field contacto-field--message">
                            <label class="contacto-field__label" for="message">{{ $messageLabel }}</label>
                            <textarea class="contacto-field__textarea" id="message" name="message" required>{{ old('message') }}</textarea>
                            <span class="contacto-field__line contacto-field__line--full" aria-hidden="true"></span>
                            @error('message')<span class="contacto-field__error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="contacto-form__actions">
                        <span class="contacto-form__required">*Campos obligatorios</span>
                        <button type="submit" class="contacto-form__btn">
                            <span class="contacto-form__btn-text">{{ $submitText }}</span>
                            <svg class="contacto-form__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                <path d="M5.83301 5.8335H14.1663M14.1663 5.8335V14.1668M14.1663 5.8335L5.83301 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endif
@endsection
