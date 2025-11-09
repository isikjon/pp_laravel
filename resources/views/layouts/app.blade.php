<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    @php
        $styleCssVersion = file_exists(public_path('css/style.css')) ? filemtime(public_path('css/style.css')) : time();
        $adaptiveCssVersion = file_exists(public_path('css/adaptive.css')) ? filemtime(public_path('css/adaptive.css')) : time();
        $appJsVersion = file_exists(public_path('js/app.js')) ? filemtime(public_path('js/app.js')) : time();
        $cityJsVersion = file_exists(public_path('js/city.js')) ? filemtime(public_path('js/city.js')) : time();
        $favoritesJsVersion = file_exists(public_path('js/favorites.js')) ? filemtime(public_path('js/favorites.js')) : time();
    @endphp
    <title>@yield('title', 'ProstitutkiMoscow')</title>
    <link rel="preload" href="{{ asset('css/style.css') }}?v={{ $styleCssVersion }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <link rel="preload" href="{{ asset('css/adaptive.css') }}?v={{ $adaptiveCssVersion }}" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript>
        <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ $styleCssVersion }}">
        <link rel="stylesheet" href="{{ asset('css/adaptive.css') }}?v={{ $adaptiveCssVersion }}">
    </noscript>
    <link rel="icon" href="{{ asset('img/icon.png') }}" type="image/x-icon">
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
    <script defer src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script defer src="{{ asset('js/app.js') }}?v={{ $appJsVersion }}"></script>
    <script defer src="{{ asset('js/city.js') }}?v={{ $cityJsVersion }}"></script>
    <script defer src="{{ asset('js/favorites.js') }}?v={{ $favoritesJsVersion }}"></script>
    @stack('scripts')
    @yield('page_scripts')
</body>
</html>

