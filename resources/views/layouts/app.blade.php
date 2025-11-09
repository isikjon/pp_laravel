<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Каталог анкет с подробными фильтрами и проверенными предложениями в Москве и Санкт-Петербурге.')">
    @php
        $styleCssVersion = file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time();
        $adaptiveCssVersion = file_exists(public_path('css/adaptive.css')) ? filemtime(public_path('css/adaptive.css')) : time();
        $appJsVersion = file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : time();
        $cityJsVersion = file_exists(public_path('js/city.js')) ? filemtime(public_path('js/city.js')) : time();
        $favoritesJsVersion = file_exists(public_path('js/favorites.js')) ? filemtime(public_path('js/favorites.js')) : time();
    @endphp
    <title>@yield('title', 'ProstitutkiMoscow')</title>
    {{-- <link rel="preload" href="{{ cached_asset('css/style.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'"> --}}
    {{-- <link rel="preload" href="{{ cached_asset('css/adaptive.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'"> --}}
    <link rel="preload" href="{{ cached_asset('img/logo.svg') }}" as="image" fetchpriority="high" type="image/svg+xml">
    {{-- <noscript>
        <link rel="stylesheet" href="{{ cached_asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ cached_asset('css/adaptive.css') }}">
    </noscript> --}}
    <link rel="icon" href="{{ cached_asset('img/icon.png') }}" type="image/x-icon">
    <link rel="preconnect" href="https://msk-z.prostitutki-today.site" crossorigin>
    @stack('styles')
</head>
<body>
    @include('components.header')
    
    <main>
        @yield('content')
    </main>
    
    @include('components.footer')
    
    @include('components.contact-form')
    @include('components.metro-modal')
    @include('components.search-dropdown')
    @include('components.city-modal')
    
    <script defer src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
    <script defer src="{{ cached_asset('js/app.js') }}"></script>
    <script defer src="{{ cached_asset('js/city.js') }}"></script>
    <script defer src="{{ cached_asset('js/favorites.js') }}"></script>
    <script>
        (function () {
            if (typeof window === 'undefined') {
                return;
            }

            if (!window.__girlCardLogger && 'ResizeObserver' in window) {
                var sizeCache = new WeakMap();
                var resizeObserver = new ResizeObserver(function (entries) {
                    entries.forEach(function (entry) {
                        var el = entry.target;
                        if (el.classList && el.classList.contains('girlCard--skeleton')) {
                            return;
                        }
                        var rect = entry.contentRect;
                        var current = {
                            width: Math.round(rect.width),
                            height: Math.round(rect.height)
                        };
                        var prev = sizeCache.get(el);
                        if (!prev) {
                            console.debug('[girlCard:init]', {
                                id: el.dataset ? el.dataset.girlId || null : null,
                                width: current.width,
                                height: current.height,
                                timestamp: performance.now ? performance.now().toFixed(1) : null
                            });
                            sizeCache.set(el, current);
                            return;
                        }

                        if (prev.width !== current.width || prev.height !== current.height) {
                            console.debug('[girlCard:resize]', {
                                id: el.dataset ? el.dataset.girlId || null : null,
                                width: current.width,
                                height: current.height,
                                deltaWidth: current.width - prev.width,
                                deltaHeight: current.height - prev.height,
                                timestamp: performance.now ? performance.now().toFixed(1) : null
                            });
                            sizeCache.set(el, current);
                        }
                    });
                });

                var observeCards = function (scope) {
                    var context = scope && scope.querySelectorAll ? scope : document;
                    var cards = context.querySelectorAll('.girlCard:not(.girlCard--skeleton)');
                    cards.forEach(function (card) {
                        if (sizeCache.has(card)) {
                            return;
                        }
                        sizeCache.set(card, null);
                        resizeObserver.observe(card);
                    });
                };

                document.addEventListener('DOMContentLoaded', function () {
                    observeCards(document);
                });

                document.addEventListener('girlCards:mutated', function (event) {
                    if (event.detail && event.detail.scope) {
                        observeCards(event.detail.scope);
                    } else {
                        observeCards(document);
                    }
                });

                window.__girlCardLogger = {
                    observe: observeCards,
                    resizeObserver: resizeObserver
                };
            }

            if ('PerformanceObserver' in window) {
                try {
                    var clsObserver = new PerformanceObserver(function (list) {
                        list.getEntries().forEach(function (entry) {
                            if (entry.hadRecentInput) {
                                return;
                            }
                            var sources = (entry.sources || []).map(function (source) {
                                if (!source.node) {
                                    return null;
                                }
                                var classList = source.node.classList ? Array.from(source.node.classList).join('.') : '';
                                return {
                                    selector: classList || source.node.tagName.toLowerCase(),
                                    text: source.node.textContent ? source.node.textContent.trim().slice(0, 80) : '',
                                    previousRect: source.previousRect,
                                    currentRect: source.currentRect
                                };
                            }).filter(Boolean);
                            console.warn('[CLS]', {
                                value: entry.value,
                                time: entry.startTime.toFixed(1),
                                sources: sources
                            });
                        });
                    });

                    clsObserver.observe({ type: 'layout-shift', buffered: true });
                    window.__clsObserver = clsObserver;
                } catch (error) {
                    console.debug('Layout shift observer unavailable', error);
                }
            }
        })();
    </script>
    @stack('scripts')
    @yield('page_scripts')
</body>
</html>

