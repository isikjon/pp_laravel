@extends('layouts.app')

@section('title', 'Интим-салоны в ' . getCityNameInCase('prepositional'))

@push('styles')
<style>
.salon-preview-img {
    cursor: zoom-in;
    transition: transform 0.3s ease;
}

.salon-preview-img:hover {
    transform: scale(1.02);
}
</style>
@endpush

@section('content')
    <section class="mainContent">
        <div class="container">
            <div class="textSection">
                <h1>
                    Интим-салоны в {{ getCityNameInCase('prepositional') }}
                </h1>
                <p>
                    Лучшие интим-салоны {{ getCityNameInCase('genitive') }} – профессиональный сервис и высокий уровень обслуживания. В каждом салоне работают красивые и опытные девушки, готовые подарить вам незабываемые моменты наслаждения.
                </p>
            </div>
            
            <div class="salonsSection" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 30px; margin-top: 40px;">
                @foreach($salons as $salon)
                    <div class="salonCard" style="background: #fff; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.1); transition: transform 0.3s;">
                        @if(!empty($salon->images) && is_array($salon->images))
                            @php
                                $firstImage = is_array($salon->images[0]) ? ($salon->images[0]['preview'] ?? $salon->images[0]['full'] ?? '') : $salon->images[0];
                                $fullImage = is_array($salon->images[0]) ? ($salon->images[0]['full'] ?? $firstImage) : $salon->images[0];
                                
                                if (empty($firstImage) || $firstImage === 'null' || stripos($firstImage, 'deleted') !== false) {
                                    $firstImage = asset('img/noimage.png');
                                    $fullImage = asset('img/noimage.png');
                                }
                            @endphp
                            <div style="height: 250px; overflow: hidden; position: relative;">
                                <a href="{{ $fullImage }}" class="salon-lightbox-trigger" data-salon-id="{{ $salon->salon_id }}" onclick="event.stopPropagation();">
                                    <img src="{{ $firstImage }}" alt="{{ $salon->name }}" class="salon-preview-img" style="width: 100%; height: 100%; object-fit: cover;">
                                </a>
                            </div>
                        @else
                            <div style="height: 250px; overflow: hidden; position: relative;">
                                <img src="{{ asset('img/noimage.png') }}" alt="{{ $salon->name }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @endif
                        
                        <a href="{{ route('salon.show', $salon->salon_id) }}" style="text-decoration: none; color: inherit;">
                            <div style="padding: 20px;">
                                <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 15px; color: #7E1D32;">
                                    {{ $salon->name }}
                                </h3>
                                
                                <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 25 25" fill="none">
                                        <path d="M8.98256 5.64514C8.91638 5.81886 7.03845 10.5509 5.1688 15.2581L4.0437 18.0957H3.39015H2.74487V18.5921V19.0884H6.30217H9.85948V18.5921V18.0957H9.14802C8.61028 18.0957 8.44483 18.0709 8.47792 17.9881C8.49446 17.9385 8.80883 17.0616 9.16456 16.044C9.52029 15.0348 9.83466 14.2075 9.85948 14.2075C9.88429 14.2075 10.5296 15.3078 11.2989 16.648C12.4902 18.7079 12.7136 19.0636 12.7963 18.9395C12.846 18.8651 13.4664 17.7648 14.1613 16.5073C14.8645 15.2416 15.4602 14.2323 15.4932 14.2737C15.5677 14.3564 16.8086 17.8558 16.8086 17.9881C16.8086 18.0709 16.6349 18.0957 16.1468 18.0957H15.485V18.5921V19.0884H19.0009H22.5168V18.5921V18.0957H21.9047H21.2842L21.127 17.6655C20.9367 17.1609 17.5035 8.32552 16.8583 6.68751C16.6183 6.07532 16.3867 5.56241 16.3371 5.54586C16.2957 5.52932 15.4767 7.04324 14.5171 8.92116C13.5657 10.7908 12.7467 12.313 12.7053 12.3047C12.664 12.2965 11.8698 10.8322 10.9267 9.04525C9.99184 7.2666 9.18938 5.73614 9.13974 5.64514L9.04047 5.47968L8.98256 5.64514Z" fill="#7E1D32"/>
                                    </svg>
                                    <span>{{ $salon->metro ?? 'Метро не указано' }}</span>
                                </div>
                                
                                @if(!empty($salon->phones))
                                    <div style="margin-bottom: 10px; display: flex; align-items: center; gap: 8px;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                            <path d="M21.97 18.33c0 .36-.08.73-.25 1.09-.17.36-.39.7-.68 1.02-.49.54-1.03.93-1.64 1.18-.6.25-1.25.38-1.95.38-1.02 0-2.11-.24-3.26-.73s-2.3-1.15-3.44-1.98c-1.14-.85-2.24-1.77-3.28-2.78C6.37 15.41 5.45 14.31 4.6 13.17c-.84-1.14-1.49-2.28-1.96-3.41C2.17 8.63 1.94 7.56 1.94 6.54c0-.68.12-1.33.36-1.93.24-.61.62-1.17 1.15-1.67C4.01 2.31 4.67 2 5.38 2c.28 0 .56.06.81.18.26.12.49.3.67.56l2.32 3.27c.18.25.31.48.4.7.09.21.14.42.14.61 0 .24-.07.48-.21.71-.13.23-.32.47-.56.71l-.76.79c-.11.11-.16.24-.16.4 0 .08.01.15.03.23.03.08.06.14.08.2.18.33.49.76.93 1.28.45.52.93 1.05 1.45 1.58.54.53 1.06 1.02 1.59 1.47.52.44.95.74 1.29.92.05.02.11.05.18.08.08.03.16.04.25.04.17 0 .3-.06.41-.17l.76-.75c.25-.25.49-.44.72-.56.23-.14.46-.21.71-.21.19 0 .39.04.61.13.22.09.45.22.7.39l3.31 2.35c.26.18.44.39.55.64.1.25.16.5.16.78z" fill="#7E1D32"/>
                                        </svg>
                                        <span>{{ $salon->phones[0] }}</span>
                                    </div>
                                @endif
                                
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #eee;">
                                    <span style="color: #7E1D32; font-weight: 600;">{{ $salon->schedule ?? 'Круглосуточно' }}</span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            @if($salons->hasPages())
            <div class="paginationGirls" style="margin-top: 40px;">
                @if($salons->onFirstPage())
                    <span class="arrowPagination arrowPagination-prev" style="opacity: 0.5; cursor: not-allowed;">
                        <img src="{{ asset('img/arrowLeft.svg') }}" alt="">
                    </span>
                @else
                    <a href="{{ $salons->previousPageUrl() }}" class="arrowPagination arrowPagination-prev">
                        <img src="{{ asset('img/arrowLeft.svg') }}" alt="">
                    </a>
                @endif
                
                <div class="pagination__paginationGirls">
                    @for($i = 1; $i <= $salons->lastPage(); $i++)
                        <a href="{{ $salons->url($i) }}" class="block-paginationGirls {{ $i == $salons->currentPage() ? 'block-paginationGirls__active' : '' }}">
                            {{ $i }}
                        </a>
                    @endfor
                </div>
                
                @if($salons->hasMorePages())
                    <a href="{{ $salons->nextPageUrl() }}" class="arrowPagination arrowPagination-next">
                        <img src="{{ asset('img/arrowNext.svg') }}" alt="">
                    </a>
                @else
                    <span class="arrowPagination arrowPagination-next" style="opacity: 0.5; cursor: not-allowed;">
                        <img src="{{ asset('img/arrowNext.svg') }}" alt="">
                    </span>
                @endif
            </div>
            @endif
        </div>
    </section>
@endsection

@section('page_scripts')
<script>
window.__SELECTED_CITY = @json($selectedCity ?? 'moscow');
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lightGallery !== 'undefined') {
        document.querySelectorAll('.salon-lightbox-trigger').forEach(function(trigger) {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const imgUrl = this.getAttribute('href');
                const salonName = this.querySelector('img').getAttribute('alt');
                
                const tempContainer = document.createElement('div');
                tempContainer.style.display = 'none';
                tempContainer.innerHTML = '<a href="' + imgUrl + '"><img src="' + imgUrl + '" alt="' + salonName + '"></a>';
                document.body.appendChild(tempContainer);
                
                const gallery = lightGallery(tempContainer, {
                    plugins: [lgZoom, lgFullscreen],
                    speed: 500,
                    download: false,
                    selector: 'a'
                });
                
                setTimeout(() => {
                    tempContainer.querySelector('a').click();
                }, 10);
                
                tempContainer.addEventListener('lgAfterClose', function() {
                    gallery.destroy();
                    document.body.removeChild(tempContainer);
                });
            });
        });
    }
});
</script>
@endsection
