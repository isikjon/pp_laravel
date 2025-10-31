@extends('layouts.app')

@section('title', 'Проститутки салоны Москвы')

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="filtersBtn">
                <img src="{{ asset('img/filter.svg') }}" alt="">
                Фильтры
            </div>
            <div class="textSection">
                <h1>
                    Проститутки салоны Москвы
                </h1>
                <p>
                    Наш сайт создан для помощи всем желающим провести свой досуг идеально, подобрав понравившуюся девушку с учетом личных предпочтений, подходящих параметров фигуры, возраста и ценовой категории. Но несмотря на обилие предложений индивидуалок, многие предпочитают обращаться за услугами жриц любви в салонах. Причина проста – больше возможностей выбора на месте, организация комнаты, салон обычно ценит свою репутацию, периодически проверяет девушек на заболевания. Поэтому для выбирающих услуги салонов Москвы становятся рациональным выбором без переплат – обычно удается подобрать интересные варианты дешевле индивидуалок.
                </p>
            </div>
            <div class="girlsSection">
                @foreach($salons as $salon)
                    @include('components.club-card', $salon)
                @endforeach
            </div>
            <a href="#!" class="more-info">
                Показать ещё
            </a>
            <div class="paginationGirls">
                <a href="#!" class="arrowPagination">
                    <img src="{{ asset('img/arrowLeft.svg') }}" alt="">
                </a>
                <div class="pagination__paginationGirls">
                    <div class="pagination__paginationGirls">
                        <a href="#!" class="block-paginationGirls block-paginationGirls__active">
                            1
                        </a>
                        <a href="#!" class="block-paginationGirls">
                            2
                        </a>
                        <a href="#!" class="block-paginationGirls">
                            3
                        </a>
                        <a href="#!" class="block-paginationGirls">
                            ...
                        </a>
                        <a href="#!" class="block-paginationGirls">
                            99
                        </a>
                    </div>
                </div>
                <a href="#!" class="arrowPagination">
                    <img src="{{ asset('img/arrowNext.svg') }}" alt="">
                </a>
            </div>
            <a href="#!" target="_blank" class="bannerBottomTG">
                <img src="{{ asset('img/bannerTG.png') }}" alt="">
            </a>
            
            <div class="textBottomPage">
                <h4>
                    Интим-салоны Москвы — качество и комфорт
                </h4>
                <p>
                    Выбирая салон, вы получаете гарантию безопасности, комфорта и широкого выбора девушек. Здесь всё организовано для вашего удобства.
                </p>
                <h4>
                    Преимущества салонов:
                </h4>
                <p>
                    Большой выбор девушек на месте;
                    <br>
                    Регулярные медицинские проверки;
                    <br>
                    Комфортные номера с душем;
                    <br>
                    Анонимность и безопасность;
                    <br>
                    Фиксированные цены без сюрпризов.
                </p>
                <h4>
                    Как посетить салон:
                </h4>
                <p>
                    Позвоните и узнайте адрес — обычно он не публикуется открыто;
                    <br>
                    Приходите в назначенное время;
                    <br>
                    Выберите девушку из присутствующих;
                    <br>
                    Оплатите услуги на входе;
                    <br>
                    Наслаждайтесь временем в комфортной обстановке.
                    <br><br>
                    В салоне вы можете выбрать девушку лично, увидеть её вживую и принять решение на месте — это главное преимущество перед вызовом индивидуалки.
                </p>
            </div>
        </div>
    </section>
@endsection

