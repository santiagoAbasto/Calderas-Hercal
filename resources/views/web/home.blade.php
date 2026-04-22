@extends('web.layouts.app')

@section('content')

@php
$heroSection     = $home?->sections->firstWhere('section_key', 'hero');
$equipSection    = $home?->sections->firstWhere('section_key', 'equipments');
$bannerSection   = $home?->sections->firstWhere('section_key', 'hero_banner');
$servicesSection = $home?->sections->firstWhere('section_key', 'services');
$aboutSection    = $home?->sections->firstWhere('section_key', 'about_preview');
$projectsSection = $home?->sections->firstWhere('section_key', 'projects');
@endphp

@if($heroSection && $heroSection->activeMedia->count() > 0)
@include('web.components.hero',          ['section' => $heroSection])
@endif

@include('web.components.equipments',    ['section' => $equipSection,    'equipments' => $equipments])

@if($bannerSection)
@include('web.components.banner',        ['section' => $bannerSection])
@endif

@include('web.components.services',     ['section' => $servicesSection, 'services' => $services])

@if($aboutSection)
@include('web.components.about-preview', ['section' => $aboutSection])
@endif

@include('web.components.projects',     ['section' => $projectsSection, 'projects' => $projects])

@endsection
