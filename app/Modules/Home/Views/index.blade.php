@section('meta_description', 'Каталог с подробными фильтрами для выбора девушек в Москве и Санкт-Петербурге: проверенные анкеты, актуальные цены и удобный поиск по параметрам.')

@extends('layouts.app')

@section('title', 'Проститутки в ' . (($cityName ?? 'Москва') === 'Санкт-Петербург' ? 'Санкт-Петербурге' : 'Москве'))

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="filtersBtn">
                <img src="{{ cached_asset('img/filter.svg') }}" alt="" width="24" height="24" decoding="async">
                Фильтры
            </div>
            <div class="textSection">
                <h1>
                    Проститутки в {{ ($cityName ?? 'Москва') === 'Санкт-Петербург' ? 'Санкт-Петербурге' : 'Москве' }}
                </h1>
                <p>
                    Лучшие проститутки {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }} собраны на одном сайте с актуальными ценами и проверенными анкетами. Используйте фильтры, чтобы быстро отфильтровать девушек по услугам, району и бюджету.
                </p>
            </div>
            
            <div class="girlsSection" data-current-page="{{ $girls->currentPage() }}">
                @foreach(($initialGirls ?? $girls) as $girl)
                    @include('components.girl-card', array_merge($girl, [
                        'fetch_high' => $loop->first,
                        'mobile_hidden' => !$loop->first
                    ]))
                @endforeach
            </div>

            <script>
                window.__DEFERRED_GIRLS = @json(($preloadedGirls ?? collect())->values());
                window.__HAS_MORE_PAGES = @json($girls->hasMorePages());
                window.__CURRENT_PAGE = @json($girls->currentPage());
            </script>
            
            <a href="#!" class="more-info" @if(empty($hasMoreInitial)) style="display:none" @endif>
                Показать ещё
            </a>
            
            @if($girls->hasPages())
            <div class="paginationGirls">
                @if($girls->onFirstPage())
                    <span class="arrowPagination arrowPagination-prev" style="opacity: 0.5; cursor: not-allowed;" aria-hidden="true">
                        <img src="{{ cached_asset('img/arrowLeft.svg') }}" alt="" width="36" height="36" decoding="async">
                    </span>
                @else
                    <a href="{{ $girls->previousPageUrl() }}" class="arrowPagination arrowPagination-prev" aria-label="Предыдущая страница">
                        <img src="{{ cached_asset('img/arrowLeft.svg') }}" alt="" width="36" height="36" decoding="async" aria-hidden="true">
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
                    <a href="{{ $girls->nextPageUrl() }}" class="arrowPagination arrowPagination-next" aria-label="Следующая страница">
                        <img src="{{ cached_asset('img/arrowNext.svg') }}" alt="" width="36" height="36" decoding="async" aria-hidden="true">
                    </a>
                @else
                    <span class="arrowPagination arrowPagination-next" style="opacity: 0.5; cursor: not-allowed;" aria-hidden="true">
                        <img src="{{ cached_asset('img/arrowNext.svg') }}" alt="" width="36" height="36" decoding="async">
                    </span>
                @endif
            </div>
            @endif
            
            <a href="#" target="_blank" class="bannerBottomTG" aria-label="Открыть рекламный баннер Telegram">
                <img src="{{ cached_asset('img/bannerTG.webp') }}" alt="Рекламный баннер Telegram" loading="lazy" decoding="async">
            </a>
            
            <div class="textBottomPage">
                <h2>
                    Проститутки {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }} – каждый день новые эксперименты страсти
                </h2>
                <p>
                    Наш портал провел масштабную работу, чтобы выбор удовольствия и подходящей мастерицы на этот вечер не потребовал лишних трат времени. Крупнейший каталог страстных шлюх {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }} с наглядными фотографиями и удобными фильтрами.
                </p>
                <h2>
                    Минимум усилий для подготовки яркого вечера
                </h2>
                <p>
                    Забудьте о постоянных стрессах и ненужных переплатах для поиска девушки на вечер с их вечными капризами и призрачными шансами на продолжение в первый вечер. Гарантия идеальной страсти и блаженства в первую же встречу – представленные шлюшки Москвы с подробным описанием услуг и актуальными ценами.
                </p>
                <h2>
                    Вариации досуга просто огромны, просто определитесь со своими предпочтениями:
                </h2>
                <p>
                    Красота и соблазн страстного стриптиза;
                    <br>
                    Классический секс с разнообразием форматов и поз;
                    <br>
                    Групповой секс с массой экспериментов;
                    <br>
                    Анальный секс – откройте для себя запретное наслаждение;
                    <br>
                    Фистинг для отважных партнеров;
                    <br>
                    Глубокий минет, принимая сперму в рот или на лицо;
                    <br>
                    Готовность наказать партнера или стать жертвой;
                    <br>
                    Ролевые игры с новыми гранями наслаждения.
                </p>
                <h2>
                    Как же провести идеальную встречу с выбранной путаной:
                </h2>
                <p>
                    Лучше не показывать настоящий номер телефона – в идеале заведите временный, с которого будете звонить и согласовывать встречи;
                    <br>
                    Оптимальный вариант для встречи – нейтральное место или апартаменты проститутки;
                    <br>
                    Важно понимать – проститутки работают за деньги. Поэтому позаботьтесь о наличии у себя достаточной суммы;
                    <br>
                    Риск – дело хорошее, но здоровье важнее. Не забываем про контрацепцию.
                    <br><br>
                    Не стоит брать с собой вес деньги. Просто рассчитайте достаточную сумму с минимальным запасом для чаевых девушке, если она их заслужила.
                    <br><br>
                    Индивидуалки редко крадут личные вещи. Об этом можно не переживать, но и слишком много ювелирных драгоценностей брать также не следует.
                </p>
                <h2>
                    Насчет цены проституток – на что же ориентироваться
                </h2>
                <p>
                    Обычно цена {{ $cityName === 'Санкт-Петербург' ? 'петербургских' : 'московских' }} проституток зависит от времени суток. Также важно учесть личные пожелания и требования клиента. Цена может различаться в зависимости от видов секса. Важно понимать – за нестандартные услуги часто придется доплатить. Да и сами девушки также могут определять цены индивидуально – в частности, девочки подороже часто пытаются поддерживать должный уровень услуг, учитывать пожелания клиента, следят за внешностью и поддерживают интересный разговор.
                </p>
                <h2>
                    Наслаждение в любой момент – без риска облома
                </h2>
                <p>
                    Будем откровенны – каждый из мужчин временами сталкивался с различными отказами и слишком дорогими обломами. Достаточно вспомнить ситуации, когда потратил в эту вечер на роскошную спутницу впечатляющую сумму, но единственной наградой остался поцелуй. Проблема успешно решается благодаря обращению к выбранной путане – она уже готова дарить страсть и наслаждение. Фактически, получаем гарантию секса – и именно такого, за который платим.
                </p>
                <h2>
                    Остается только один шаг для наслаждения
                </h2>
                <p>
                    Просто выберите анкету, согласуйте услуги и готовьтесь к предстоящей встрече. Каталог проституток {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }} готов впечатлять своим разнообразием – от классической близости для ценителей нежности для смелых экспериментов, помогающих расслабиться и дать свободу внутреннему я.
                </p>
            </div>
        </div>
    </section>
    
    @include('components.filters-modals')
@endsection

@section('page_scripts')
    @php
        $filtersJsVersion = file_exists(public_path('js/filters.js')) ? filemtime(public_path('js/filters.js')) : time();
        $filtersJsSrc = asset('js/filters.js') . '?v=' . $filtersJsVersion;
    @endphp
    <script>
        const filtersScriptSrc = @json($filtersJsSrc);
        let filtersScriptLoaded = false;

        function loadFiltersScript(callback) {
            if (filtersScriptLoaded) {
                if (typeof callback === 'function') {
                    callback();
                }
                return;
            }

            filtersScriptLoaded = true;
            const script = document.createElement('script');
            script.src = filtersScriptSrc;
            script.defer = true;
            if (typeof callback === 'function') {
                script.addEventListener('load', callback, { once: true });
            }
            document.head.appendChild(script);
        }

        if ('requestIdleCallback' in window) {
            requestIdleCallback(function () {
                loadFiltersScript();
            });
        } else {
            window.addEventListener('load', function () {
                loadFiltersScript();
            });
        }

        document.addEventListener('click', function onFirstInteractive(event) {
            if (event.target.closest('.filtersBtn') || event.target.closest('.more-info') || event.target.closest('.btn-formFilterModal__btn')) {
                loadFiltersScript();
                document.removeEventListener('click', onFirstInteractive);
            }
        });
    </script>
@endsection
