<!DOCTYPE html>
<html lang="es">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

@php
$_seoPage = \App\Support\SeoPageResolver::resolve(request());
$_baseTitle = $__env->hasSection('title')
    ? trim($__env->yieldContent('title'))
    : trim((string) ($_seoPage?->meta_title ?? ''));
$_title = $_baseTitle !== ''
    ? (str_contains(mb_strtolower($_baseTitle), 'calderas hercal') ? $_baseTitle : $_baseTitle . ' — Calderas Hercal')
    : 'Calderas Hercal — Calderas y Termotanques Industriales';

$_desc = $__env->hasSection('meta_description')
    ? trim($__env->yieldContent('meta_description'))
    : trim((string) ($_seoPage?->meta_description ?? 'Fabricamos calderas y termotanques industriales de última generación. Más de 35 años de experiencia en seguridad, eficiencia y confianza.'));
$_keywords = $__env->hasSection('meta_keywords')
    ? trim($__env->yieldContent('meta_keywords'))
    : trim((string) ($_seoPage?->meta_keywords ?? ''));
$_canonical = $__env->hasSection('canonical_url')
    ? trim($__env->yieldContent('canonical_url'))
    : trim((string) ($_seoPage?->canonical_url ?? url()->current()));
$_robots = trim((string) ($_seoPage?->robots ?? 'index,follow'));
$_ogImage = $__env->hasSection('og_image')
    ? trim($__env->yieldContent('og_image'))
    : trim((string) ($_seoPage?->og_image_url ?? ''));
$_twitterCard = $_ogImage !== '' ? 'summary_large_image' : 'summary';
@endphp

<title>{{ $_title }}</title>
<meta name="description" content="{{ $_desc }}">
@if($_keywords !== '')
<meta name="keywords" content="{{ $_keywords }}">
@endif
<meta name="robots" content="{{ $_robots }}">
<link rel="canonical" href="{{ $_canonical }}">
<meta name="csrf-token" content="{{ csrf_token() }}">

<meta property="og:type"        content="website">
<meta property="og:site_name"   content="Calderas Hercal">
<meta property="og:url"         content="{{ $_canonical }}">
<meta property="og:title"       content="{{ $_title }}">
<meta property="og:description" content="{{ $_desc }}">
@if($_ogImage !== '')
<meta property="og:image"       content="{{ $_ogImage }}">
<meta name="twitter:image"      content="{{ $_ogImage }}">
@endif
<meta name="twitter:card"       content="{{ $_twitterCard }}">
<meta name="twitter:title"      content="{{ $_title }}">
<meta name="twitter:description" content="{{ $_desc }}">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800&family=Rubik:wght@400;500;600;700&display=swap" rel="stylesheet">
{{-- Mona Sans se carga via @font-face en hercal/variables.css --}}

@vite(['resources/css/web.css', 'resources/js/web.js'])

@stack('styles')

</head>
<body>

@include('web.layouts.header')

<main>
@yield('content')
</main>

@include('web.layouts.footer')

{{-- TOAST --}}
@if(session('success') || session('error'))
<div id="web-toast" class="web-toast {{ session('error') ? 'web-toast--error' : 'web-toast--success' }}">
    <div class="web-toast__icon">
        @if(session('error'))
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
        @endif
    </div>
    <span class="web-toast__msg">{{ session('success') ?? session('error') }}</span>
    <button class="web-toast__close" onclick="document.getElementById('web-toast').remove()" aria-label="Cerrar">✕</button>
</div>
<script>
setTimeout(function(){
    var t = document.getElementById('web-toast');
    if(t){ t.classList.add('web-toast--hide'); setTimeout(function(){ if(t) t.remove(); }, 400); }
}, 4500);
</script>
@endif

@stack('scripts')

</body>
</html>
