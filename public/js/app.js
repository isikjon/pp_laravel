document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.querySelector(".moreInfo");
    
    if (toggleBtn) {
    const hiddenBlock = document.querySelector(".flexTop-formFilterModal__none");
    const arrow = toggleBtn.querySelector(".arrow-down");
    const text = toggleBtn.querySelector("p");

        if (hiddenBlock && arrow && text) {
    toggleBtn.addEventListener("click", () => {
        const isVisible = hiddenBlock.style.display === "flex";

        hiddenBlock.style.display = isVisible ? "none" : "flex";

        arrow.classList.toggle("rotated", !isVisible);

        text.textContent = isVisible ? "Расширенный поиск" : "Скрыть";
    });
        }
    }
});



var jsTriggers = document.querySelectorAll('.js-tab-trigger'),
    jsContents = document.querySelectorAll('.js-tab-content');

jsTriggers.forEach(function(trigger) {
    trigger.addEventListener('click', function() {
        var id = this.getAttribute('data-tab'),
            content = document.querySelector('.js-tab-content[data-tab="'+id+'"]'),
            activeTrigger = document.querySelector('.js-tab-trigger.active'),
            activeContent = document.querySelector('.js-tab-content.active');

        activeTrigger.classList.remove('active');
        trigger.classList.add('active');

        activeContent.classList.remove('active');
        content.classList.add('active');
    });
});
