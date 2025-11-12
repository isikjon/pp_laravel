document.addEventListener("DOMContentLoaded", function() {
    const cityModal = document.getElementById("cityModal");
    const cityChooseTriggers = document.querySelectorAll(".modal-cityChoose");
    const closeCityModal = document.querySelector(".closeCityModal");
    const cityItems = document.querySelectorAll(".cityItem");
    
    // Открытие модалки выбора города
    cityChooseTriggers.forEach(function(trigger) {
        trigger.style.cursor = "pointer";
        trigger.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            cityModal.style.display = "block";
            setTimeout(function() {
                cityModal.style.opacity = "1";
            }, 10);
        });
    });
    
    // Закрытие модалки
    if (closeCityModal) {
        closeCityModal.addEventListener("click", function() {
            cityModal.style.opacity = "0";
            setTimeout(function() {
                cityModal.style.display = "none";
            }, 300);
        });
    }
    
    // Закрытие по клику вне модалки
    window.addEventListener("click", function(e) {
        if (e.target === cityModal) {
            cityModal.style.opacity = "0";
            setTimeout(function() {
                cityModal.style.display = "none";
            }, 300);
        }
    });
    
    // Выбор города
    cityItems.forEach(function(item) {
        item.addEventListener("click", function() {
            const city = this.getAttribute("data-city");
            
            // Сохраняем в localStorage
            localStorage.setItem("selectedCity", city);
            localStorage.removeItem("selectedMetro");
            
            // Устанавливаем cookie через AJAX
            fetch('/api/city/set', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify({ city: city })
            })
            .then(response => response.json())
            .then(data => {
                // Закрываем модалку
                cityModal.style.opacity = "0";
                setTimeout(function() {
                    cityModal.style.display = "none";
                }, 300);
                
                // Перезагружаем страницу с параметром города
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('city', city);
                window.location.href = currentUrl.toString();
            })
            .catch(error => {
                console.error('Error setting city:', error);
                // В случае ошибки все равно перезагружаем
                const currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('city', city);
                window.location.href = currentUrl.toString();
            });
        });
    });
    
    // Функция для получения cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) {
            return parts.pop().split(';').shift();
        }
        return null;
    }
    
    // Инициализация отображения города и проверка URL параметра
    function initializeCityDisplay() {
        const urlParams = new URLSearchParams(window.location.search);
        const cityFromUrl = urlParams.get('city');
        
        // Приоритет: URL > localStorage > cookie > default
        let selectedCity = cityFromUrl || localStorage.getItem('selectedCity') || getCookie('selectedCity') || 'moscow';
        
        // Валидация города
        if (!['moscow', 'spb'].includes(selectedCity)) {
            selectedCity = 'moscow';
        }
        
        // Если в URL нет параметра city, добавляем его и перенаправляем
        // Но только для основных страниц сайта (не админка, не API)
        if (!cityFromUrl) {
            const pathname = window.location.pathname;
            
            // Пропускаем админку, API и другие служебные маршруты
            if (pathname.startsWith('/admin') || 
                pathname.startsWith('/api') || 
                pathname.startsWith('/livewire') ||
                pathname.startsWith('/_')) {
                // Для служебных страниц просто обновляем отображение
                const cityName = selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
                cityChooseTriggers.forEach(function(element) {
                    element.textContent = cityName;
                });
                return;
            }
            
            // Сохраняем в localStorage
            localStorage.setItem('selectedCity', selectedCity);
            
            // Добавляем параметр city в URL и перенаправляем
            const currentUrl = new URL(window.location.href);
            currentUrl.searchParams.set('city', selectedCity);
            
            // Используем replace вместо href, чтобы не добавлять запись в историю
            window.location.replace(currentUrl.toString());
            return; // Прерываем выполнение, так как произойдет редирект
        }
        
        // Сохраняем в localStorage если пришло из URL
        if (cityFromUrl) {
            localStorage.setItem('selectedCity', cityFromUrl);
        }
        
        const cityName = selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        // Обновляем отображение города везде
        cityChooseTriggers.forEach(function(element) {
            element.textContent = cityName;
        });
    }
    
    initializeCityDisplay();
});

