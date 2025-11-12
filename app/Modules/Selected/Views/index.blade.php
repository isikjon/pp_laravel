@extends('layouts.app')

@section('title', 'Избранные')

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="textSection">
                <h1>Избранные</h1>
            </div>
            
            <div class="girlsSection">
                <div class="girls-loader">
                    <div class="spinner"></div>
                    <p>Загрузка...</p>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('page_scripts')
    <script>
        window.__SELECTED_CITY = @json($selectedCity ?? 'moscow');
    </script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/favorites-page.js') }}?v={{ time() }}"></script>
@endsection

