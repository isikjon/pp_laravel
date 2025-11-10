@extends('layouts.app')

@section('title', 'Массажистка ' . $girl['name'] . ' из ' . $girl['city'])

@section('meta_description', $girl['description'] ?? 'Массажистка ' . $girl['name'] . ' из ' . $girl['city'] . '. Возраст: ' . ($girl['age'] ?? 'не указан') . '. Телефон: ' . ($girl['phone'] ?? 'не указан') . '.')

@push('styles')
<style>
.photoGirlCardWrap {
    margin: 30px 0 !important;
    display: flex !important;
    flex-wrap: wrap;
    gap: 10px;
    width: 100%;
}

.gridzy-container {
    width: 100%;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
}

.gridzy-container a {
    display: block !important;
    position: relative;
    cursor: pointer;
    overflow: hidden;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    width: auto !important;
    max-width: 245px !important;
    flex: 0 0 auto;
}

.gridzy-container a:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    transform: translateY(-2px);
}

.gridzy-container a img {
    width: auto !important;
    max-width: 245px !important;
    height: auto !important;
    display: block;
    transition: transform 0.3s ease;
    border-radius: 8px;
}

.gridzy-container a:hover img {
    transform: scale(1.02);
}

.lg-backdrop {
    background-color: rgba(0, 0, 0, 0.95);
}

.lg-toolbar {
    background-color: rgba(0, 0, 0, 0.7);
}

.lg-outer .lg-thumb-outer {
    background-color: rgba(0, 0, 0, 0.8);
}

.lg-actions .lg-next, .lg-actions .lg-prev {
    background-color: rgba(126, 29, 50, 0.8);
    color: #fff;
}

.lg-actions .lg-next:hover, .lg-actions .lg-prev:hover {
    background-color: rgba(126, 29, 50, 1);
}

.lg-sub-html {
    background-color: rgba(0, 0, 0, 0.8);
    font-family: 'Noto Sans', sans-serif;
}

@media screen and (max-width: 1350px) {
    .gridzy-container a,
    .gridzy-container a img {
        max-width: calc(25% - 10px) !important;
    }
}

@media screen and (max-width: 768px) {
    .gridzy-container a,
    .gridzy-container a img {
        max-width: calc(33.333% - 10px) !important;
    }
}

@media screen and (max-width: 500px) {
    .gridzy-container {
        gap: 5px;
    }
    
    .gridzy-container a,
    .gridzy-container a img {
        max-width: calc(50% - 5px) !important;
    }
}
</style>
@endpush

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="flexWrapperGirlCard">
                <img src="{{ $girl['mainPhoto'] }}" alt="" class="photo-flexWrapperGirlCard" style="cursor: pointer;" onclick="if(document.querySelector('#girlGallery a')){document.querySelector('#girlGallery a').click();}">
                <div class="right-flexWrapperGirlCard">
                    <h2 class="title-right-flexWrapperGirlCard">
                        Массажистка {{ $girl['name'] }} из {{ $girl['city'] }}
                    </h2>
                    <div class="flexTags-right-flexWrapperGirlCard">
                        <div class="left-flexTags-right-flexWrapperGirlCard">
                            <p class="idCard">
                                id анкеты: {{ $girl['id'] }}
                            </p>
                            @if($girl['verified'])
                            <div class="flex-bottom-girlCard">
                                <img src="{{ asset('img/flex-bottom-girlCard-1.svg') }}" alt="" loading="lazy" decoding="async">
                                <p class="verified-status-text">{{ $girl['verified'] ?? 'Фото проверены' }}</p>
                            </div>
                            @endif
                        </div>
                        <div class="right-flexTags-right-flexWrapperGirlCard">
                            <div class="right-bottom-girlCard">
                                @if($girl['outcall'])
                                <div class="flex-bottom-girlCard">
                                    <img src="{{ asset('img/flex-bottom-girlCard-2.svg') }}" alt="">
                                    <p>
                                        Выезд
                                    </p>
                                </div>
                                @endif
                                @if($girl['apartment'])
                                <div class="flex-bottom-girlCard">
                                    <img src="{{ asset('img/flex-bottom-girlCard-3.svg') }}" alt="">
                                    <p>
                                        Апартаменты
                                    </p>
                                </div>
                                @endif
                            </div>
                            <a href="#!" class="favorite-btn-detail" data-girl-id="{{ $girl['id'] }}" data-girl-name="{{ $girl['name'] }}">
                                <img src="{{ asset('img/flexBottomHeader-8.svg') }}" alt="" class="favorite-icon">
                            </a>
                            <a href="#!">
                                <img src="{{ asset('img/flexBottomHeader-8-3.svg') }}" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="flexInfo-right-flexWrapperGirlCard">
                        <div class="leftInfo-flexInfo-right-flexWrapperGirlCard">
                            <div class="telBlock-leftInfo-flexInfo-right-flexWrapperGirlCard">
                                <a href="tel:{{ $girl['phone'] }}" class="tel-leftInfo-flexInfo-right-flexWrapperGirlCard">
                                    {{ $girl['phone'] }}
                                </a>
                                <a href="#!">
                                    <img src="{{ asset('img/tg.svg') }}" alt="">
                                </a>
                            </div>
                            <p class="text-leftInfo-flexInfo-right-flexWrapperGirlCard">
                                можно звонить: {{ $girl['schedule'] }}
                            </p>
                            <span class="info-leftInfo-flexInfo-right-flexWrapperGirlCard">
                                Пожалуйста, сообщите девушке,что вы нашли ее на нашем сайтеProstitutkiMoscow
                            </span>
                        </div>
                        <div class="centerInfo-flexInfo-right-flexWrapperGirlCard">
                            <h3>
                                Параметры:
                            </h3>
                            <div class="flexLineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Возраст:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['age'] }} год
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Рост:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['height'] }} см
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Грудь:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['bust'] }} размер
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Вес:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['weight'] }} кг
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="rightInfo-flexInfo-right-flexWrapperGirlCard">
                            <h3>
                                Выезд
                            </h3>
                            <div class="ticks-rightInfo-flexInfo-right-flexWrapperGirlCard">
                                @foreach($girl['outcallPlaces'] as $place)
                                <div class="block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard">
                                    <img src="{{ asset('img/block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard.svg') }}" alt="">
                                    <p>
                                        {{ $place }}
                                    </p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flexInfo-right-flexWrapperGirlCard">
                        <div class="centerInfo-flexInfo-right-flexWrapperGirlCard">
                            <h3>
                                Локация:
                            </h3>
                            <div class="flexLineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Город:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['city'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Метро:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['metro'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Район:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['district'] }}
                                    </p>
                                </div>
                            </div>
                            <a href="#!" class="lookOnMap">Показать на карте</a>
                            <button onclick="openContactModal({id: '{{ $girl['id'] }}', name: '{{ $girl['name'] }}', phone: '{{ $girl['phone'] }}', url: '{{ url()->current() }}'})" class="contactButton">
                                Обратная связь
                            </button>
                        </div>
                        <div class="centerInfo-flexInfo-right-flexWrapperGirlCard">
                            <h3>
                                Внешность:
                            </h3>
                            <div class="flexLineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Цвет волос:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['hairColor'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                         Интим стрижка:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['intimHaircut'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Национальность:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['nationality'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex-blockPrecises-right-wrapper-girlCard">
                        <div class="blockPrecises-right-wrapper-girlCard">
                            <div class="blockPrecises-right-wrapper-girlCard__top blockPrecises-right-wrapper-girlCard__top-1">
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlockTitle">
                                <span>
                                    Выезд
                                </span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>
                                            1 час
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>
                                    {{ $girl['prices']['outcall']['1h'] ? number_format($girl['prices']['outcall']['1h'], 0, '', ' ') : '—' }}
                                </span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>
                                            2 часа
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>
                                            {{ $girl['prices']['outcall']['2h'] ? number_format($girl['prices']['outcall']['2h'], 0, '', ' ') : '—' }}
                                        </span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div data-theme="night" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>
                                            Ночь
                                        </p>
                                    </div>
                                    <span>
                                            {{ $girl['prices']['outcall']['night'] ? number_format($girl['prices']['outcall']['night'], 0, '', ' ') : '—' }}
                                        </span>
                                </div>
                            </div>
                            <div class="blockPrecises-right-wrapper-girlCard__top blockPrecises-right-wrapper-girlCard__top-1">
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlockTitle">
                                    <span>
                                        Апартаменты
                                    </span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>
                                            1 час
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>
                                    {{ $girl['prices']['apartment']['1h'] ? number_format($girl['prices']['apartment']['1h'], 0, '', ' ') : '—' }}
                                </span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>
                                            2 часа
                                        </p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>
                                            {{ $girl['prices']['apartment']['2h'] ? number_format($girl['prices']['apartment']['2h'], 0, '', ' ') : '—' }}
                                        </span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div data-theme="night" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>
                                            Ночь
                                        </p>
                                    </div>
                                    <span>
                                            {{ $girl['prices']['apartment']['night'] ? number_format($girl['prices']['apartment']['night'], 0, '', ' ') : '—' }}
                                        </span>
                                </div>
                            </div>
                        </div>
                        <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                        @if($girl['prices']['anal'])
                        <div style="background: url({{ asset('img/bgAnal.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2 blockPrecises-right-wrapper-girlCard__topBlock-2__cardGirl">
                            <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                <p>
                                    Анал
                                </p>
                            </div>
                            <span>
                                @if($girl['prices']['anal'] === 'by_phone')
                                    Цену уточняйте
                                @else
                                    + {{ number_format($girl['prices']['anal'], 0, '', ' ') }}
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    <p class="text-right-flexWrapperGirlCard">
                        {{ $girl['description'] }}
                    </p>
                </div>
            </div>
            <div class="photoGirlCardWrap">
                <div id="girlGallery" class="gridzy-container">
                    @foreach($girl['photos'] as $index => $photo)
                        <a href="{{ $photo }}" 
                           data-src="{{ $photo }}"
                           data-sub-html="<h4>{{ $girl['name'] }} - Фото {{ $index + 1 }}</h4>">
                            <img src="{{ $photo }}" alt="{{ $girl['name'] }} - Фото {{ $index + 1 }}">
                        </a>
                    @endforeach
                </div>
            </div>
            @if($girl['video'])
            <video class="videoGirlCardWrap" src="{{ $girl['video'] }}" width="480" height="270" poster="{{ $girl['videoPoster'] }}" controls></video>
            @endif
            <div class="infoPriseGirlCard">
                @foreach($girl['services'] as $category => $services)
                    <div class="rightInfo-flexInfo-right-flexWrapperGirlCard">
                        <h3>
                            {{ $category }}
                        </h3>
                        <div class="ticks-rightInfo-flexInfo-right-flexWrapperGirlCard">
                            @foreach($services as $service)
                                <div class="block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard">
                                    <img src="{{ asset($service['available'] ? 'img/block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard.svg' : 'img/block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard-2.svg') }}" alt="">
                                    <div class="right-block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard">
                                        <p>
                                            {{ $service['name'] }}
                                        </p>
                                        @if($service['extra'])
                                            <span>
                                                доп
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="revBlockGirlCard">
                <div class="title-revBlockGirlCard">
                    <h3>
                        Отзывы посетителей:
                    </h3>
                    <a href="#!" class="modal-rev">
                        Оставить отзыв
                    </a>
                </div>
                @if(!empty($girl['reviews']) && count($girl['reviews']) > 0)
                    @foreach($girl['reviews'] as $review)
                        <div class="revBlockGirlCard__rev">
                            <div class="left-revBlockGirlCard">
                                <p class="name-revBlockGirlCard">
                                    {{ $review['author'] }}
                                </p>
                                <span class="date-revBlockGirlCard">
                                    {{ $review['date'] }}
                                </span>
                            </div>
                            <p class="text-revBlockGirlCard">
                                {{ $review['text'] }}
                            </p>
                        </div>
                    @endforeach
                @else
                    <div class="revBlockGirlCard__rev">
                        <p class="text-revBlockGirlCard" style="text-align: center; color: #999; padding: 20px;">
                            Пока еще никто не оставлял отзыв
                        </p>
                    </div>
                @endif
            </div>
            
            <h3 class="titleBlock-girlsSection">
                Похожие массажистки
            </h3>
            
            @if(!empty($similarGirls) && count($similarGirls) > 0)
            <div class="girlsSection">
                @foreach($similarGirls as $girl)
                    @include('components.girl-card', $girl)
                @endforeach
            </div>
            @endif
        </div>
    </section>
@endsection

@section('page_scripts')
<script>
$(document).ready(function() {
    function getFavorites() {
        const favorites = localStorage.getItem('favorites');
        return favorites ? JSON.parse(favorites) : [];
    }
    
    function saveFavorites(favorites) {
        localStorage.setItem('favorites', JSON.stringify(favorites));
        updateCounter();
    }
    
    function updateCounter() {
        const count = getFavorites().length;
        $('.favorites-counter').text(count);
    }
    
    function addToFavorites(girlId) {
        const favorites = getFavorites();
        if (!favorites.includes(girlId)) {
            favorites.push(girlId);
            saveFavorites(favorites);
            return true;
        }
        return false;
    }
    
    function removeFromFavorites(girlId) {
        let favorites = getFavorites();
        const index = favorites.indexOf(girlId);
        if (index > -1) {
            favorites.splice(index, 1);
            saveFavorites(favorites);
            return true;
        }
        return false;
    }
    
    function isInFavorites(girlId) {
        return getFavorites().includes(girlId);
    }
    
    function showPopup(message, type = 'success') {
        const existingPopup = $('.favorite-popup');
        if (existingPopup.length) {
            existingPopup.remove();
        }
        
        const icon = type === 'success' 
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#FF0042"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#ccc"/></svg>';
        
        const popup = $(`
            <div class="favorite-popup favorite-popup--${type}">
                <div class="favorite-popup__content">
                    <div class="favorite-popup__icon">${icon}</div>
                    <div class="favorite-popup__message">${message}</div>
                </div>
            </div>
        `);
        
        $('body').append(popup);
        
        setTimeout(() => {
            popup.addClass('favorite-popup--show');
        }, 10);
        
        setTimeout(() => {
            popup.removeClass('favorite-popup--show');
            setTimeout(() => {
                popup.remove();
            }, 300);
        }, 2500);
    }
    
    function updateFavoriteIcon(isFavorite) {
        const icon = $('.favorite-btn-detail .favorite-icon');
        if (isFavorite) {
            icon.attr('src', '{{ asset("img/flexBottomHeader-8-2.svg") }}');
        } else {
            icon.attr('src', '{{ asset("img/flexBottomHeader-8.svg") }}');
        }
    }
    
    const girlId = $('.favorite-btn-detail').data('girl-id');
    if (girlId && isInFavorites(girlId)) {
        updateFavoriteIcon(true);
    }
    
    $('.favorite-btn-detail').on('click', function(e) {
        e.preventDefault();
        
        const girlId = $(this).data('girl-id');
        const girlName = $(this).data('girl-name');
        
        if (!girlId) {
            console.error('Не удалось найти ID девушки!');
            return;
        }
        
        if (isInFavorites(girlId)) {
            removeFromFavorites(girlId);
            updateFavoriteIcon(false);
            showPopup(`${girlName} удалена из избранного`, 'remove');
        } else {
            addToFavorites(girlId);
            updateFavoriteIcon(true);
            showPopup(`${girlName} добавлена в избранное`, 'success');
        }
    });
    
    
    if (typeof lightGallery !== 'undefined') {
        lightGallery(document.getElementById('girlGallery'), {
            plugins: [lgZoom, lgThumbnail, lgFullscreen],
            speed: 500,
            licenseKey: 'your_license_key',
            download: false,
            mobileSettings: {
                controls: true,
                showCloseIcon: true,
                download: false
            },
            thumbWidth: 100,
            thumbHeight: 100,
            thumbMargin: 5,
            enableSwipe: true,
            enableDrag: true,
            closeOnTap: true,
            preload: 2,
            loop: true,
            counter: true,
            hideBarsDelay: 3000
        });
    }
});
</script>
@endsection
