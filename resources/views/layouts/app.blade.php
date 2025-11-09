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
    {{--    <noscript>
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
            let observerInstance = null;

            function loadImage(img) {
                if (!img || img.dataset.loaded === 'true') {
                    return;
                }
                const realSrc = img.getAttribute('data-src');
                if (realSrc) {
                    img.src = realSrc;
                    img.removeAttribute('data-src');
                }
                img.dataset.loaded = 'true';
            }

            function getObserver() {
                if (observerInstance || !('IntersectionObserver' in window)) {
                    return observerInstance;
                }

                observerInstance = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (entry.isIntersecting) {
                            loadImage(entry.target);
                            observerInstance.unobserve(entry.target);
                        }
                    });
                }, { rootMargin: '150px 0px' });

                return observerInstance;
            }

            window.observeDeferredImages = function (scope) {
                const context = scope instanceof Element ? scope : document;
                const images = context.querySelectorAll ? context.querySelectorAll('img.deferred-image[data-src]') : [];

                if (!images.length) {
                    return;
                }

                const observer = getObserver();

                images.forEach(function (img) {
                    if (img.dataset.immediate === 'true') {
                        loadImage(img);
                        return;
                    }

                    if (observer) {
                        observer.observe(img);
                    } else {
                        loadImage(img);
                    }
                });
            };

            document.addEventListener('DOMContentLoaded', function () {
                window.observeDeferredImages();
            });
        })();
    </script>
    @stack('scripts')
    @yield('page_scripts')
</body>
</html>

