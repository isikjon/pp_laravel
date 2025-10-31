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
    <script src="{{ asset('js/favorites-page.js') }}?v={{ time() }}"></script>
@endsection

