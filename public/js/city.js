document.addEventListener('DOMContentLoaded', function() {
    const cityModal = document.getElementById('cityModal');
    const cityTriggers = document.querySelectorAll('.modal-cityChoose');
    const closeCityModal = document.querySelector('.closeCityModal');
    const cityItems = document.querySelectorAll('.cityItem');

    function getCurrentCity() {
        return localStorage.getItem('selectedCity') || 'moscow';
    }

    function getCityName(cityCode) {
        return cityCode === 'spb' ? 'Санкт-Петербург' : 'Москва';
    }

    function updateCityDisplay() {
        const urlParams = new URLSearchParams(window.location.search);
        const cityFromUrl = urlParams.get('city');
        
        if (cityFromUrl) {
            localStorage.setItem('selectedCity', cityFromUrl);
        }
        
        const currentCity = getCurrentCity();
        const cityName = getCityName(currentCity);
        
        cityTriggers.forEach(trigger => {
            trigger.textContent = cityName;
        });
    }

    cityTriggers.forEach(trigger => {
        trigger.style.cursor = 'pointer';
        trigger.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            cityModal.style.display = 'block';
            setTimeout(() => {
                cityModal.style.opacity = '1';
            }, 10);
        });
    });

    if (closeCityModal) {
        closeCityModal.addEventListener('click', function() {
            cityModal.style.opacity = '0';
            setTimeout(() => {
                cityModal.style.display = 'none';
            }, 300);
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === cityModal) {
            cityModal.style.opacity = '0';
            setTimeout(() => {
                cityModal.style.display = 'none';
            }, 300);
        }
    });

    cityItems.forEach(item => {
        item.addEventListener('click', function() {
            const selectedCity = this.getAttribute('data-city');
            
            localStorage.setItem('selectedCity', selectedCity);
            localStorage.removeItem('selectedMetro');
            
            cityModal.style.opacity = '0';
            setTimeout(() => {
                cityModal.style.display = 'none';
            }, 300);
            
            window.location.href = `/?city=${selectedCity}`;
        });
    });

    updateCityDisplay();
});

