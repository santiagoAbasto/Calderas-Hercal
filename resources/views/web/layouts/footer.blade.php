@php
$_email     = \App\Models\Setting::get('email',     'hercal.ter@gmail.com');
$_phone1    = \App\Models\Setting::get('phone_1',   '+54 11 2116 5379');
$_phone2    = \App\Models\Setting::get('phone_2',   '+54 11 3337 4083');
$_whatsapp  = \App\Models\Setting::get('whatsapp',  '541121165379');
$_instagram = \App\Models\Setting::get('instagram', null);
@endphp

<footer class="hc-footer">
    <div class="hc-footer__inner">
        <div class="hc-footer__main">
            <div class="hc-footer__brand">
                <a href="{{ route('home') }}" class="hc-footer__logo" aria-label="Calderas Hercal">
                    <img src="{{ cms_media_url('logo/logo-hercal.png') }}" alt="Calderas Hercal">
                </a>

                @if($_instagram)
                <a href="{{ $_instagram }}" class="hc-footer__instagram" target="_blank" rel="noopener noreferrer" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M17.5 6.5H17.51M7 2H17C19.7614 2 22 4.23858 22 7V17C22 19.7614 19.7614 22 17 22H7C4.23858 22 2 19.7614 2 17V7C2 4.23858 4.23858 2 7 2ZM16 11.37C16.1234 12.2022 15.9813 13.0522 15.5938 13.799C15.2063 14.5458 14.5931 15.1514 13.8416 15.5297C13.0901 15.9079 12.2384 16.0396 11.4078 15.9059C10.5771 15.7723 9.80976 15.3801 9.21484 14.7852C8.61992 14.1902 8.22773 13.4229 8.09407 12.5922C7.9604 11.7616 8.09207 10.9099 8.47033 10.1584C8.84859 9.40685 9.45419 8.79374 10.201 8.40624C10.9478 8.01874 11.7978 7.87659 12.63 8C13.4789 8.12588 14.2649 8.52146 14.8717 9.12831C15.4785 9.73515 15.8741 10.5211 16 11.37Z" stroke="#AA0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </a>
                @endif
            </div>

            <div class="hc-footer__nav-col">
                <h4 class="hc-footer__heading">Secciones</h4>
                <div class="hc-footer__links">
                    <div class="hc-footer__links-list">
                        <a href="{{ route('nosotros') }}">Nosotros</a>
                        <a href="{{ route('servicios.index') }}">Servicios</a>
                        <a href="{{ route('equipos.index') }}">Equipos</a>
                        <a href="{{ route('proyectos.index') }}">Trabajos realizados</a>
                    </div>
                    <div class="hc-footer__links-list">
                        <a href="{{ route('clientes') }}">Clientes</a>
                        <a href="{{ route('contacto') }}">Contacto</a>
                    </div>
                </div>
            </div>

            <div class="hc-footer__contact-col">
                <h4 class="hc-footer__heading">Información de Contacto</h4>
                <div class="hc-footer__contact-list">
                    @if($_email)
                    <a href="mailto:{{ $_email }}" class="hc-footer__contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M18.3334 5.83337L10.8584 10.5834C10.6011 10.7446 10.3037 10.83 10.0001 10.83C9.69648 10.83 9.39902 10.7446 9.14175 10.5834L1.66675 5.83337M3.33341 3.33337H16.6667C17.5872 3.33337 18.3334 4.07957 18.3334 5.00004V15C18.3334 15.9205 17.5872 16.6667 16.6667 16.6667H3.33341C2.41294 16.6667 1.66675 15.9205 1.66675 15V5.00004C1.66675 4.07957 2.41294 3.33337 3.33341 3.33337Z" stroke="#AA0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>{{ $_email }}</span>
                    </a>
                    @endif

                    @if($_phone1)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $_phone1) }}" target="_blank" rel="noopener noreferrer" class="hc-footer__contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <g clip-path="url(#clip0_footer_phone_1)">
                                <path d="M18.3334 14.1V16.6C18.3344 16.8321 18.2868 17.0618 18.1939 17.2745C18.1009 17.4871 17.9645 17.678 17.7935 17.8349C17.6225 17.9918 17.4206 18.1113 17.2007 18.1856C16.9809 18.26 16.7479 18.2876 16.5168 18.2667C13.9525 17.9881 11.4893 17.1118 9.32511 15.7084C7.31163 14.4289 5.60455 12.7219 4.32511 10.7084C2.91676 8.53438 2.04031 6.0592 1.76677 3.48337C1.74595 3.25293 1.77334 3.02067 1.84719 2.80139C1.92105 2.58211 2.03975 2.38061 2.19575 2.20972C2.35174 2.03883 2.54161 1.9023 2.75327 1.80881C2.96492 1.71532 3.19372 1.66692 3.42511 1.66671H5.92511C6.32953 1.66273 6.7216 1.80594 7.02824 2.06965C7.33488 2.33336 7.53517 2.69958 7.59177 3.10004C7.69729 3.9001 7.89298 4.68565 8.17511 5.44171C8.28723 5.73998 8.31149 6.06414 8.24503 6.37577C8.17857 6.68741 8.02416 6.97347 7.80011 7.20004L6.74177 8.25837C7.92807 10.3447 9.65549 12.0721 11.7418 13.2584L12.8001 12.2C13.0267 11.976 13.3127 11.8216 13.6244 11.7551C13.936 11.6887 14.2602 11.7129 14.5584 11.825C15.3145 12.1072 16.1001 12.3029 16.9001 12.4084C17.3049 12.4655 17.6746 12.6694 17.9389 12.9813C18.2032 13.2932 18.3436 13.6914 18.3334 14.1Z" stroke="#AA0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_footer_phone_1">
                                    <rect width="20" height="20" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <span>{{ $_phone1 }}</span>
                    </a>
                    @endif

                    @if($_phone2)
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $_phone2) }}" target="_blank" rel="noopener noreferrer" class="hc-footer__contact-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <g clip-path="url(#clip0_footer_phone_2)">
                                <path d="M18.3334 14.1V16.6C18.3344 16.8321 18.2868 17.0618 18.1939 17.2745C18.1009 17.4871 17.9645 17.678 17.7935 17.8349C17.6225 17.9918 17.4206 18.1113 17.2007 18.1856C16.9809 18.26 16.7479 18.2876 16.5168 18.2667C13.9525 17.9881 11.4893 17.1118 9.32511 15.7084C7.31163 14.4289 5.60455 12.7219 4.32511 10.7084C2.91676 8.53438 2.04031 6.0592 1.76677 3.48337C1.74595 3.25293 1.77334 3.02067 1.84719 2.80139C1.92105 2.58211 2.03975 2.38061 2.19575 2.20972C2.35174 2.03883 2.54161 1.9023 2.75327 1.80881C2.96492 1.71532 3.19372 1.66692 3.42511 1.66671H5.92511C6.32953 1.66273 6.7216 1.80594 7.02824 2.06965C7.33488 2.33336 7.53517 2.69958 7.59177 3.10004C7.69729 3.9001 7.89298 4.68565 8.17511 5.44171C8.28723 5.73998 8.31149 6.06414 8.24503 6.37577C8.17857 6.68741 8.02416 6.97347 7.80011 7.20004L6.74177 8.25837C7.92807 10.3447 9.65549 12.0721 11.7418 13.2584L12.8001 12.2C13.0267 11.976 13.3127 11.8216 13.6244 11.7551C13.936 11.6887 14.2602 11.7129 14.5584 11.825C15.3145 12.1072 16.1001 12.3029 16.9001 12.4084C17.3049 12.4655 17.6746 12.6694 17.9389 12.9813C18.2032 13.2932 18.3436 13.6914 18.3334 14.1Z" stroke="#AA0000" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                                <clipPath id="clip0_footer_phone_2">
                                    <rect width="20" height="20" fill="white"/>
                                </clipPath>
                            </defs>
                        </svg>
                        <span>{{ $_phone2 }}</span>
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="hc-footer__bottom">
            <div class="hc-footer__bottom-inner">
                <span class="hc-footer__copyright">© Copyright {{ date('Y') }} <strong>Calderas Hercal</strong>. Todos los derechos reservados.</span>
                <span class="hc-footer__credits">by <a href="https://osole.com.ar/" target="_blank" rel="noopener noreferrer">Osole</a></span>
            </div>
        </div>
    </div>

    {{-- WHATSAPP FLOTANTE --}}
    @if($_whatsapp)
    <a href="https://wa.me/{{ preg_replace('/\D/', '', $_whatsapp) }}"
       target="_blank" rel="noopener noreferrer"
       class="hc-whatsapp-btn" aria-label="Contactar por WhatsApp">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
            <path fill-rule="evenodd" clip-rule="evenodd" d="M23.3317 19.176C22.9325 18.9774 20.9751 18.02 20.6107 17.8867C20.2463 17.7547 19.981 17.6894 19.7144 18.0867C19.4491 18.4814 18.6868 19.3747 18.4551 19.6387C18.2219 19.904 17.9902 19.936 17.5923 19.7387C17.1943 19.5387 15.9109 19.1214 14.3903 17.772C13.2073 16.7214 12.4074 15.424 12.1756 15.0267C11.9439 14.6307 12.1502 14.416 12.3498 14.2187C12.5293 14.0414 12.7477 13.756 12.9473 13.5254C13.147 13.2934 13.2126 13.128 13.3452 12.8627C13.4792 12.5987 13.4122 12.368 13.3118 12.1694C13.2126 11.9707 12.4168 10.02 12.0845 9.22671C11.7617 8.45471 11.4334 8.56004 11.1896 8.54671C10.9565 8.53604 10.6912 8.53337 10.4259 8.53337C10.1607 8.53337 9.72926 8.63204 9.36485 9.02937C8.9991 9.42537 7.97151 10.384 7.97151 12.3347C7.97151 14.284 9.39701 16.168 9.59663 16.4334C9.79625 16.6974 12.4034 20.7 16.3972 22.416C17.3484 22.824 18.0893 23.068 18.6667 23.2493C19.6206 23.552 20.4888 23.5093 21.1747 23.4067C21.9384 23.2933 23.53 22.448 23.8623 21.5227C24.1932 20.5974 24.1932 19.804 24.0941 19.6387C23.9949 19.4734 23.7296 19.3747 23.3304 19.176H23.3317ZM16.0676 29.0467H16.0623C13.6901 29.0471 11.3616 28.4125 9.32064 27.2093L8.83833 26.924L3.82499 28.2333L5.1634 23.3693L4.84855 22.8707C3.52239 20.7698 2.82057 18.3384 2.82419 15.8574C2.82687 8.59071 8.76732 2.67872 16.073 2.67872C19.6099 2.67872 22.9352 4.05205 25.4352 6.54271C26.6682 7.76482 27.6456 9.21813 28.3106 10.8186C28.9757 12.419 29.3153 14.1348 29.3097 15.8667C29.307 23.1333 23.3666 29.0467 16.0676 29.0467ZM27.3376 4.65071C25.8614 3.17195 24.1051 1.99943 22.1703 1.20112C20.2355 0.402806 18.1608 -0.00543499 16.0663 5.46367e-05C7.28556 5.46367e-05 0.136654 7.11338 0.133975 15.856C0.129906 18.6384 0.863301 21.3725 2.26016 23.7827L0 32L8.44578 29.7947C10.7821 31.0615 13.4003 31.7253 16.0609 31.7253H16.0676C24.8483 31.7253 31.9972 24.612 31.9999 15.868C32.0064 13.7844 31.5977 11.7202 30.7974 9.79475C29.9971 7.86933 28.8212 6.12094 27.3376 4.65071Z" fill="white"/>
        </svg>
    </a>
    @endif

</footer>

@if($_whatsapp)
@push('scripts')
<script>
(() => {
  const whatsappButton = document.querySelector('.hc-whatsapp-btn');
  if (!whatsappButton) return;

  let touchHandled = false;

  whatsappButton.addEventListener('touchend', (event) => {
    touchHandled = true;
    event.preventDefault();
    window.open(whatsappButton.href, '_blank', 'noopener');
  }, { passive: false });

  whatsappButton.addEventListener('click', (event) => {
    if (touchHandled) {
      touchHandled = false;
      event.preventDefault();
    }
  });
})();
</script>
@endpush
@endif
