@extends('layouts.app')

@section('title', 'Проститутки индивидуалки массажистки ' . (($cityName ?? 'Москва') === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы'))

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="filtersBtn">
                <img src="{{ asset('img/filter.svg') }}" alt="">
                Фильтры
            </div>
            <div class="textSection">
                <h1>
                    Проститутки индивидуалки массажистки {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }}
                </h1>
                <p>
                    Тяжелый трудовой день за плечами, усталость и стрессы надоели? Во всем обилии возможностей для развлечений и досуга многие обращают заслуженное внимание на услуги путан. Что может быть прекраснее, чем понимание, ласка и страстное наслаждение в комфортной обстановке?
                    <br><br>
                    Только, если совместить страстную и чувственную близость с профессиональным массажем. Проститутки индивидуалки массажистки {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }} объединяют в себе удивительные таланты понимающей собеседницы, чувственной массажистки для релакса и страстной любовницы для ярких эмоций и восторга. Наш проект создан специально для желающих подарить себе идеальный досуг, с нотками страсти, наслаждения и релакса. Такую возможность подарит опытная массажистка, готовая продолжить вечер в более непринужденной атмосфере – мы подготовили широкий выбор таких девушек, с возможностью индивидуального выбора для каждого посетителя.
                </p>
            </div>
            
            <div class="girlsSection">
                @foreach($girls as $girl)
                    @include('components.girl-card', $girl)
                @endforeach
            </div>
            
            <a href="#!" class="more-info">
                Показать ещё
            </a>
            
            @if($girls->hasPages())
            <div class="paginationGirls">
                @if($girls->onFirstPage())
                    <span class="arrowPagination arrowPagination-prev" style="opacity: 0.5; cursor: not-allowed;">
                        <img src="{{ asset('img/arrowLeft.svg') }}" alt="">
                    </span>
                @else
                    <a href="{{ $girls->previousPageUrl() }}" class="arrowPagination arrowPagination-prev">
                        <img src="{{ asset('img/arrowLeft.svg') }}" alt="">
                    </a>
                @endif
                
                <div class="pagination__paginationGirls">
                    @php
                        $currentPage = $girls->currentPage();
                        $lastPage = $girls->lastPage();
                        $start = max(1, $currentPage - 1);
                        $end = min($lastPage, $currentPage + 1);
                        
                        if ($currentPage <= 2) {
                            $end = min(3, $lastPage);
                        }
                        if ($currentPage >= $lastPage - 1) {
                            $start = max(1, $lastPage - 2);
                        }
                    @endphp
                    
                    @if($start > 1)
                        <a href="{{ $girls->url(1) }}" class="block-paginationGirls">1</a>
                        @if($start > 2)
                            <span class="block-paginationGirls" style="cursor: default;">...</span>
                        @endif
                    @endif
                    
                    @for($i = $start; $i <= $end; $i++)
                        <a href="{{ $girls->url($i) }}" class="block-paginationGirls {{ $i == $currentPage ? 'block-paginationGirls__active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                    
                    @if($end < $lastPage)
                        @if($end < $lastPage - 1)
                            <span class="block-paginationGirls" style="cursor: default;">...</span>
                        @endif
                        <a href="{{ $girls->url($lastPage) }}" class="block-paginationGirls">{{ $lastPage }}</a>
                    @endif
                </div>
                
                @if($girls->hasMorePages())
                    <a href="{{ $girls->nextPageUrl() }}" class="arrowPagination arrowPagination-next">
                        <img src="{{ asset('img/arrowNext.svg') }}" alt="">
                    </a>
                @else
                    <span class="arrowPagination arrowPagination-next" style="opacity: 0.5; cursor: not-allowed;">
                        <img src="{{ asset('img/arrowNext.svg') }}" alt="">
                    </span>
                @endif
            </div>
            @endif
            <a href="#!" target="_blank" class="bannerBottomTG">
                <img src="{{ asset('img/bannerTG.png') }}" alt="">
            </a>
            
            <div class="textBottomPage">
                <h4>
                    Массажистки {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }} — релакс и наслаждение
                </h4>
                <p>
                    Массаж от опытной девушки — это не только расслабление мышц, но и чувственное удовольствие. Выбирай массажистку с интим-услугами и погрузись в мир наслаждения.
                </p>
                <h4>
                    Виды массажа:
                </h4>
                <p>
                    Классический расслабляющий массаж;
                    <br>
                    Эротический массаж с элементами ласк;
                    <br>
                    Урологический массаж для мужского здоровья;
                    <br>
                    Тайский массаж всего тела;
                    <br>
                    Массаж с продолжением — интим после релакса.
                </p>
                <h4>
                    Преимущества массажисток:
                </h4>
                <p>
                    Профессиональные навыки массажа;
                    <br>
                    Комфортная обстановка и чистота;
                    <br>
                    Возможность совместить релакс с интимом;
                    <br>
                    Конфиденциальность встречи;
                    <br>
                    Индивидуальный подход к каждому клиенту.
                    <br><br>
                    Массаж от красивой девушки — это уникальное сочетание физического расслабления и чувственного удовольствия. Попробуй и убедись сам!
                </p>
            </div>
        </div>
    </section>
    
    @include('components.filters-modals')
@endsection

@section('page_scripts')
    <script src="{{ asset('js/filters.js') }}?v={{ time() }}"></script>
@endsection

