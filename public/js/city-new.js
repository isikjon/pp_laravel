document.addEventListener("DOMContentLoaded", function() {
    const cityModal = document.getElementById("cityModal");
    const cityChooseTriggers = document.querySelectorAll(".modal-cityChoose");
    const cityChooseElements = document.querySelectorAll(".cityChoose");
    const closeCityModal = document.querySelector(".closeCityModal");
    const cityItems = document.querySelectorAll(".cityItem");
    
    // Открытие модалки при клике на cityChoose
    cityChooseElements.forEach(function(element) {
        element.addEventListener("click", function(e) {
            e.preventDefault();
            e.stopPropagation();
            if (cityModal) {
                cityModal.style.display = "block";
                cityModal.style.opacity = "0";
                setTimeout(function() {
                    cityModal.style.opacity = "1";
                }, 10);
            }
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
        item.style.cursor = 'pointer';
        
        const handleCityClick = function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const clickedCity = item.getAttribute("data-city");
            
            if (!clickedCity) {
                console.error('City not found in data-city attribute');
                return;
            }
            
            console.log('City selected:', clickedCity);
            
            localStorage.setItem("selectedCity", clickedCity);
            localStorage.removeItem("selectedMetro");
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            
            if (!csrfToken) {
                console.error('CSRF token not found');
                alert('Ошибка: CSRF токен не найден. Перезагрузите страницу.');
                return;
            }
            
            console.log('Sending city request:', clickedCity, 'CSRF:', csrfToken.substring(0, 10) + '...');
            
            fetch('/api/city/set', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin',
                body: JSON.stringify({ city: clickedCity })
            })
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    return response.text().then(text => {
                        console.error('Response error:', text);
                        throw new Error('Network response was not ok: ' + response.status + ' - ' + text);
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                cityModal.style.opacity = "0";
                setTimeout(function() {
                    cityModal.style.display = "none";
                }, 100);
                
                if (data.redirect_url) {
                    console.log('Redirecting to:', data.redirect_url);
                    setTimeout(function() {
                        window.location.href = data.redirect_url;
                    }, 150);
                } else {
                    console.log('Reloading page');
                    setTimeout(function() {
                        window.location.reload();
                    }, 150);
                }
            })
            .catch(error => {
                console.error('Error setting city:', error);
                alert('Ошибка при выборе города: ' + error.message);
                cityModal.style.opacity = "0";
                setTimeout(function() {
                    cityModal.style.display = "none";
                }, 300);
            });
        };
        
        item.addEventListener("click", handleCityClick);
        
        const span = item.querySelector("span");
        const svg = item.querySelector("svg");
        
        if (span) {
            span.style.pointerEvents = 'none';
            span.addEventListener("click", handleCityClick);
        }
        
        if (svg) {
            svg.style.pointerEvents = 'none';
            svg.addEventListener("click", handleCityClick);
        }
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
    
    function initializeCityDisplay() {
        const host = window.location.hostname;
        const subdomain = host.split('.')[0];
        
        let selectedCity;
        
        if (subdomain === 'spb') {
            selectedCity = 'spb';
        } else {
            selectedCity = localStorage.getItem('selectedCity') || getCookie('selectedCity') || 'moscow';
        }
        
        if (!['moscow', 'spb'].includes(selectedCity)) {
            selectedCity = 'moscow';
        }
        
        localStorage.setItem('selectedCity', selectedCity);
        
        const cityName = selectedCity === 'spb' ? 'Санкт-Петербург' : 'Москва';
        
        cityChooseTriggers.forEach(function(element) {
            element.textContent = cityName;
        });
    }
    
    initializeCityDisplay();
});

