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
    <style id="critical-css">
        :root{color-scheme:light;}
        *{box-sizing:border-box;}
        html,body{margin:0;height:100%;background:#FFFFFF;color:#292D33;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;}
        body{overflow-x:hidden;}
        a{color:#292D33;text-decoration:none;}
        img{max-width:100%;height:auto;display:block;}
        .container{width:100%;max-width:1320px;margin:0 auto;padding:0 16px;position:relative;}
        header{position:fixed;top:0;width:100%;background:#FFFFFF;box-shadow:0 5px 20px rgba(44,41,51,.10);padding:15px 0 20px;z-index:10;}
        main{margin-top:180px;}
        .flexTopHeader{display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:nowrap;}
        .flexTopHeader>a{flex:0 0 auto;display:inline-flex;align-items:center;}
        .center-flexTopHeader{flex:0 0 auto;display:flex;align-items:center;gap:20px;white-space:nowrap;}
        .center-flexTopHeader svg{flex:0 0 auto;}
        .center-flexTopHeader p{margin:0;white-space:nowrap;}
        .cityChoose,.headerMetro{display:flex;align-items:center;gap:10px;padding:6px 12px;border-radius:12px;background:rgba(238,238,238,.4);min-height:44px;min-width:220px;}
        .cityChoose span,.headerMetro span{display:inline-block;min-width:90px;}
        .rightHeaderTop{flex:1 1 auto;display:flex;align-items:center;justify-content:flex-end;gap:20px;min-width:260px;}
        .search-rightHeaderTop{position:relative;width:100%;max-width:240px;}
        .search-rightHeaderTop input{width:100%;min-height:48px;padding:12px 60px 12px 16px;border-radius:12px;border:1px solid #D7D7D7;font-size:16px;font-weight:500;color:#292D33;}
        .search-rightHeaderTop button{position:absolute;right:16px;top:50%;transform:translateY(-50%);background:none;border:0;padding:0;display:flex;align-items:center;justify-content:center;cursor:pointer;}
        .modalRegistration{display:inline-flex;align-items:center;justify-content:center;min-height:44px;padding:10px 16px;border-radius:12px;background:#7E1D32;color:#FFFFFF;font-size:14px;font-weight:600;gap:8px;}
        .lineHeader{width:100%;height:1px;background:#EEEEEE;margin:22px 0;}
        .flexBottomHeader{display:flex;align-items:center;justify-content:space-between;gap:10px;margin-top:18px;}
        .flexBottomHeader a{display:inline-flex;align-items:center;gap:10px;padding:10px 16px;border-radius:12px;color:#292D33;font-size:16px;font-weight:500;min-height:44px;transition:color .3s ease;}        
        .flexBottomHeader img{pointer-events:none;}
        .textSection{margin-top:35px;}
        .textSection h1{font-size:25px;font-weight:700;line-height:1.6;margin:0;}
        .textSection p{margin-top:15px;font-size:14px;line-height:1.6;color:#292D33;}
        .girlsSection{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:32px 24px;margin-top:35px;align-items:stretch;}
        .girlCard{display:flex;flex-direction:column;gap:12px;border:1px solid #DEDEE2;border-radius:10px;padding:12px;background:#FFFFFF;min-height:360px;transition:opacity .3s ease;}
        .wrapper-girlCard{display:flex;gap:15px;align-items:stretch;}
        .photoGirl{flex:0 0 210px;aspect-ratio:2/3;border-radius:12px;overflow:hidden;background:#EFEFEF;position:relative;display:flex;align-items:center;justify-content:center;}
        .photoGirl__img{width:100%;height:100%;object-fit:cover;}
        .right-wrapper-girlCard{flex:1 1 auto;display:flex;flex-direction:column;gap:7px;max-width:100%;}
        .name-girlCard{display:flex;align-items:center;gap:10px;}
        .name-girlCard p{font-size:20px;font-weight:500;margin:0;}
        .ageGirlCard{font-size:16px;color:#292D33;margin:0;}
        .line-right-wrapper-girlCard{border-top:1px dashed #DADADA;margin:6px 0;}
        .infoParameters-right-wrapper-girlCard{display:flex;gap:15px;flex-wrap:wrap;font-size:14px;}
        .infoParameters-right-wrapper-girlCard p{margin:0;}
        .infoParameters-right-wrapper-girlCard span{color:#6E6E6E;font-size:12px;}
        .tel-right-wrapper-girlCard{font-size:16px;font-weight:700;color:#292D33;}
        .infoTownWhatsapp{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:14px;}
        .infoTownWhatsapp p{margin:0;}
        .metro-right-wrapper-girlCard{color:#03AFFF;font-size:14px;}
        .blockPrecises-right-wrapper-girlCard{min-height:180px;display:flex;flex-direction:column;gap:10px;}
        .blockPrecises-right-wrapper-girlCard__top,.blockPrecises-right-wrapper-girlCard__top-1{display:flex;align-items:stretch;width:100%;min-height:90px;}
        .blockPrecises-right-wrapper-girlCard__topBlock{flex:1 1 0;min-height:80px;border-radius:8px;padding:8px;display:flex;flex-direction:column;justify-content:space-between;background:#FEEA7F center/cover no-repeat;}
        .bottom-girlCard{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px;color:#6E6E6E;}
        .flex-bottom-girlCard{display:inline-flex;align-items:center;gap:8px;}
        .verified-status-text{color:#006400;font-weight:600;}
        .more-info{display:flex;align-items:center;justify-content:center;min-height:48px;padding:12px 24px;border-radius:12px;background:#7E1D32;color:#FFFFFF;margin:35px auto 0;width:max-content;}
        @media (max-width:1200px){.center-flexTopHeader{flex-wrap:wrap;gap:12px;}.cityChoose,.headerMetro{min-width:calc(50% - 12px);width:calc(50% - 12px);}.rightHeaderTop .search-rightHeaderTop{max-width:none;}}
        @media (max-width:930px){.flexTopHeader{flex-wrap:wrap;gap:16px;}.center-flexTopHeader{order:3;width:100%;justify-content:space-between;}.cityChoose,.headerMetro{width:48%;}.rightHeaderTop{order:2;width:100%;justify-content:space-between;}.rightHeaderTop .search-rightHeaderTop{flex:1 1 100%;}}
        @media (max-width:768px){.girlsSection{grid-template-columns:minmax(0,1fr);}.girlCard{min-height:420px;}.photoGirl{flex:0 0 auto;width:100%;}.girlCard.is-mobile-hidden{display:none!important;}}
        @media (max-width:600px){.center-flexTopHeader{flex-direction:column;align-items:flex-start;gap:8px;}.cityChoose,.headerMetro{width:100%;}}
    </style>
    <link rel="preload" href="{{ cached_asset('css/style.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ cached_asset('css/adaptive.css') }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ cached_asset('img/logo.svg') }}" as="image" fetchpriority="high" type="image/svg+xml">
    <noscript>
        <link rel="stylesheet" href="{{ cached_asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ cached_asset('css/adaptive.css') }}">
    </noscript>
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

