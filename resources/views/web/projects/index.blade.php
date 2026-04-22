@extends('web.layouts.app')

@php
$banner = $page?->sections->where('section_key', 'trabajos_banner')->first();
$bannerMedia = $banner?->activeMedia->first();
$bannerMeta = $banner?->meta ?? [];
$pageTitle = $banner?->title ?? 'Trabajos realizados';
$breadcrumbHomeText = $bannerMeta['breadcrumb_home_text'] ?? 'Inicio';
$breadcrumbCurrentText = $bannerMeta['breadcrumb_current_text'] ?? 'Trabajos realizados';
$cardButtonText = $bannerMeta['card_button_text'] ?? 'Ver más';
@endphp

@section('content')
@if($banner && $bannerMedia)
<section class="trabajos-banner">
    @if($bannerMedia)
        @if($bannerMedia->type === 'image' && $bannerMedia->file_url)
            <img
                class="trabajos-banner__img"
                src="{{ $bannerMedia->file_url }}"
                alt="{{ $bannerMedia->alt_text ?? 'Trabajos realizados Calderas Hercal' }}"
                aria-hidden="true">
        @elseif($bannerMedia->type === 'video' && $bannerMedia->file_url)
            <video
                class="trabajos-banner__video"
                autoplay muted loop playsinline
                aria-hidden="true">
                <source src="{{ $bannerMedia->file_url }}" type="video/mp4">
            </video>
        @elseif($bannerMedia->type === 'youtube' && $bannerMedia->youtube_embed)
            <iframe
                class="trabajos-banner__iframe"
                src="{{ $bannerMedia->youtube_embed }}?autoplay=1&mute=1&loop=1&playlist={{ last(explode('/', trim(parse_url($bannerMedia->youtube_url, PHP_URL_PATH), '/'))) }}&controls=0&showinfo=0&rel=0&iv_load_policy=3&modestbranding=1&disablekb=1"
                allow="autoplay; encrypted-media"
                allowfullscreen
                aria-hidden="true">
            </iframe>
        @endif
    @endif

    <div class="trabajos-banner__overlay" aria-hidden="true"></div>

    <div class="trabajos-banner__content">
        <h1 class="trabajos-banner__title">{{ $pageTitle }}</h1>
        <nav class="trabajos-banner__breadcrumb" aria-label="Migas de pan">
            <a href="{{ route('home') }}">{{ $breadcrumbHomeText }}</a>
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M7.5 15L12.5 10L7.5 5" stroke="#F5FBFE" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            <span>{{ $breadcrumbCurrentText }}</span>
        </nav>
    </div>
</section>
@endif

<section class="trabajos-page">
    <div class="hc-container">
        <div class="trabajos-page__grid">
            @forelse($projects as $project)
            <div class="trabajos-card" data-project="{{ $project->id }}" role="button" tabindex="0" aria-label="Ver galería: {{ $project->title }}">
                <div class="trabajos-card__img">
                    <img src="{{ $project->cover_image_url }}" alt="{{ $project->title }}" loading="lazy">
                </div>
                <div class="trabajos-card__body">
                    <span class="trabajos-card__title">{{ $project->title }}</span>
                    <button class="trabajos-card__btn" aria-hidden="true" tabindex="-1">
                        <svg class="trabajos-card__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5.83325 5.83337H14.1666M14.1666 5.83337V14.1667M14.1666 5.83337L5.83325 14.1667" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </div>
            </div>
            @empty
            <p class="hc-empty">No hay trabajos disponibles.</p>
            @endforelse
        </div>
    </div>
</section>

<div class="trabajos-modal" id="trabajosProjectModal" role="dialog" aria-modal="true" aria-label="Galería de proyecto" hidden>
    <div class="trabajos-modal__backdrop" id="trabajosModalBackdrop"></div>
    <div class="trabajos-modal__box">
        <button class="trabajos-modal__close" id="trabajosModalClose" aria-label="Cerrar galería">
            <svg xmlns="http://www.w3.org/2000/svg" width="37" height="37" viewBox="0 0 37 37" fill="none" aria-hidden="true">
                <path d="M18.4997 20.6585L22.9705 25.1293C23.2531 25.412 23.6129 25.5533 24.0497 25.5533C24.4865 25.5533 24.8462 25.412 25.1288 25.1293C25.4115 24.8467 25.5528 24.487 25.5528 24.0502C25.5528 23.6134 25.4115 23.2536 25.1288 22.971L20.658 18.5002L25.1288 14.0293C25.4115 13.7467 25.5528 13.387 25.5528 12.9502C25.5528 12.5134 25.4115 12.1536 25.1288 11.871C24.8462 11.5884 24.4865 11.447 24.0497 11.447C23.6129 11.447 23.2531 11.5884 22.9705 11.871L18.4997 16.3418L14.0288 11.871C13.7462 11.5884 13.3865 11.447 12.9497 11.447C12.5129 11.447 12.1531 11.5884 11.8705 11.871C11.5879 12.1536 11.4466 12.5134 11.4466 12.9502C11.4466 13.387 11.5879 13.7467 11.8705 14.0293L16.3413 18.5002L11.8705 22.971C11.5879 23.2536 11.4466 23.6134 11.4466 24.0502C11.4466 24.487 11.5879 24.8467 11.8705 25.1293C12.1531 25.412 12.5129 25.5533 12.9497 25.5533C13.3865 25.5533 13.7462 25.412 14.0288 25.1293L18.4997 20.6585ZM18.4997 33.9168C16.367 33.9168 14.3629 33.5119 12.4872 32.702C10.6115 31.8921 8.97988 30.7939 7.59238 29.4075C6.20489 28.021 5.1067 26.3894 4.29784 24.5127C3.48898 22.6359 3.08404 20.6318 3.08301 18.5002C3.08198 16.3686 3.48693 14.3644 4.29784 12.4877C5.10876 10.6109 6.20694 8.97934 7.59238 7.59287C8.97783 6.2064 10.6094 5.10822 12.4872 4.29833C14.3649 3.48844 16.3691 3.0835 18.4997 3.0835C20.6303 3.0835 22.6344 3.48844 24.5122 4.29833C26.3899 5.10822 28.0215 6.2064 29.407 7.59287C30.7924 8.97934 31.8911 10.6109 32.7031 12.4877C33.515 14.3644 33.9194 16.3686 33.9163 18.5002C33.9133 20.6318 33.5083 22.6359 32.7015 24.5127C31.8947 26.3894 30.7965 28.021 29.407 29.4075C28.0174 30.7939 26.3858 31.8926 24.5122 32.7035C22.6385 33.5145 20.6344 33.9189 18.4997 33.9168ZM18.4997 30.8335C21.9427 30.8335 24.859 29.6387 27.2486 27.2491C29.6382 24.8595 30.833 21.9432 30.833 18.5002C30.833 15.0571 29.6382 12.1408 27.2486 9.7512C24.859 7.36162 21.9427 6.16683 18.4997 6.16683C15.0566 6.16683 12.1403 7.36162 9.75072 9.7512C7.36113 12.1408 6.16634 15.0571 6.16634 18.5002C6.16634 21.9432 7.36113 24.8595 9.75072 27.2491C12.1403 29.6387 15.0566 30.8335 18.4997 30.8335Z" fill="#AEAEB2"/>
            </svg>
        </button>
        <div class="trabajos-modal__slider">
            <div class="trabajos-modal__slides" id="trabajosModalSlides"></div>
        </div>
        <div class="trabajos-modal__dots" id="trabajosModalDots"></div>
    </div>
</div>

<script>
var trabajosProjectsData = {};
@foreach($projects as $project)
trabajosProjectsData[{{ $project->id }}] = {
    title: "{{ addslashes($project->title) }}",
    images: [
        @if($project->cover_image)
        { url: "{{ $project->cover_image_url }}", alt: "{{ addslashes($project->title) }}" },
        @endif
        @foreach($project->images->where('is_active', true)->sortBy('order') as $img)
        { url: "{{ $img->image_url }}", alt: "{{ addslashes($project->title) }}" },
        @endforeach
        @if(!$project->cover_image && $project->images->where('is_active', true)->isEmpty())
        { url: "{{ $project->cover_image_url }}", alt: "{{ addslashes($project->title) }}" }
        @endif
    ]
};
@endforeach
</script>

@push('scripts')
<script>
(() => {
  const modal = document.getElementById('trabajosProjectModal');
  const backdrop = document.getElementById('trabajosModalBackdrop');
  const slidesEl = document.getElementById('trabajosModalSlides');
  const dotsEl = document.getElementById('trabajosModalDots');
  const btnClose = document.getElementById('trabajosModalClose');
  if (!modal || !slidesEl || !dotsEl || !btnClose) return;

  let current = 0;
  let pages = [];
  let currentProjectId = null;
  let touchStartX = 0;
  let touchStartY = 0;
  let lockedScrollY = 0;

  const normalizeImages = (images) => {
    const seen = new Set();

    return images.filter((image) => {
      if (!image || !image.url || seen.has(image.url)) return false;
      seen.add(image.url);
      return true;
    });
  };

  const buildPages = (images) => {
    const normalized = normalizeImages(images);
    const itemsPerPage = window.innerWidth <= 768 ? 1 : 2;
    const grouped = [];

    for (let i = 0; i < normalized.length; i += itemsPerPage) {
      grouped.push(normalized.slice(i, i + itemsPerPage));
    }

    return grouped;
  };

  const blockProjectImageActions = () => {
    document.querySelectorAll('.trabajos-card__img img, .trabajos-modal__media img').forEach((image) => {
      image.addEventListener('contextmenu', (event) => event.preventDefault());
      image.addEventListener('dragstart', (event) => event.preventDefault());
    });
  };

  const render = () => {
    slidesEl.innerHTML = '';
    dotsEl.innerHTML = '';

    pages.forEach((page, index) => {
      const slide = document.createElement('div');
      slide.className = 'trabajos-modal__slide' + (index === current ? ' is-active' : '');

      page.forEach((img) => {
        const media = document.createElement('div');
        media.className = 'trabajos-modal__media';
        const image = document.createElement('img');
        image.src = img.url;
        image.alt = img.alt;
        image.loading = 'lazy';
        media.appendChild(image);
        slide.appendChild(media);
      });

      slidesEl.appendChild(slide);

      const dot = document.createElement('button');
      dot.type = 'button';
      dot.className = 'trabajos-modal__dot' + (index === current ? ' is-active' : '');
      dot.setAttribute('aria-label', 'Página ' + (index + 1));
      dot.addEventListener('click', () => goTo(index));
      dotsEl.appendChild(dot);
    });

    dotsEl.hidden = pages.length <= 1;
    blockProjectImageActions();
  };

  const goTo = (index) => {
    current = Math.max(0, Math.min(index, pages.length - 1));
    render();
  };

  const openModal = (projectId) => {
    const data = trabajosProjectsData[projectId];
    if (!data || !data.images.length) return;

    currentProjectId = projectId;
    pages = buildPages(data.images);
    current = 0;
    render();
    modal.hidden = false;
    lockedScrollY = window.scrollY;
    document.body.style.position = 'fixed';
    document.body.style.top = `-${lockedScrollY}px`;
    document.body.style.left = '0';
    document.body.style.right = '0';
    document.body.style.width = '100%';
    document.body.style.overflow = 'hidden';
  };

  const closeModal = () => {
    modal.hidden = true;
    document.body.style.position = '';
    document.body.style.top = '';
    document.body.style.left = '';
    document.body.style.right = '';
    document.body.style.width = '';
    document.body.style.overflow = '';
    window.scrollTo(0, lockedScrollY);
  };

  document.querySelectorAll('.trabajos-card').forEach((card) => {
    card.addEventListener('click', () => openModal(parseInt(card.dataset.project, 10)));
    card.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        openModal(parseInt(card.dataset.project, 10));
      }
    });
  });

  btnClose.addEventListener('click', closeModal);
  backdrop.addEventListener('click', closeModal);
  slidesEl.addEventListener('touchstart', (event) => {
    const touch = event.changedTouches[0];
    touchStartX = touch.clientX;
    touchStartY = touch.clientY;
  }, { passive: true });

  slidesEl.addEventListener('touchmove', (event) => {
    if (modal.hidden || pages.length <= 1) return;

    const touch = event.changedTouches[0];
    const deltaX = touch.clientX - touchStartX;
    const deltaY = touch.clientY - touchStartY;

    if (Math.abs(deltaX) > Math.abs(deltaY)) {
      event.preventDefault();
    }
  }, { passive: false });

  slidesEl.addEventListener('touchend', (event) => {
    if (modal.hidden || pages.length <= 1) return;

    const touch = event.changedTouches[0];
    const deltaX = touch.clientX - touchStartX;
    const deltaY = touch.clientY - touchStartY;

    if (Math.abs(deltaX) < 30 || Math.abs(deltaX) <= Math.abs(deltaY)) return;

    if (deltaX < 0) {
      goTo(current + 1);
    } else {
      goTo(current - 1);
    }
  }, { passive: true });

  document.addEventListener('keydown', (event) => {
    if (modal.hidden) return;
    if (event.key === 'Escape') closeModal();
    if (event.key === 'ArrowRight') goTo(current + 1);
    if (event.key === 'ArrowLeft') goTo(current - 1);
  });

  window.addEventListener('resize', () => {
    if (modal.hidden || currentProjectId === null || !trabajosProjectsData[currentProjectId]) return;
    pages = buildPages(trabajosProjectsData[currentProjectId].images);
    current = Math.min(current, pages.length - 1);
    render();
  });

  blockProjectImageActions();
})();
</script>
@endpush
@endsection
