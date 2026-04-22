@php
$slides = ($section?->activeMedia ?? collect())
    ->sort(static fn ($left, $right) => \App\Support\CmsOrder::compare($left->order, $right->order))
    ->values();
$fallbackTitle = $section?->title ?? 'Calderas y termotanques de última generación';
$fallbackBtnText = $section?->meta['button_text'] ?? 'Ver equipos';
$fallbackBtnUrl = $section?->meta['button_url'] ?? route('equipos.index');
$firstSlide = $slides->first();
$heroTitle = $firstSlide?->title ?: $fallbackTitle;
$btnText = $firstSlide?->button_text ?: $fallbackBtnText;
$btnUrl = $firstSlide?->button_url ?: $fallbackBtnUrl;
$slidesPayload = $slides->map(fn ($slide) => [
    'title' => $slide->title ?: $fallbackTitle,
    'button_text' => $slide->button_text ?: $fallbackBtnText,
    'button_url' => $slide->button_url ?: $fallbackBtnUrl,
])->values();
$slideCount = $slides->count();
@endphp

@if($slideCount === 0)
    @php return; @endphp
@endif

<section class="hc-hero" data-slides="{{ $slideCount }}">

    {{-- SLIDES (backgrounds) --}}
    @if($slideCount)
        @foreach($slides as $i => $slide)
            @if($slide->type === 'youtube' && $slide->youtube_embed)
            <div class="hc-hero__slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                <iframe
                    src="{{ $i === 0 ? $slide->youtube_embed . '?autoplay=1&mute=1&loop=1&controls=0&rel=0&playlist=' . basename($slide->youtube_embed) : '' }}"
                    data-src="{{ $slide->youtube_embed }}?autoplay=1&mute=1&loop=1&controls=0&rel=0&playlist={{ basename($slide->youtube_embed) }}"
                    allow="autoplay; encrypted-media"
                    allowfullscreen
                    title="Video slide {{ $i + 1 }}">
                </iframe>
            </div>
            @elseif($slide->type === 'video' && $slide->file_url)
            <div class="hc-hero__slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}">
                <video autoplay muted loop playsinline>
                    <source src="{{ $slide->file_url }}" type="video/mp4">
                </video>
            </div>
            @else
            <div class="hc-hero__slide {{ $i === 0 ? 'active' : '' }}" data-index="{{ $i }}"
                 style="background-image: url('{{ $slide->file_url }}')">
            </div>
            @endif
        @endforeach
    @endif

    {{-- DARK OVERLAY --}}
    <div class="hc-hero__overlay"></div>

    {{-- TEXT CONTENT --}}
    <div class="hc-hero__content">
        <h1 class="hc-hero__title">{!! $heroTitle !!}</h1>
        <a href="{{ $btnUrl }}" class="hc-hero__btn">
            <span class="hc-hero__btn-text">{{ $btnText }}</span>
            <svg class="hc-hero__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                <path d="M5.83325 5.8335H14.1666M14.1666 5.8335V14.1668M14.1666 5.8335L5.83325 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </a>
    </div>

    {{-- DOTS --}}
    @if($slideCount > 1)
    <div class="hc-hero__dots">
        @foreach($slides as $i => $slide)
        <button class="hc-hero__dot {{ $i === 0 ? 'active' : '' }}"
                data-index="{{ $i }}"
                aria-label="Ir al slide {{ $i + 1 }}">
        </button>
        @endforeach
    </div>
    @endif

</section>

@if($slideCount > 0)
<script type="application/json" id="hc-hero-slides-data">@json($slidesPayload)</script>
@endif

@push('scripts')
<script>
(function(){
    var hero = document.querySelector('.hc-hero');
    if(!hero) return;
    var slides = hero.querySelectorAll('.hc-hero__slide');
    var dots   = hero.querySelectorAll('.hc-hero__dot');
    var title = hero.querySelector('.hc-hero__title');
    var button = hero.querySelector('.hc-hero__btn');
    var buttonText = hero.querySelector('.hc-hero__btn-text');
    var payloadNode = document.getElementById('hc-hero-slides-data');
    var content = [];

    if (payloadNode) {
        try {
            content = JSON.parse(payloadNode.textContent || '[]');
        } catch (error) {
            content = [];
        }
    }

    var current = 0, timer = null;

    function syncContent(i){
        var slide = content[i];
        if(!slide) return;

        if(title) title.innerHTML = slide.title || '';

        if(button && buttonText){
            buttonText.textContent = slide.button_text || '';
            button.setAttribute('href', slide.button_url || '#');
            button.style.display = slide.button_text ? '' : 'none';
        }
    }

    function goTo(i){
        slides[current].classList.remove('active');
        if(dots[current]) dots[current].classList.remove('active');
        current = (i + slides.length) % slides.length;
        slides[current].classList.add('active');
        if(dots[current]) dots[current].classList.add('active');
        syncContent(current);

        // Lazy-load YouTube iframes
        var iframe = slides[current].querySelector('iframe[data-src]');
        if(iframe && !iframe.src){ iframe.src = iframe.getAttribute('data-src'); }
    }

    syncContent(0);
    if(slides.length <= 1) return;

    function start(){ timer = setInterval(function(){ goTo(current + 1); }, 5000); }
    function stop(){ clearInterval(timer); }

    dots.forEach(function(dot){
        dot.addEventListener('click', function(){
            stop(); goTo(parseInt(this.dataset.index, 10)); start();
        });
    });

    start();
}());
</script>
@endpush
