// City Navigation Helper
// Обновляет ссылки навигации с параметром города

document.addEventListener('DOMContentLoaded', function() {
    function getCurrentCity() {
        // Получаем город из URL
        const urlParams = new URLSearchParams(window.location.search);
        const cityFromUrl = urlParams.get('city');
        if (cityFromUrl) {
            return cityFromUrl;
        }
        
        // Получаем город из localStorage
        const cityFromStorage = localStorage.getItem('selectedCity');
        if (cityFromStorage) {
            return cityFromStorage;
        }
        
        // Получаем город из cookie
        const cookies = document.cookie.split(';');
        for (let cookie of cookies) {
            const [name, value] = cookie.trim().split('=');
            if (name === 'selectedCity') {
                return decodeURIComponent(value);
            }
        }
        
        return 'moscow'; // дефолтное значение
    }
    
    function updateNavigationLinks() {
        const currentCity = getCurrentCity();
        
        if (currentCity && currentCity !== 'moscow') {
            // Обновляем ссылки в навигации
            const links = document.querySelectorAll('a[href*="/masseuse"], a[href*="/salons"], a[href*="/stripclubs"], a[href*="/intim-map"], a[href="/"]');
            
            links.forEach(link => {
                const href = link.getAttribute('href');
                if (!href) return;
                
                // Проверяем, нет ли уже параметра city в ссылке
                if (href.includes('?')) {
                    const [baseUrl, queryString] = href.split('?');
                    const params = new URLSearchParams(queryString);
                    if (!params.has('city')) {
                        params.set('city', currentCity);
                        link.setAttribute('href', baseUrl + '?' + params.toString());
                    }
                } else {
                    // Добавляем параметр city
                    const separator = href.includes('#') ? '&' : '?';
                    if (!href.includes('city=')) {
                        link.setAttribute('href', href + separator + 'city=' + currentCity);
                    }
                }
            });
        }
    }
    
    // Устанавливаем cookie при загрузке страницы
    function ensureCityInCookie() {
        const currentCity = getCurrentCity();
        
        // Устанавливаем cookie
        const expiryDate = new Date();
        expiryDate.setFullYear(expiryDate.getFullYear() + 1);
        document.cookie = `selectedCity=${currentCity}; expires=${expiryDate.toUTCString()}; path=/; SameSite=Lax`;
    }
    
    // Инициализация
    updateNavigationLinks();
    ensureCityInCookie();
    
    // Обновляем ссылки при изменении города
    const cityItems = document.querySelectorAll('.cityItem');
    cityItems.forEach(item => {
        item.addEventListener('click', function() {
            setTimeout(updateNavigationLinks, 100);
        });
    });
});

