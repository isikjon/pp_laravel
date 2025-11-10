@extends('layouts.app')

@section('title', $clubData['name'] . ' - Стрип-клуб')

@section('meta_description', $clubData['description'] ?? 'Стрип-клуб ' . $clubData['name'] . ' в ' . ($clubData['city'] ?? 'Москве') . '. ' . ($clubData['schedule'] ?? '') . ' Телефон: ' . (is_array($clubData['phones']) ? implode(', ', $clubData['phones']) : ($clubData['phones'] ?? '')) . '.')

@push('styles')
<style>
.club-gallery {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 30px;
}

.club-gallery a {
    display: block;
    height: 200px;
    border-radius: 10px;
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.club-gallery a:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}

.club-gallery a img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.club-gallery a:hover img {
    transform: scale(1.05);
}

.lg-backdrop {
    background-color: rgba(0, 0, 0, 0.95);
}

.lg-toolbar {
    background-color: rgba(0, 0, 0, 0.7);
}

.lg-actions .lg-next, .lg-actions .lg-prev {
    background-color: rgba(126, 29, 50, 0.8);
    color: #fff;
}

.lg-actions .lg-next:hover, .lg-actions .lg-prev:hover {
    background-color: rgba(126, 29, 50, 1);
}
</style>
@endpush

@section('content')
    <section class="mainContent">
        <div class="container">
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 30px; color: #7E1D32;">
                {{ $clubData['name'] }}
            </h1>
            
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 40px; margin-bottom: 40px;">
                <div>
                    @if(!empty($clubData['images']))
                        <div class="club-gallery" id="clubGallery">
                            @foreach($clubData['images'] as $image)
                                <a href="{{ $image }}" data-src="{{ $image }}">
                                    <img src="{{ $image }}" alt="{{ $clubData['name'] }}">
                                </a>
                            @endforeach
                        </div>
                    @endif
                    
                    @if($clubData['description'])
                        <div style="background: #f9f9f9; padding: 25px; border-radius: 15px; margin-bottom: 30px;">
                            <h3 style="font-size: 20px; font-weight: 600; margin-bottom: 15px;">Описание</h3>
                            <p style="line-height: 1.8; white-space: pre-wrap;">{{ $clubData['description'] }}</p>
                        </div>
                    @endif
                </div>
                
                <div>
                    <div style="background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); position: sticky; top: 20px;">
                        <h3 style="font-size: 22px; font-weight: 600; margin-bottom: 20px; color: #7E1D32;">Контакты</h3>
                        
                        @foreach($clubData['phones'] as $phone)
                            <a href="tel:{{ $phone }}" style="display: block; padding: 15px; background: #7E1D32; color: #fff; text-align: center; border-radius: 10px; text-decoration: none; font-weight: 600; margin-bottom: 10px;">
                                {{ $phone }}
                            </a>
                        @endforeach
                        
                        <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                            @if($clubData['metro'])
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: #7E1D32;">Метро:</strong>
                                    <div>{{ $clubData['metro'] }}</div>
                                </div>
                            @endif
                            
                            @if($clubData['district'])
                                <div style="margin-bottom: 15px;">
                                    <strong style="color: #7E1D32;">Район:</strong>
                                    <div>{{ $clubData['district'] }}</div>
                                </div>
                            @endif
                            
                            <div>
                                <strong style="color: #7E1D32;">График:</strong>
                                <div>{{ $clubData['schedule'] }}</div>
                            </div>
                        </div>
                        
                        @if(!empty($clubData['tariffs']))
                            <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid #eee;">
                                <strong style="color: #7E1D32; display: block; margin-bottom: 10px;">Тарифы:</strong>
                                @foreach($clubData['tariffs'] as $category => $prices)
                                    @if(is_array($prices))
                                        <div style="margin-bottom: 10px;">
                                            <strong>{{ $category }}:</strong>
                                            @foreach($prices as $duration => $price)
                                                <div style="display: flex; justify-content: space-between; margin-left: 15px;">
                                                    <span>{{ $duration }}</span>
                                                    <span style="font-weight: 600;">{{ $price }} ₽</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <a href="{{ route('stripclubs.index') }}" style="display: inline-block; padding: 15px 30px; background: #7E1D32; color: #fff; border-radius: 10px; text-decoration: none; font-weight: 600;">
                ← Назад к списку клубов
            </a>
        </div>
    </section>
@endsection

@section('page_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof lightGallery !== 'undefined') {
        const galleryElement = document.getElementById('clubGallery');
        if (galleryElement) {
            lightGallery(galleryElement, {
                plugins: [lgZoom, lgThumbnail, lgFullscreen],
                speed: 500,
                download: false,
                mobileSettings: {
                    controls: true,
                    showCloseIcon: true,
                    download: false
                },
                thumbWidth: 100,
                thumbHeight: 80,
                thumbMargin: 5,
                selector: 'a'
            });
        }
    }
});
</script>
@endsection
