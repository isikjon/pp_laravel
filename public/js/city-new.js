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
        item.addEventListener("click", function() {
            const city = this.getAttribute("data-city");
            
            localStorage.setItem("selectedCity", city);
            localStorage.removeItem("selectedMetro");
            
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
                cityModal.style.opacity = "0";
                setTimeout(function() {
                    cityModal.style.display = "none";
                }, 300);
                
                if (data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.reload();
                }
            })
            .catch(error => {
                console.error('Error setting city:', error);
                cityModal.style.opacity = "0";
                setTimeout(function() {
                    cityModal.style.display = "none";
                }, 300);
                window.location.reload();
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

