<footer>
    <div class="container">
        <div class="flexContainerFooter">
            <div class="top-flexContainerFooter">
                <img src="{{ asset('img/footerLogo.svg') }}" alt="">
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
                    <a href="javascript:void(0)" onclick="openContactModal()">
                        Контакты
                    </a>
                    <a href="{{ route('home') }}">
                        Карта сайта
                    </a>
                </div>
            </div>
        </div>
    </div>
</footer>

