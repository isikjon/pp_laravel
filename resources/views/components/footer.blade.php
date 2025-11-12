<footer>
    <div class="container">
        <div class="flexContainerFooter">
            <div class="top-flexContainerFooter">
                <img src="{{ cached_asset('img/footerLogo.svg') }}" alt="" width="233" height="41" loading="lazy" decoding="async">
                <div class="right-top-flexContainerFooter">
                    <a href="{{ route('home') }}">
                        Индивидуалки
                    </a>
                    <a href="{{ route('salons.index') }}">
                        Интим-салоны
                    </a>
                    <a href="{{ route('stripclubs.index') }}">
                        Стрип-клубы
                    </a>
                    <a href="{{ route('masseuse') }}">
                        Массажистки
                    </a>
                    <a href="{{ route('intimmap.index') }}">
                        Интим-карта
                    </a>
                </div>
            </div>
            <div class="line-flexContainerFooter"></div>
            <div class="bottom-flexContainerFooter">
                <p>
                    © ProstitutkiMoscow, {{ date('Y') }}
                </p>
                <div class="right-btoom-flexContainerFooter">
                    {{-- <a href="#contactFormModal" data-open-contact>
                        Контакты
                    </a> --}}
                    <a href="{{ route('home') }}">
                        Карта сайта
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

