document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.querySelector(".moreInfo");
    const hiddenBlock = document.querySelector(".flexTop-formFilterModal__none");
    const arrow = toggleBtn.querySelector(".arrow-down");
    const text = toggleBtn.querySelector("p");

    toggleBtn.addEventListener("click", () => {
        const isVisible = hiddenBlock.style.display === "flex";

        // Переключаем отображение блока
        hiddenBlock.style.display = isVisible ? "none" : "flex";

        // Поворачиваем стрелку
        arrow.classList.toggle("rotated", !isVisible);

        // Меняем текст
        text.textContent = isVisible ? "Расширенный поиск" : "Скрыть";
    });
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