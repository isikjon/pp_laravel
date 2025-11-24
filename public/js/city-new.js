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
    
    // Выбор города - просто сохраняем в localStorage перед переходом
    cityItems.forEach(function(item) {
        item.addEventListener("click", function(e) {
            const clickedCity = this.getAttribute("data-city");
            if (clickedCity) {
                localStorage.setItem("selectedCity", clickedCity);
                localStorage.removeItem("selectedMetro");
            }
        });
    });
    
    // Город уже установлен сервером в blade шаблоне через {{ $cityName }}
    // JavaScript только управляет модальным окном
});

