@extends('layouts.app')

@section('title', $girl['name'])

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="flexWrapperGirlCard">
                <img src="{{ asset($girl['photo']) }}" alt="" class="photo-flexWrapperGirlCard">
                <div class="right-flexWrapperGirlCard">
                    <h2 class="title-right-flexWrapperGirlCard">
                        {{ $girl['name'] }}
                    </h2>
                    <div class="flexTags-right-flexWrapperGirlCard">
                        <div class="left-flexTags-right-flexWrapperGirlCard">
                            <p class="idCard">
                                id анкеты: {{ $girl['id'] }}
                            </p>
                            @if($girl['verified'] ?? false)
                                <div class="flex-bottom-girlCard">
                                    <img src="{{ asset('img/flex-bottom-girlCard-1.svg') }}" alt="">
                                    <p style="color: #00A81E">
                                        Фото проверены
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div class="right-flexTags-right-flexWrapperGirlCard">
                            <div class="right-bottom-girlCard">
                                <div class="flex-bottom-girlCard">
                                    <img src="{{ asset('img/flex-bottom-girlCard-2.svg') }}" alt="">
                                    <p>
                                        Выезд
                                    </p>
                                </div>
                                @if($girl['apartment'] ?? false)
                                    <div class="flex-bottom-girlCard">
                                        <img src="{{ asset('img/flex-bottom-girlCard-3.svg') }}" alt="">
                                        <p>
                                            Апартаменты
                                        </p>
                                    </div>
                                @endif
                            </div>
                            <a href="#!">
                                <img src="{{ asset('img/flexBottomHeader-8.svg') }}" alt="">
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
                                можно звонить: {{ $girl['call_time'] }}
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
                                        {{ $girl['breast_size'] }} размер
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
                                @foreach($girl['departure_places'] as $place)
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
                            <button onclick="openContactModal({id: '{{ $girl['id'] ?? '' }}', name: '{{ $girl['name'] ?? '' }}', phone: '{{ $girl['phone'] ?? '' }}', url: '{{ url()->current() }}'})" class="contactButton">
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
                                        {{ $girl['hair_color'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                         Интим стрижка:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $girl['intimate_haircut'] }}
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
                                        <p>1 час</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>{{ $girl['prices']['departure']['1_hour'] }}</span>
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
                                    <span>{{ $girl['prices']['departure']['2_hours'] }}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div style="background: url({{ asset('img/bgNight.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Ночь</p>
                                    </div>
                                    <span>{{ $girl['prices']['departure']['night'] }}</span>
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
                                        <p>1 час</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>{{ $girl['prices']['apartments']['1_hour'] }}</span>
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
                                    <span>{{ $girl['prices']['apartments']['2_hours'] }}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div style="background: url({{ asset('img/bgNight.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Ночь</p>
                                    </div>
                                    <span>{{ $girl['prices']['apartments']['night'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                        <div style="background: url({{ asset('img/bgAnal.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2 blockPrecises-right-wrapper-girlCard__topBlock-2__cardGirl">
                            <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                <p>Анал</p>
                            </div>
                            <span>{{ $girl['prices']['anal'] }}</span>
                        </div>
                    </div>
                    <p class="text-right-flexWrapperGirlCard">
                        {{ $girl['description'] }}
                    </p>
                </div>
            </div>
            <div class="photoGirlCardWrap">
                @foreach($girl['gallery'] as $image)
                    <img src="{{ asset($image) }}" alt="">
                @endforeach
            </div>
            @if(!empty($girl['video']))
            <video class="videoGirlCardWrap" src="{{ asset($girl['video']) }}" width="480" height="270" poster="{{ asset('img/poster.png') }}" controls></video>
            @endif
            <div class="servicesBlockGirlCard">
                @foreach($girl['services'] as $category => $services)
                    @if(count($services) > 0)
                        <div class="servicesBlockGirlCard__service">
                            <h4>
                                {{ ['sex' => 'Секс', 'additional' => 'Дополнительно', 'massage' => 'Массаж'][$category] ?? $category }}
                            </h4>
                            <div class="grid-servicesBlockGirlCard__service">
                                @foreach($services as $service)
                                    <div class="flex-grid-servicesBlockGirlCard__service">
                                        @if($service['extra'])
                                            <img src="{{ asset('img/flexInfoCardSalonIcon-2.svg') }}" alt="">
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <circle cx="9.5" cy="9.5" r="9.5" fill="#292D32"/>
                                            </svg>
                                        @endif
                                        <p>
                                            {{ $service['name'] }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="revBlockGirlCard">
                <div class="title-revBlockGirlCard">
                    <h3>
                        Отзывы посетителей:
                    </h3>
                    <a href="#!" class="revModal">Добавить оценку</a>
                </div>
                @foreach($girl['reviews'] as $review)
                    <div class="revBlockGirlCard__rev">
                        <div class="title-revBlockGirlCard">
                            <div class="left-revBlockGirlCard">
                                <p class="date-revBlockGirlCard">
                                    {{ $review['date'] }}
                                </p>
                                <p class="name-revBlockGirlCard">
                                    {{ $review['name'] }}
                                </p>
                            </div>
                        </div>
                        <p class="text-revBlockGirlCard">
                            {{ $review['text'] }}
                        </p>
                    </div>
                @endforeach
            </div>
            
            <a href="#!" target="_blank" class="bannerBottomTG">
                <img src="{{ asset('img/bannerTG.png') }}" alt="">
            </a>
        </div>
    </section>
@endsection

