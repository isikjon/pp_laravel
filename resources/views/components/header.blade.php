<header>
    @php
        $currentRoute = Route::currentRouteName();
    @endphp
    <div class="container">
        <div class="flexTopHeader">
            <a href="{{ route('home', ['city' => $selectedCity ?? 'moscow']) }}">
                <img src="{{ cached_asset('img/logo.svg') }}" alt="ProstitutkiMoscow" class="logo" width="233" height="41" decoding="async" fetchpriority="high">
            </a>
            <div class="center-flexTopHeader">
                <div class="cityChoose">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                        <path d="M4.36489 8.96968C6.33489 0.309678 19.1649 0.319678 21.1249 8.97968C22.2749 14.0597 19.1149 18.3597 16.3449 21.0197C14.3349 22.9597 11.1549 22.9597 9.13489 21.0197C6.37489 18.3597 3.21489 14.0497 4.36489 8.96968Z" fill="#7E1D32"/>
                        <path d="M12.7449 13.9097C14.468 13.9097 15.8649 12.5128 15.8649 10.7897C15.8649 9.06655 14.468 7.66968 12.7449 7.66968C11.0217 7.66968 9.62488 9.06655 9.62488 10.7897C9.62488 12.5128 11.0217 13.9097 12.7449 13.9097Z" fill="white"/>
                    </svg>
                    <p>
                        Ваш город: <span class="modal-cityChoose">{{ $cityName ?? 'Москва' }}</span>
                    </p>
                </div>
                <div class="headerMetro" data-metro-trigger style="cursor: pointer;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                        <path d="M8.98256 5.64514C8.91638 5.81886 7.03845 10.5509 5.1688 15.2581L4.0437 18.0957H3.39015H2.74487V18.5921V19.0884H6.30217H9.85948V18.5921V18.0957H9.14802C8.61028 18.0957 8.44483 18.0709 8.47792 17.9881C8.49446 17.9385 8.80883 17.0616 9.16456 16.044C9.52029 15.0348 9.83466 14.2075 9.85948 14.2075C9.88429 14.2075 10.5296 15.3078 11.2989 16.648C12.4902 18.7079 12.7136 19.0636 12.7963 18.9395C12.846 18.8651 13.4664 17.7648 14.1613 16.5073C14.8645 15.2416 15.4602 14.2323 15.4932 14.2737C15.5677 14.3564 16.8086 17.8558 16.8086 17.9881C16.8086 18.0709 16.6349 18.0957 16.1468 18.0957H15.485V18.5921V19.0884H19.0009H22.5168V18.5921V18.0957H21.9047H21.2842L21.127 17.6655C20.9367 17.1609 17.5035 8.32552 16.8583 6.68751C16.6183 6.07532 16.3867 5.56241 16.3371 5.54586C16.2957 5.52932 15.4767 7.04324 14.5171 8.92116C13.5657 10.7908 12.7467 12.313 12.7053 12.3047C12.664 12.2965 11.8698 10.8322 10.9267 9.04525C9.99184 7.2666 9.18938 5.73614 9.13974 5.64514L9.04047 5.47968L8.98256 5.64514Z" fill="#7E1D32"/>
                    </svg>
                    <p>
                        Метро: <span class="modal-headerMetro">Арбат</span>
                    </p>
                </div>
            </div>
            <div class="rightHeaderTop">
                <form class="search-rightHeaderTop" action="" method="get">
                    <input type="text" name="query" placeholder="Поиск...">
                    <button type="submit" aria-label="Поиск">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                            <path d="M9.16666 17.1463C13.3088 17.1463 16.6667 13.7885 16.6667 9.64635C16.6667 5.50421 13.3088 2.14635 9.16666 2.14635C5.02452 2.14635 1.66666 5.50421 1.66666 9.64635C1.66666 13.7885 5.02452 17.1463 9.16666 17.1463Z" stroke="#7E1D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M15.7749 17.7212C16.2166 19.0545 17.2249 19.1879 17.9999 18.0212C18.7083 16.9545 18.2416 16.0795 16.9583 16.0795C16.0083 16.0712 15.4749 16.8129 15.7749 17.7212Z" stroke="#7E1D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </form>
                {{-- <a href="#contactFormModal" class="modalRegistration" data-open-contact>
                    Обратная связь
                </a> --}}
                <div class="hamburger-menu">
                    <input id="menu__toggle" type="checkbox" aria-label="Открыть главное меню" aria-controls="mainMobileMenu">
                    <label class="menu__btn" for="menu__toggle">
                        <img src="{{ cached_asset('img/burger.svg') }}" alt="" class="burgerSvg" loading="lazy" decoding="async" width="36" height="36">
                        <img class="closeBurger" src="{{ cached_asset('img/closeBurger.svg') }}" alt="" loading="lazy" decoding="async" width="36" height="36">
                    </label>
                    <ul class="menu__box" id="mainMobileMenu">
                        <li class="content-burger">
                            <ul class="listBurgerUl">
                                <li>
                                    <a href="{{ route('home', ['city' => $selectedCity ?? 'moscow']) }}">
                                        <img src="{{ cached_asset('img/logo.svg') }}" alt="ProstitutkiMoscow" class="logo" width="233" height="41" loading="lazy" decoding="async">
                                    </a>
                                    <div class="cityChoose">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                                            <path d="M4.36489 8.96968C6.33489 0.309678 19.1649 0.319678 21.1249 8.97968C22.2749 14.0597 19.1149 18.3597 16.3449 21.0197C14.3349 22.9597 11.1549 22.9597 9.13489 21.0197C6.37489 18.3597 3.21489 14.0497 4.36489 8.96968Z" fill="#7E1D32"/>
                                            <path d="M12.7449 13.9097C14.468 13.9097 15.8649 12.5128 15.8649 10.7897C15.8649 9.06655 14.468 7.66968 12.7449 7.66968C11.0217 7.66968 9.62488 9.06655 9.62488 10.7897C9.62488 12.5128 11.0217 13.9097 12.7449 13.9097Z" fill="white"/>
                                        </svg>
                                        <p>
                                            Ваш город: <span class="modal-cityChoose">{{ $cityName ?? 'Москва' }}</span>
                                        </p>
                                    </div>
                                    <div class="headerMetro" data-metro-trigger style="cursor: pointer;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                                            <path d="M8.98256 5.64514C8.91638 5.81886 7.03845 10.5509 5.1688 15.2581L4.0437 18.0957H3.39015H2.74487V18.5921V19.0884H6.30217H9.85948V18.5921V18.0957H9.14802C8.61028 18.0957 8.44483 18.0709 8.47792 17.9881C8.49446 17.9385 8.80883 17.0616 9.16456 16.044C9.52029 15.0348 9.83466 14.2075 9.85948 14.2075C9.88429 14.2075 10.5296 15.3078 11.2989 16.648C12.4902 18.7079 12.7136 19.0636 12.7963 18.9395C12.846 18.8651 13.4664 17.7648 14.1613 16.5073C14.8645 15.2416 15.4602 14.2323 15.4932 14.2737C15.5677 14.3564 16.8086 17.8558 16.8086 17.9881C16.8086 18.0709 16.6349 18.0957 16.1468 18.0957H15.485V18.5921V19.0884H19.0009H22.5168V18.5921V18.0957H21.9047H21.2842L21.127 17.6655C20.9367 17.1609 17.5035 8.32552 16.8583 6.68751C16.6183 6.07532 16.3867 5.56241 16.3371 5.54586C16.2957 5.52932 15.4767 7.04324 14.5171 8.92116C13.5657 10.7908 12.7467 12.313 12.7053 12.3047C12.664 12.2965 11.8698 10.8322 10.9267 9.04525C9.99184 7.2666 9.18938 5.73614 9.13974 5.64514L9.04047 5.47968L8.98256 5.64514Z" fill="#7E1D32"/>
                                        </svg>
                                        <p>
                                            Метро: <span class="modal-headerMetro">Арбат</span>
                                        </p>
                                    </div>
                                    <div class="flexBottomHeader">
                                        <a href="{{ route('home', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'home') aria-current="page" @endif>
                                        <img src="{{ cached_asset('img/flexBottomHeader-1.svg') }}" alt="" width="11" height="24" loading="lazy" decoding="async">
                                            Индивидуалки
                                        </a>
                                        <a href="{{ route('salons.index', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'salons.index') aria-current="page" @endif>
                                            <img src="{{ cached_asset('img/flexBottomHeader-2.svg') }}" alt="" width="13" height="24" loading="lazy" decoding="async">
                                            Интим-салоны
                                        </a>
                                        <a href="{{ route('stripclubs.index', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'stripclubs.index') aria-current="page" @endif>
                                            <img src="{{ cached_asset('img/flexBottomHeader-3.svg') }}" alt="" width="9" height="24" loading="lazy" decoding="async">
                                            Стрип-клубы
                                        </a>
                                        <a href="{{ route('masseuse', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'masseuse') aria-current="page" @endif>
                                            <img src="{{ cached_asset('img/flexBottomHeader-4.svg') }}" alt="" width="31" height="24" loading="lazy" decoding="async">
                                            Массажистки
                                        </a>
                                        <a href="{{ route('intimmap.index', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'intimmap.index') aria-current="page" @endif>
                                            <img src="{{ cached_asset('img/flexBottomHeader-6.svg') }}" alt="" width="25" height="24" loading="lazy" decoding="async">
                                            Интим-карта
                                        </a>
                                        <a href="{{ route('favorites', ['city' => $selectedCity ?? 'moscow']) }}" class="favorites-counter-link" @if($currentRoute === 'favorites') aria-current="page" @endif>
                                            <img src="{{ cached_asset('img/flexBottomHeader-8.svg') }}" alt="" width="24" height="24" loading="lazy" decoding="async">
                                            <span class="favorites-counter">0</span>
                                        </a>
                                    </div>
                                    <form class="search-rightHeaderTop" action="" method="get">
                                        <input type="text" name="query" placeholder="Поиск...">
                                        <button type="submit" aria-label="Поиск">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                                <path d="M9.16666 17.1463C13.3088 17.1463 16.6667 13.7885 16.6667 9.64635C16.6667 5.50421 13.3088 2.14635 9.16666 2.14635C5.02452 2.14635 1.66666 5.50421 1.66666 9.64635C1.66666 13.7885 5.02452 17.1463 9.16666 17.1463Z" stroke="#7E1D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                                <path d="M15.7749 17.7212C16.2166 19.0545 17.2249 19.1879 17.9999 18.0212C18.7083 16.9545 18.2416 16.0795 16.9583 16.0795C16.0083 16.0712 15.4749 16.8129 15.7749 17.7212Z" stroke="#7E1D32" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </button>
                                    </form>
                                    {{-- <a href="#contactFormModal" class="modalRegistration" data-open-contact>
                                        Обратная связь
                                    </a> --}}
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="lineHeader"></div>
        <div class="flexBottomHeader">
            <a href="{{ route('home', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'home') aria-current="page" @endif>
                <img src="{{ cached_asset('img/flexBottomHeader-1.svg') }}" alt="" width="11" height="24" loading="lazy" decoding="async">
                Индивидуалки
            </a>
            <a href="{{ route('salons.index', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'salons.index') aria-current="page" @endif>
                <img src="{{ cached_asset('img/flexBottomHeader-2.svg') }}" alt="" width="13" height="24" loading="lazy" decoding="async">
                Интим-салоны
            </a>
            <a href="{{ route('stripclubs.index', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'stripclubs.index') aria-current="page" @endif>
                <img src="{{ cached_asset('img/flexBottomHeader-3.svg') }}" alt="" width="9" height="24" loading="lazy" decoding="async">
                Стрип-клубы
            </a>
            <a href="{{ route('masseuse', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'masseuse') aria-current="page" @endif>
                <img src="{{ cached_asset('img/flexBottomHeader-4.svg') }}" alt="" width="31" height="24" loading="lazy" decoding="async">
                Массажистки
            </a>
            <a href="{{ route('intimmap.index', ['city' => $selectedCity ?? 'moscow']) }}" @if($currentRoute === 'intimmap.index') aria-current="page" @endif>
                <img src="{{ cached_asset('img/flexBottomHeader-6.svg') }}" alt="" width="25" height="24" loading="lazy" decoding="async">
                Интим-карта
            </a>
            <a href="{{ route('favorites', ['city' => $selectedCity ?? 'moscow']) }}" class="favorites-counter-link" @if($currentRoute === 'favorites') aria-current="page" @endif>
                <img src="{{ cached_asset('img/flexBottomHeader-8.svg') }}" alt="" width="24" height="24" loading="lazy" decoding="async">
                <span class="favorites-counter">0</span>
            </a>
        </div>
    </div>
</header>

