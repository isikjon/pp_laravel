@extends('layouts.app')

@section('title', 'Интим карта ' . ($cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы'))

@push('styles')
<style>
.markersContact {
    display: flex;
    gap: 20px;
    margin: 30px 0;
    flex-wrap: wrap;
}

.block-markersContact {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    user-select: none;
}

.block-markersContact input[type="checkbox"] {
    display: none;
}

.block-markersContact svg {
    width: 20px;
    height: 20px;
}

.block-markersContact p {
    margin: 0;
    font-size: 16px;
    font-weight: 500;
}

.mapContact {
    width: 100%;
    height: 630px;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    margin-bottom: 30px;
}

.map-inner {
    width: 100%;
    height: 100%;
}

.bannerBottomTG {
    display: block;
    margin: 40px 0;
}

.bannerBottomTG img {
    width: 100%;
    border-radius: 15px;
}

@media (max-width: 768px) {
    .mapContact {
        height: 500px;
    }
}
</style>
@endpush

@section('content')
<section class="mainContent">
    <div class="container">
        <div class="textSection">
            <h1>
                Интим карта {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурга' : 'Москвы' }}
            </h1>
            <p>
                Интим карта проститутки – удобный сервис для желающих быстро найти путану с учетом своего местоположения. Теперь не приходится тратить массу времени и нервов на подходящие поиски. На нашем сайте можете узнать, где стоят проститутки в {{ $cityName === 'Санкт-Петербург' ? 'Санкт-Петербурге' : 'Москве' }}, подходящие под ваши личные параметры. Широкий выбор соблазнительных девушек разных возрастов, параметров и ценовых категорий, чтобы подарить себе истинное наслаждение уже в ближайшее время.
                <br><br>
                Стремитесь подарить себе воплощение смелых фантазий, насладиться близостью с подходящей путаной или даже двумя? Есть желание разнообразить досуг и воплотить смелые мечты? Отличная идея – не стоит себе отказывать в наслаждении. Но делать это следует рационально – без ненужных трат на поиск, воспользуйтесь проверенными решениями нашего сайта.
            </p>
        </div>
        
        <div class="markersContact">
            <div class="block-markersContact" data-type="1">
                <input type="checkbox" id="map01" checked>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M7.50033 18.3334H12.5003C16.667 18.3334 18.3337 16.6667 18.3337 12.5V7.50002C18.3337 3.33335 16.667 1.66669 12.5003 1.66669H7.50033C3.33366 1.66669 1.66699 3.33335 1.66699 7.50002V12.5C1.66699 16.6667 3.33366 18.3334 7.50033 18.3334Z" stroke="#DD2222" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.45801 10L8.81634 12.3583L13.5413 7.64166" stroke="#DD2222" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <label for="map01" style="cursor: pointer; color: #D22">
                    Индивидуалки
                </label>
            </div>
            <div class="block-markersContact" data-type="2">
                <input type="checkbox" id="map02" checked>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M7.50033 18.3334H12.5003C16.667 18.3334 18.3337 16.6667 18.3337 12.5V7.50002C18.3337 3.33335 16.667 1.66669 12.5003 1.66669H7.50033C3.33366 1.66669 1.66699 3.33335 1.66699 7.50002V12.5C1.66699 16.6667 3.33366 18.3334 7.50033 18.3334Z" stroke="#004FC3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.45801 10L8.81634 12.3583L13.5413 7.64166" stroke="#004FC3" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <label for="map02" style="cursor: pointer; color: #004FC3">
                    Интим-салоны
                </label>
            </div>
            <div class="block-markersContact" data-type="3">
                <input type="checkbox" id="map03" checked>
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M7.50033 18.3334H12.5003C16.667 18.3334 18.3337 16.6667 18.3337 12.5V7.50002C18.3337 3.33335 16.667 1.66669 12.5003 1.66669H7.50033C3.33366 1.66669 1.66699 3.33335 1.66699 7.50002V12.5C1.66699 16.6667 3.33366 18.3334 7.50033 18.3334Z" stroke="#E101FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M6.45801 10L8.81634 12.3583L13.5413 7.64166" stroke="#E101FF" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <label for="map03" style="cursor: pointer; color: #E101FF">
                    Стрип-клубы
                </label>
            </div>
        </div>
        
        <div class="mapContact">
            <div id="map" class="map-inner" data-city="{{ $cityName }}"></div>
        </div>
        
        <a href="#!" target="_blank" class="bannerBottomTG">
            <img src="{{ asset('img/bannerTG.png') }}" alt="">
        </a>
    </div>
</section>
@endsection

@section('page_scripts')
<script src="https://api-maps.yandex.ru/2.1/?apikey=YOUR_API_KEY&lang=ru_RU" type="text/javascript"></script>
<script>
let myMap;
const selectedCity = '{{ $selectedCity }}';
const cityName = '{{ $cityName }}';

ymaps.ready(init);

function init() {
    const mapCenter = selectedCity === 'spb' 
        ? [59.9311, 30.3609]
        : [55.7558, 37.6173];
    
    myMap = new ymaps.Map("map", {
        center: mapCenter,
        zoom: 11,
        controls: ['zoomControl']
    });
    
    loadMapMarkers();
}

function getSelectedTypes() {
    const types = [];
    document.querySelectorAll('.block-markersContact input[type="checkbox"]:checked').forEach(checkbox => {
        types.push(checkbox.closest('.block-markersContact').getAttribute('data-type'));
    });
    return types;
}

async function loadMapMarkers() {
    try {
        const types = getSelectedTypes();
        const response = await fetch(`{{ route('intimmap.data') }}?city=${selectedCity}&types=${types.join(',')}`);
        const data = await response.json();
        
        myMap.geoObjects.removeAll();
        
        if (data.girls && data.girls.length > 0) {
            addGirlsToMap(data.girls);
        }
        
        if (data.salons && data.salons.length > 0) {
            addSalonsToMap(data.salons);
        }
        
        if (data.clubs && data.clubs.length > 0) {
            addClubsToMap(data.clubs);
        }
    } catch (error) {
        console.error('Ошибка загрузки данных карты:', error);
    }
}

function addGirlsToMap(girls) {
    const clusterer = new ymaps.Clusterer({
        preset: 'islands#invertedRedClusterIcons',
        clusterDisableClickZoom: false,
        clusterOpenBalloonOnClick: true,
        clusterBalloonContentLayout: 'cluster#balloonCarousel',
        clusterBalloonPanelMaxMapArea: 0,
        clusterBalloonContentLayoutWidth: 200,
        clusterBalloonContentLayoutHeight: 130,
        clusterBalloonPagerSize: 5
    });
    
    const placemarks = girls.map(girl => {
        if (!girl.coordinates) return null;
        
        const coords = parseCoordinates(girl.coordinates);
        if (!coords) return null;
        
        return new ymaps.Placemark(coords, {
            balloonContentHeader: girl.name,
            balloonContentBody: `<a href="/girl/${girl.id}" target="_blank" style="color: #7E1D32; text-decoration: none;">Перейти к анкете</a>`,
            hintContent: girl.name
        }, {
            preset: 'islands#redIcon'
        });
    }).filter(p => p !== null);
    
    clusterer.add(placemarks);
    myMap.geoObjects.add(clusterer);
}

function addSalonsToMap(salons) {
    const clusterer = new ymaps.Clusterer({
        preset: 'islands#invertedBlueClusterIcons',
        clusterDisableClickZoom: false,
        clusterOpenBalloonOnClick: true
    });
    
    const placemarks = salons.map(salon => {
        if (!salon.coordinates) return null;
        
        const coords = parseCoordinates(salon.coordinates);
        if (!coords) return null;
        
        return new ymaps.Placemark(coords, {
            balloonContentHeader: salon.name,
            balloonContentBody: `<a href="/salons/${salon.id}" target="_blank" style="color: #004FC3; text-decoration: none;">Перейти к салону</a>`,
            hintContent: salon.name
        }, {
            preset: 'islands#blueIcon'
        });
    }).filter(p => p !== null);
    
    clusterer.add(placemarks);
    myMap.geoObjects.add(clusterer);
}

function addClubsToMap(clubs) {
    const placemarks = clubs.map(club => {
        if (!club.coordinates) return null;
        
        const coords = parseCoordinates(club.coordinates);
        if (!coords) return null;
        
        return new ymaps.Placemark(coords, {
            balloonContentHeader: club.name,
            balloonContentBody: `<a href="/stripclubs/${club.id}" target="_blank" style="color: #E101FF; text-decoration: none;">Перейти к клубу</a>`,
            hintContent: club.name
        }, {
            preset: 'islands#violetIcon'
        });
    }).filter(p => p !== null);
    
    placemarks.forEach(placemark => {
        myMap.geoObjects.add(placemark);
    });
}

function parseCoordinates(coords) {
    if (Array.isArray(coords)) {
        return coords;
    }
    
    if (typeof coords === 'string') {
        try {
            const parsed = JSON.parse(coords);
            if (Array.isArray(parsed) && parsed.length === 2) {
                return [parseFloat(parsed[0]), parseFloat(parsed[1])];
            }
        } catch (e) {
            const parts = coords.split(',');
            if (parts.length === 2) {
                return [parseFloat(parts[0].trim()), parseFloat(parts[1].trim())];
            }
        }
    }
    
    return null;
}

document.querySelectorAll('.block-markersContact').forEach(block => {
    block.addEventListener('click', function() {
        const checkbox = this.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        
        loadMapMarkers();
    });
});
</script>
@endsection

