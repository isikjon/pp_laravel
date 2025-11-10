document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menu__toggle');
    const cityChooseTriggers = document.querySelectorAll('.cityChoose');
    const metroTriggers = document.querySelectorAll('[data-metro-trigger]');
    const filtersBtns = document.querySelectorAll('.filtersBtn');
    
    function closeMenu() {
        if (menuToggle && menuToggle.checked) {
            menuToggle.checked = false;
        }
    }
    
    cityChooseTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', closeMenu);
    });
    
    metroTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', closeMenu);
    });
    
    filtersBtns.forEach(function(btn) {
        btn.addEventListener('click', closeMenu);
    });
});

