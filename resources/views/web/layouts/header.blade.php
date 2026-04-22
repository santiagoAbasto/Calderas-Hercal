<header class="hc-header" id="mainNav">
    <div class="hc-header__inner">
        <div class="hc-header__brand">
            <a href="{{ route('home') }}" class="hc-header__logo" aria-label="Calderas Hercal — Inicio">
                <img src="{{ cms_media_url('logo/logo-hercal.png') }}" alt="Calderas Hercal">
            </a>
        </div>

        <div class="hc-header__desktop">
            <nav class="hc-header__nav" aria-label="Navegación principal">
                <a href="{{ route('nosotros') }}" class="hc-header__nav-link{{ request()->routeIs('nosotros') ? ' hc-header__nav-link--active' : '' }}">Nosotros</a>
                <a href="{{ route('servicios.index') }}" class="hc-header__nav-link{{ request()->routeIs('servicios*') ? ' hc-header__nav-link--active' : '' }}">Servicios</a>
                <a href="{{ route('equipos.index') }}" class="hc-header__nav-link{{ request()->routeIs('equipos*') ? ' hc-header__nav-link--active' : '' }}">Equipos</a>
                <a href="{{ route('proyectos.index') }}" class="hc-header__nav-link{{ request()->routeIs('proyectos*') ? ' hc-header__nav-link--active' : '' }}">Trabajos realizados</a>
                <a href="{{ route('clientes') }}" class="hc-header__nav-link{{ request()->routeIs('clientes') ? ' hc-header__nav-link--active' : '' }}">Clientes</a>
                <a href="{{ route('contacto') }}" class="hc-header__nav-link{{ request()->routeIs('contacto*') ? ' hc-header__nav-link--active' : '' }}">Contacto</a>
            </nav>

            <div class="hc-header__actions">
                <button type="button" class="hc-header__search" id="headerSearchOpen" aria-label="Buscar">
                    <svg width="46" height="46" viewBox="0 0 46 46" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <circle cx="23" cy="23" r="23" fill="#D9D9D9"/>
                    </svg>
                    <svg class="hc-header__search-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M17.5 17.5L13.8833 13.8833M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="#202020" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </button>

                <a href="{{ route('presupuesto') }}" class="hc-header__cta">
                    <span class="hc-header__cta-text">Presupuesto</span>
                    <svg class="hc-header__cta-icon" width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M5.83325 5.8335H14.1666M14.1666 5.8335V14.1668M14.1666 5.8335L5.83325 14.1668" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
            </div>
        </div>

        <button class="hc-header__hamburger" id="hamburgerBtn" aria-label="Abrir menú" aria-expanded="false">
            <span class="hc-header__hamburger-line"></span>
            <span class="hc-header__hamburger-line"></span>
            <span class="hc-header__hamburger-line"></span>
        </button>
    </div>

    <div class="hc-header__overlay" id="navOverlay"></div>

    <nav class="hc-header__drawer" id="navLinks" aria-label="Navegación principal mobile">
        <button class="hc-header__drawer-close" id="navClose" aria-label="Cerrar menú">✕</button>

        <a href="{{ route('nosotros') }}" class="hc-header__drawer-link{{ request()->routeIs('nosotros') ? ' hc-header__drawer-link--active' : '' }}">Nosotros</a>
        <a href="{{ route('servicios.index') }}" class="hc-header__drawer-link{{ request()->routeIs('servicios*') ? ' hc-header__drawer-link--active' : '' }}">Servicios</a>
        <a href="{{ route('equipos.index') }}" class="hc-header__drawer-link{{ request()->routeIs('equipos*') ? ' hc-header__drawer-link--active' : '' }}">Equipos</a>
        <a href="{{ route('proyectos.index') }}" class="hc-header__drawer-link{{ request()->routeIs('proyectos*') ? ' hc-header__drawer-link--active' : '' }}">Trabajos realizados</a>
        <a href="{{ route('clientes') }}" class="hc-header__drawer-link{{ request()->routeIs('clientes') ? ' hc-header__drawer-link--active' : '' }}">Clientes</a>
        <a href="{{ route('contacto') }}" class="hc-header__drawer-link{{ request()->routeIs('contacto*') ? ' hc-header__drawer-link--active' : '' }}">Contacto</a>
        <button type="button" class="hc-header__drawer-search" id="headerSearchOpenMobile">Buscar</button>
        <a href="{{ route('presupuesto') }}" class="hc-header__drawer-cta">Presupuesto</a>
    </nav>

    <div class="hc-header-search" id="headerSearchModal" hidden>
        <div class="hc-header-search__backdrop" id="headerSearchBackdrop"></div>
        <div class="hc-header-search__panel" role="dialog" aria-modal="true" aria-label="Buscar en el sitio">
            <button type="button" class="hc-header-search__close" id="headerSearchClose" aria-label="Cerrar buscador">✕</button>
            <h2 class="hc-header-search__title">Buscar</h2>
            <p class="hc-header-search__text">Buscá servicios, equipos y trabajos realizados.</p>
            <form action="{{ route('search') }}" method="GET" class="hc-header-search__form" role="search">
                <div class="hc-header-search__field">
                    <svg class="hc-header-search__field-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                        <path d="M17.5 17.5L13.8833 13.8833M15.8333 9.16667C15.8333 12.8486 12.8486 15.8333 9.16667 15.8333C5.48477 15.8333 2.5 12.8486 2.5 9.16667C2.5 5.48477 5.48477 2.5 9.16667 2.5C12.8486 2.5 15.8333 5.48477 15.8333 9.16667Z" stroke="#202020" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <input type="search" name="q" id="headerSearchInput" placeholder="Buscar por nombre, descripción o tipo" autocomplete="off">
                </div>
                <button type="submit" class="hc-header-search__submit">Buscar</button>
            </form>
        </div>
    </div>
</header>
