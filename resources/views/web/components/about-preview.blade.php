@php
$aboutTitle  = $section?->title       ?? 'Seguridad, eficiencia y confianza';
$aboutDesc   = $section?->description ?? 'Fabricamos las calderas más seguras, de mayor eficiencia, de mayor confiabilidad en su operación y de mayor vida útil del mercado, utilizando únicamente tecnología de punta a nivel mundial, con los mejores materiales que existen en el mercado internacional.';
$aboutMedia  = $section?->activeMedia->first();
$aboutMeta   = $section?->meta ?? [];
$years       = $aboutMeta['years']       ?? 35;
$clients     = $aboutMeta['clients']     ?? 3000;
$projectsNum = $aboutMeta['projects']    ?? 2500;
$btnUrl      = $aboutMeta['button_url']  ?? route('nosotros');
$btnText     = $aboutMeta['button_text'] ?? 'Ver más';
$descExtra   = $aboutMeta['description_extra'] ?? null;
@endphp

<section class="hc-section hc-about-preview">
    <div class="hc-container">
        <div class="hc-about-preview__grid">

            {{-- IMAGE --}}
            <div class="hc-about-preview__image">
                @if($aboutMedia)
                <img src="{{ $aboutMedia->file_url }}"
                     alt="{{ $aboutMedia->alt_text ?? 'Calderas Hercal' }}"
                     loading="lazy">
                @else
                <div class="hc-about-preview__img-placeholder"></div>
                @endif
            </div>

            {{-- CONTENT --}}
            <div class="hc-about-preview__content">
                <div class="hc-about-preview__title">{!! $aboutTitle !!}</div>

                <div class="hc-about-preview__desc">
                    {!! $aboutDesc !!}
                    @if($descExtra)
                    {!! $descExtra !!}
                    @endif
                </div>

                <a href="{{ $btnUrl }}" class="hc-about-preview__btn">
                    <span class="hc-about-preview__btn-text">{!! $btnText !!}</span>
                    <svg class="hc-about-preview__btn-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M5.83325 5.83325H14.1666M14.1666 5.83325V14.1666M14.1666 5.83325L5.83325 14.1666" stroke="#D50101" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>

                <div class="hc-metrics">
                    <div class="hc-metric hc-metric--left">
                        <span class="hc-metric__value" data-counter-target="{{ $years }}">+1</span>
                        <span class="hc-metric__label">Años de experiencia</span>
                    </div>
                    <div class="hc-metric">
                        <span class="hc-metric__value" data-counter-target="{{ $clients }}">+1</span>
                        <span class="hc-metric__label">Clientes</span>
                    </div>
                    <div class="hc-metric hc-metric--right">
                        <span class="hc-metric__value" data-counter-target="{{ $projectsNum }}">+1</span>
                        <span class="hc-metric__label">Obras realizadas</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

@push('scripts')
<script>
(function () {
    var section = document.querySelector('.hc-about-preview');
    if (!section) return;

    var counters = section.querySelectorAll('.hc-metric__value[data-counter-target]');
    if (!counters.length) return;

    var hasAnimated = false;

    function animateCounter(el) {
        var target = parseInt(el.getAttribute('data-counter-target'), 10);
        if (!target || target < 1) {
            el.textContent = '+0';
            return;
        }

        var start = 1;
        var duration = 1400;
        var startTime = null;

        function tick(timestamp) {
            if (!startTime) startTime = timestamp;
            var progress = Math.min((timestamp - startTime) / duration, 1);
            var current = Math.floor(start + (target - start) * progress);
            el.textContent = '+' + current;

            if (progress < 1) {
                window.requestAnimationFrame(tick);
            } else {
                el.textContent = '+' + target;
            }
        }

        window.requestAnimationFrame(tick);
    }

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting && !hasAnimated) {
                hasAnimated = true;
                counters.forEach(animateCounter);
                observer.disconnect();
            }
        });
    }, { threshold: 0.35 });

    observer.observe(section);
}());
</script>
@endpush
