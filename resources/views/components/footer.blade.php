<footer>
    <div class="container">
        <div class="flexContainerFooter">
            <div class="top-flexContainerFooter">
                <img src="{{ asset('img/footerLogo.svg') }}" alt="">
                <div class="right-top-flexContainerFooter">
                    <a href="{{ route('home') }}">
                        Индивидуалки
                    </a>
                    <a href="{{ route('home') }}">
                        Интим-салоны
                    </a>
                    <a href="{{ route('home') }}">
                        Стрип-клубы
                    </a>
                    <a href="{{ route('home') }}?service[]=Массаж_Эротический">
                        Массажистки
                    </a>
                    <a href="{{ route('home') }}">
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

