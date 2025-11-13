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
        header{position:unset!important;width:100%;background:#FFFFFF;box-shadow:0 5px 20px rgba(44,41,51,.10);padding:15px 0 20px;z-index:10;}
        main{margin-top:30px;}
        .flexTopHeader{display:flex;align-items:center;justify-content:space-between;gap:24px;flex-wrap:nowrap;}
        .flexTopHeader>a{flex:0 0 auto;display:inline-flex;align-items:center;}
        .center-flexTopHeader{flex:0 0 auto;display:flex;align-items:center;gap:20px;white-space:nowrap;}
        .center-flexTopHeader svg{flex:0 0 auto;}
        .center-flexTopHeader p{margin:0;white-space:nowrap;}
        .cityChoose,.headerMetro{display:flex;align-items:center;gap:10px;padding:6px 12px;border-radius:12px;background:rgba(238,238,238,.4);min-height:44px;min-width:220px;}
        .cityChoose span,.headerMetro span{display:inline-block;min-width:90px;}
        .modal-cityChoose{cursor:default!important;pointer-events:none;}
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
        .filtersBtn{display:inline-flex;align-items:center;justify-content:center;gap:15px;padding:7px 44px;border-radius:12px;border:1px solid #7E1D32;background:#7E1D32;color:#FFFFFF;cursor:pointer;font-size:16px;font-weight:500;min-height:48px;}
        .filtersBtn__icon{display:inline-block;width:24px;height:24px;flex:0 0 24px;background-repeat:no-repeat;background-size:24px 24px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M21 17H17' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M11 17H3' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M16.1213 14.8787C17.2929 16.0504 17.2929 17.9496 16.1213 19.1213C14.9496 20.2929 13.0504 20.2929 11.8787 19.1213C10.7071 17.9496 10.7071 16.0504 11.8787 14.8787C13.0504 13.7071 14.9496 13.7071 16.1213 14.8787' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M3 7H7' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M12.1213 4.87873C13.2929 6.05038 13.2929 7.94962 12.1213 9.12127C10.9496 10.2929 9.05038 10.2929 7.87873 9.12127C6.70709 7.94962 6.70709 6.05038 7.87873 4.87873C9.05038 3.70709 10.9496 3.70709 12.1213 4.87873' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3Cpath d='M13 7H21' stroke='white' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");}
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
        .blockPrecises-right-wrapper-girlCard{min-height:184px;display:flex;flex-direction:column;gap:12px;}
        .blockPrecises-right-wrapper-girlCard__top,.blockPrecises-right-wrapper-girlCard__top-1{display:flex;align-items:stretch;width:100%;min-height:88px;gap:10px;}
        .blockPrecises-right-wrapper-girlCard__topBlock{flex:0 0 70px;width:70px!important;min-height:88px;border-radius:12px;padding:10px;display:flex;flex-direction:column;align-items:center;justify-content:space-between;background:linear-gradient(135deg,#fee97f 0%,#ffdf65 100%);position:relative;overflow:hidden;color:#292D33;}
        .blockPrecises-right-wrapper-girlCard__topBlock[data-theme="hour-1"],.blockPrecises-right-wrapper-girlCard__topBlock[data-theme="hour-2"]{color:#292D33;}
        .blockPrecises-right-wrapper-girlCard__topBlock[data-theme="anal"],.blockPrecises-right-wrapper-girlCard__topBlock[data-theme="night"]{background:linear-gradient(135deg,#7e1d32 0%,#b52b4c 100%);color:#FFFFFF;}
        .blockPrecises-right-wrapper-girlCard__topBlock[data-theme="night"]{background:linear-gradient(135deg,#1d1a4a 0%,#312d73 100%);}
        .blockPrecises-right-wrapper-girlCard__topBlock span{font-weight:600;color:inherit;}
        .hourFlex-blockPrecises-right-wrapper-girlCard__top{display:flex;align-items:center;justify-content:center;gap:5px;}
        .hourFlex-blockPrecises-right-wrapper-girlCard__top p{margin:0;color:inherit;font-weight:600;}
        .favorite-toggle{display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:50%;background:rgba(126,29,50,.08);transition:background .2s ease;}
        .favorite-toggle__icon{display:block;width:24px;height:24px;background-repeat:no-repeat;background-size:24px 24px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M16.8199 2H7.17989C5.04989 2 3.31989 3.74 3.31989 5.86V19.95C3.31989 21.75 4.60989 22.51 6.18989 21.64L11.0699 18.93C11.5899 18.64 12.4299 18.64 12.9399 18.93L17.8199 21.64C19.3999 22.52 20.6899 21.76 20.6899 19.95V5.86C20.6799 3.74 18.9499 2 16.8199 2Z' stroke='%237E1D32' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");}
        .favorite-toggle.is-active,.favorite-toggle.is-favorite{background:rgba(126,29,50,.15);}
        .favorite-toggle.is-active .favorite-toggle__icon,.favorite-toggle.is-favorite .favorite-toggle__icon{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'%3E%3Cpath d='M16.82 2H7.18C5.05 2 3.32 3.74 3.32 5.86V19.95C3.32 21.75 4.61 22.51 6.19 21.64L11.07 18.93C11.59 18.64 12.43 18.64 12.94 18.93L17.82 21.64C19.4 22.52 20.69 21.76 20.69 19.95V5.86C20.68 3.74 18.95 2 16.82 2Z' fill='%237E1D32' stroke='%237E1D32' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");}
        .bottom-girlCard{display:flex;align-items:center;justify-content:space-between;gap:12px;font-size:12px;color:#6E6E6E;}
        .flex-bottom-girlCard{display:inline-flex;align-items:center;gap:8px;}
        .verified-status-text{color:#006400;font-weight:600;}
        .more-info{display:flex;align-items:center;justify-content:center;min-height:48px;padding:12px 24px;border-radius:12px;background:#7E1D32;color:#FFFFFF;margin:35px auto 0;width:max-content;}
        .hamburger-menu{display:none;}
        #menu__toggle{opacity:0;display:none;}
        .menu__btn{display:flex;align-items:center;cursor:pointer;z-index:10100;width:36px;height:36px;position:relative;}
        .menu__box{position:fixed;visibility:hidden;top:0;right:-100%;width:100%;height:100%;margin:0;list-style:none;background:rgba(255,255,255,1);backdrop-filter:blur(10px);transition-duration:0.25s;z-index:10050;padding:20px 50px 30px;overflow-y:auto;}
        #menu__toggle:checked ~ .menu__box{visibility:visible;right:0;}
        .closeBurger{display:none;}
        #menu__toggle:checked ~ .menu__btn .closeBurger{display:flex;}
        #menu__toggle:checked ~ .menu__btn .burgerSvg{display:none;}
        @media (max-width:1220px){.hamburger-menu{display:block!important;}.center-flexTopHeader{display:none!important;}.lineHeader{display:none!important;}.flexBottomHeader:not(.listBurgerUl .flexBottomHeader){display:none!important;}.rightHeaderTop .search-rightHeaderTop{display:none!important;}.rightHeaderTop .modalRegistration{display:none!important;}.rightHeaderTop{justify-content:flex-end!important;gap:0!important;}}
        @media (max-width:768px){.girlsSection{display:flex!important;flex-direction:column!important;gap:24px!important;min-height:1080px!important;}.girlCard{min-height:540px!important;width:100%!important;}.wrapper-girlCard{flex-direction:column!important;align-items:center!important;gap:16px!important;}.photoGirl{width:100%!important;max-width:280px!important;height:420px!important;flex:0 0 420px!important;}.photoGirl__img{width:100%!important;height:420px!important;}.right-wrapper-girlCard{width:100%!important;max-width:100%!important;}.girlCard.is-mobile-hidden,.girlCard.mobile-hidden-default{display:none!important;}}
    </style>
    <link rel="preload" href="{{ cached_asset('css/style.css') }}" as="style">
    <link rel="preload" href="{{ cached_asset('css/adaptive.css') }}" as="style">
    <noscript>
        <link rel="stylesheet" href="{{ cached_asset('css/style.css') }}">
        <link rel="stylesheet" href="{{ cached_asset('css/adaptive.css') }}">
    </noscript>
    <script>
        (function(){
            var cssFiles = @json([
                cached_asset('css/style.css'),
                cached_asset('css/adaptive.css')
            ]);

            if (!('requestAnimationFrame' in window)) {
                cssFiles.forEach(function(href){
                    var link=document.createElement('link');
                    link.rel='stylesheet';
                    link.href=href;
                    document.head.appendChild(link);
                });
                return;
            }

            function loadCSS(href){
                var link=document.createElement('link');
                link.rel='stylesheet';
                link.href=href;
                link.media='print';
                link.onload=function(){ link.media='all'; };
                document.head.appendChild(link);
            }

            requestAnimationFrame(function(){
                if ('requestIdleCallback' in window) {
                    requestIdleCallback(function(){ cssFiles.forEach(loadCSS); });
                } else {
                    cssFiles.forEach(loadCSS);
                }
            });
        })();
    </script>
    <link rel="preload" href="{{ cached_asset('img/logo.svg') }}" as="image" fetchpriority="high" type="image/svg+xml">
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
    @include('components.filters-modals')
    
    <script defer src="{{ cached_asset('js/app.js') }}"></script>
    <script defer src="{{ cached_asset('js/city-new.js') }}"></script>
    <script defer src="{{ cached_asset('js/city-navigation.js') }}"></script>
    <script defer src="{{ cached_asset('js/filters.js') }}"></script>
    <script defer src="{{ cached_asset('js/favorites.js') }}"></script>
    <script defer src="{{ cached_asset('js/menu.js') }}"></script>
    <script defer src="{{ cached_asset('js/images-fallback.js') }}"></script>
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
                            sizeCache.set(el, current);
                            return;
                        }

                        if (prev.width !== current.width || prev.height !== current.height) {
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

