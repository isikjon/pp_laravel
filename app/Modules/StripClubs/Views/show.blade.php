@extends('layouts.app')

@section('title', $club['name'])

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="flexWrapperGirlCard">
                <img src="{{ asset($club['photo']) }}" alt="" class="photo-flexWrapperGirlCard">
                <div class="right-flexWrapperGirlCard">
                    <h2 class="title-right-flexWrapperGirlCard">
                        {{ $club['name'] }}
                    </h2>
                    <div class="flexTags-right-flexWrapperGirlCard">
                        <div class="left-flexTags-right-flexWrapperGirlCard">
                            <p class="idCard">
                                Режим работы: {{ $club['schedule'] }}
                            </p>
                        </div>
                        <div class="right-flexTags-right-flexWrapperGirlCard">
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
                                <a href="tel:{{ $club['phone'] }}" class="tel-leftInfo-flexInfo-right-flexWrapperGirlCard">
                                    {{ $club['phone'] }}
                                </a>
                                <a href="#!">
                                    <img src="{{ asset('img/tg.svg') }}" alt="">
                                </a>
                            </div>
                            <p class="text-leftInfo-flexInfo-right-flexWrapperGirlCard">
                                можно звонить: {{ $club['call_time'] }}
                            </p>
                        </div>
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
                                        {{ $club['city'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Метро:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $club['metro'] }}
                                    </p>
                                </div>
                                <div class="lineBLocks-centerInfo-flexInfo-right-flexWrapperGirlCard">
                                    <span>
                                        Район:
                                    </span>
                                    <div class="line-centerInfo-flexInfo-right-flexWrapperGirlCard"></div>
                                    <p>
                                        {{ $club['district'] }}
                                    </p>
                                </div>
                            </div>
                            <a href="#!" class="lookOnMap">Показать на карте</a>
                            <button onclick="openContactModal({id: '{{ $club['id'] ?? '' }}', name: '{{ $club['name'] ?? '' }}', phone: '{{ $club['phone'] ?? '' }}', url: '{{ url()->current() }}'})" class="contactButton">
                                Обратная связь
                            </button>
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
                                    <span>{{ $club['prices']['departure']['1_hour'] }}</span>
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
                                    <span>{{ $club['prices']['departure']['2_hours'] }}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div style="background: url({{ asset('img/bgNight.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Ночь</p>
                                    </div>
                                    <span>{{ $club['prices']['departure']['night'] }}</span>
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
                                    <span>{{ $club['prices']['apartments']['1_hour'] }}</span>
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
                                    <span>{{ $club['prices']['apartments']['2_hours'] }}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div style="background: url({{ asset('img/bgNight.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Ночь</p>
                                    </div>
                                    <span>{{ $club['prices']['apartments']['night'] }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                        <div style="background: url({{ asset('img/bgAnal.png') }}) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2 blockPrecises-right-wrapper-girlCard__topBlock-2__cardGirl">
                            <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                <p>Анал</p>
                            </div>
                            <span>{{ $club['prices']['anal'] }}</span>
                        </div>
                    </div>
                    <p class="text-right-flexWrapperGirlCard">
                        {{ $club['description'] }}
                    </p>
                </div>
            </div>
            <div class="photoGirlCardWrap">
                @foreach($club['gallery'] as $image)
                    <img src="{{ asset($image) }}" alt="">
                @endforeach
            </div>
            @if(!empty($club['video']))
            <video class="videoGirlCardWrap" src="{{ asset($club['video']) }}" width="480" height="270" poster="{{ asset('img/poster.png') }}" controls></video>
            @endif
            <div class="revBlockGirlCard">
                <div class="title-revBlockGirlCard">
                    <h3>
                        Отзывы посетителей:
                    </h3>
                    <a href="#!" class="revModal">Добавить оценку</a>
                </div>
                @foreach($club['reviews'] as $review)
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
                            @if(!empty($review['ratings']))
                                <div class="right-revBlockGirlCard">
                                    @if($review['photo_match'] ?? false)
                                        <div class="flex-bottom-girlCard">
                                            <img src="{{ asset('img/block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard.svg') }}" alt="">
                                            <p style="color: #00A81E">
                                                Фото соответствует
                                            </p>
                                        </div>
                                    @endif
                                    @if($review['individual'] ?? false)
                                        <div class="flex-bottom-girlCard">
                                            <img src="{{ asset('img/block-ticks-rightInfo-flexInfo-right-flexWrapperGirlCard.svg') }}" alt="">
                                            <p style="color: #00A81E">
                                                Индивидуалка
                                            </p>
                                        </div>
                                    @endif
                                    @foreach($review['ratings'] as $key => $rating)
                                        <div class="flex-bottom-girlCard">
                                            <p>
                                                {{ ['anal' => 'Анал', 'service' => 'Сервис', 'bj' => 'Минет', 'classic' => 'Кл.секс', 'finish' => 'Окончание'][$key] }}: {{ $rating }}/5
                                            </p>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
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

