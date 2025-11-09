<div class="girlCard" data-girl-id="{{ $id }}">
    <div class="wrapper-girlCard">
        <a href="{{ $detailRoute ?? route('girl.show', ['id' => $id]) }}" class="photoGirl" style="display: block; position: relative;" aria-label="Открыть анкету {{ $name }}">
            @if($hasStatus ?? false)
            <div class="status-photoGirl">
                <img src="{{ cached_asset('img/status-photoGirl.png') }}" alt="Фото проверено" loading="lazy" decoding="async" width="56" height="56">
            </div>
            @endif
            @if($hasVideo ?? false)
            <div class="video-photoGirl">
                <img src="{{ cached_asset('img/video-photoGirl.png') }}" alt="Есть видео" loading="lazy" decoding="async" width="56" height="56">
            </div>
            @endif
            @php
                $placeholderPixel = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';
            @endphp
            <img src="{{ $placeholderPixel }}"
                 data-src="{{ $photo }}"
                 alt="Фото {{ $name }}"
                 class="photoGirl__img deferred-image"
                 loading="{{ !empty($fetch_high) ? 'eager' : 'lazy' }}"
                 fetchpriority="{{ !empty($fetch_high) ? 'high' : 'auto' }}"
                 decoding="async"
                 width="210"
                 height="315"
                 @if(!empty($fetch_high)) data-immediate="true" @endif>
        </a>
        <div class="right-wrapper-girlCard">
            <div class="name-girlCard">
                <a href="{{ $detailRoute ?? route('girl.show', ['id' => $id]) }}" style="color: inherit; text-decoration: none;" aria-label="Перейти в анкету {{ $name }}">
                    <p>{{ $name }}</p>
                </a>
                <a href="#!" data-girl-id="{{ $id }}" class="favorite-toggle" aria-label="{{ $favorite ? 'Удалить из избранного ' : 'Добавить в избранное ' }}{{ $name }}">
                <img src="{{ cached_asset($favorite ? 'img/flexBottomHeader-8-2.svg' : 'img/flexBottomHeader-8.svg') }}" alt="{{ $favorite ? 'В избранном' : 'Добавить в избранное' }}" loading="lazy" decoding="async" width="24" height="24">
                </a>
            </div>
            <p class="ageGirlCard">{{ $age }} года</p>
            <div class="line-right-wrapper-girlCard"></div>
            <div class="infoParameters-right-wrapper-girlCard">
                <div class="block-infoParameters-right-wrapper-girlCard">
                    <p>Рост:</p>
                    <span>{{ $height }} см</span>
                </div>
                <div class="block-infoParameters-right-wrapper-girlCard">
                    <p>Вес:</p>
                    <span>{{ $weight }} кг</span>
                </div>
                <div class="block-infoParameters-right-wrapper-girlCard">
                    <p>Грудь:</p>
                    <span>{{ $bust }} размер</span>
                </div>
            </div>
            <div class="line-right-wrapper-girlCard"></div>
            <a href="tel:{{ $phone }}" class="tel-right-wrapper-girlCard">{{ $phone }}</a>
            <div class="infoTownWhatsapp">
                <p>{{ $city }},</p>
                <a href="#!" aria-label="Написать в WhatsApp {{ $name }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none" role="img" aria-label="WhatsApp">
                        <rect width="25" height="25" rx="12.5" fill="#48C95F"/>
                        <path d="M17.4543 7.53906C16.2195 6.30859 14.5732 5.625 12.8354 5.625C9.22256 5.625 6.29573 8.54167 6.29573 12.1419C6.29573 13.2812 6.61585 14.4206 7.16463 15.3776L6.25 18.75L9.72561 17.8385C10.686 18.3398 11.7378 18.6133 12.8354 18.6133C16.4482 18.6133 19.375 15.6966 19.375 12.0964C19.3293 10.4102 18.689 8.76953 17.4543 7.53906ZM15.9909 14.4661C15.8537 14.8307 15.2134 15.1953 14.8933 15.2409C14.6189 15.2865 14.253 15.2865 13.8872 15.1953C13.6585 15.1042 13.3384 15.013 12.9726 14.8307C11.3262 14.1471 10.2744 12.5065 10.1829 12.3698C10.0915 12.2786 9.49695 11.5039 9.49695 10.6836C9.49695 9.86328 9.90854 9.4987 10.0457 9.31641C10.1829 9.13411 10.3659 9.13411 10.503 9.13411C10.5945 9.13411 10.7317 9.13411 10.8232 9.13411C10.9146 9.13411 11.0518 9.08854 11.189 9.40755C11.3262 9.72656 11.6463 10.5469 11.6921 10.5924C11.7378 10.6836 11.7378 10.7747 11.6921 10.8659C11.6463 10.957 11.6006 11.0482 11.5091 11.1393C11.4177 11.2305 11.3262 11.3672 11.2805 11.4128C11.189 11.5039 11.0976 11.5951 11.189 11.7318C11.2805 11.9141 11.6006 12.4154 12.1037 12.8711C12.7439 13.418 13.247 13.6003 13.4299 13.6914C13.6128 13.7826 13.7043 13.737 13.7957 13.6458C13.8872 13.5547 14.2073 13.1901 14.2988 13.0078C14.3902 12.8255 14.5274 12.8711 14.6646 12.9167C14.8018 12.9622 15.625 13.3724 15.7622 13.4635C15.9451 13.5547 16.0366 13.6003 16.0823 13.6458C16.128 13.7826 16.128 14.1016 15.9909 14.4661Z" fill="white"/>
                    </svg>
                </a>
            </div>
            <p class="metro-right-wrapper-girlCard">{{ $metro }}</p>
            <div class="blockPrecises-right-wrapper-girlCard">
                <div class="blockPrecises-right-wrapper-girlCard__top blockPrecises-right-wrapper-girlCard__top-1">
                    <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                        <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                            <p>1 час</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span>{{ number_format($price1h, 0, '', ' ') }}</span>
                    </div>
                    <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                    <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                        <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                            <p>2 часа</p>
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <span>{{ number_format($price2h, 0, '', ' ') }}</span>
                    </div>
                </div>
                <div class="blockPrecises-right-wrapper-girlCard__top">
                    <div style="background: url({{ cached_asset('img/bgAnal.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                        <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                            <p>Анал</p>
                        </div>
                        <span>{{ $priceAnal ? number_format($priceAnal, 0, '', ' ') : '-' }}</span>
                    </div>
                    <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                    <div style="background: url({{ cached_asset('img/bgNight.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                        <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                            <p>Ночь</p>
                        </div>
                        <span>{{ number_format($priceNight, 0, '', ' ') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="bottom-girlCard">
        <div class="flex-bottom-girlCard">
            <img src="{{ cached_asset('img/flex-bottom-girlCard-1.svg') }}" alt="" loading="lazy" decoding="async" width="18" height="18" aria-hidden="true">
            <p class="verified-status-text">{{ $verified ?? 'Фото проверены' }}</p>
        </div>
        <div class="right-bottom-girlCard">
            @if($outcall ?? false)
            <div class="flex-bottom-girlCard">
                <img src="{{ cached_asset('img/flex-bottom-girlCard-2.svg') }}" alt="" loading="lazy" decoding="async" width="18" height="18" aria-hidden="true">
                <p>Выезд</p>
            </div>
            @endif
            @if($apartment ?? false)
            <div class="flex-bottom-girlCard">
                <img src="{{ cached_asset('img/flex-bottom-girlCard-3.svg') }}" alt="" loading="lazy" decoding="async" width="18" height="18" aria-hidden="true">
                <p>Апартаменты</p>
            </div>
            @endif
        </div>
    </div>
</div>

