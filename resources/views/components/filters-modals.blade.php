{{-- Модальное окно с фильтрами --}}
<div id="modal__project1" class="modal">
    <div class="modal-content modal-content-1">
        <span class="close close1">
            <img src="{{ cached_asset('img/close.svg') }}" alt="">
        </span>
        <form class="formFilterModal" id="filtersForm">
            {{-- Верхняя основная секция фильтров --}}
            <div class="flexTop-formFilterModal">
                <div class="column-checkbox-formFilterModal">
                    <h5>Секс</h5>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="service[]" value="Секс_Классический">
                        Классический
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="service[]" value="Секс_Анальный">
                        Анальный
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="service[]" value="Секс_Групповой">
                        Групповой
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="service[]" value="Секс_Лесбийский">
                        Лесбийский
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="service[]" value="Секс_Виртуальный">
                        Виртуальный
                    </label>
                </div>
                <div class="column-checkbox-formFilterModal">
                    <h5>Место</h5>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="place[]" value="Квартира">
                        Квартира
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="place[]" value="Баня/Сауна">
                        Баня/Сауна
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="place[]" value="Гостиница">
                        Гостиница
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="place[]" value="Офис">
                        Офис
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="place[]" value="Апартаменты">
                        Апартаменты
                    </label>
                </div>
                <div class="column-checkbox-formFilterModal">
                    <h5>Окончание</h5>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="finish[]" value="В рот">
                        В рот
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="finish[]" value="На лицо">
                        На лицо
                    </label>
                    <label class="flex-column-checkbox-formFilterModal">
                        <input type="checkbox" name="finish[]" value="На грудь">
                        На грудь
                    </label>
                </div>
                <div class="column-checkbox-formFilterModal column-checkbox-formFilterModal__number">
                    <h5>Параметры</h5>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>Возраст</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" name="age_from" min="18" max="99" placeholder="18">
                            <span>до</span>
                            <input type="number" name="age_to" min="18" max="99" placeholder="99">
                        </div>
                    </div>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>Грудь</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" name="bust_from" min="1" max="9" placeholder="1">
                            <span>до</span>
                            <input type="number" name="bust_to" min="1" max="9" placeholder="9">
                        </div>
                    </div>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>Рост (см)</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" name="height_from" min="140" max="210" placeholder="140">
                            <span>до</span>
                            <input type="number" name="height_to" min="140" max="210" placeholder="210">
                        </div>
                    </div>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>Вес (кг)</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" name="weight_from" min="35" max="120" placeholder="35">
                            <span>до</span>
                            <input type="number" name="weight_to" min="35" max="120" placeholder="120">
                        </div>
                    </div>
                </div>
                <div class="column-checkbox-formFilterModal column-checkbox-formFilterModal__number">
                    <h5>Выезд</h5>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>1 час</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" name="price_1h_from" min="1500" max="150000" placeholder="1500">
                            <span>до</span>
                            <input type="number" name="price_1h_to" min="1500" max="150000" placeholder="150000">
                        </div>
                    </div>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>2 часа</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" name="price_2h_from" min="1500" max="150000" placeholder="1500">
                            <span>до</span>
                            <input type="number" name="price_2h_to" min="1500" max="150000" placeholder="150000">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Кнопка для раскрытия дополнительных фильтров --}}
            <div class="moreFilters" id="moreFiltersToggle" style="text-align: center; margin: 20px 0; cursor: pointer;">
                <p style="color: #7E1D32; font-weight: 600; display: inline-flex; align-items: center; gap: 10px;">
                    Дополнительные фильтры
                    <img src="{{ cached_asset('img/arrow-down.svg') }}" alt="" class="arrow-down" id="moreFiltersArrow" style="transition: transform 0.3s;">
                </p>
            </div>

            {{-- Дополнительная секция фильтров (скрыта по умолчанию) --}}
            <div id="additionalFilters" style="display: none;">
                <div class="flexBottom-formFilterModal">
                    {{-- Массаж --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Массаж</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Классический">
                            Классический
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Профессиональный">
                            Профессиональный
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Расслабляющий">
                            Расслабляющий
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Тайский">
                            Тайский
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Урологический">
                            Урологический
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Точечный">
                            Точечный
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Эротический">
                            Эротический
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Массаж_Ветка сакуры">
                            Ветка сакуры
                        </label>
                    </div>

                    {{-- Садо-мазо --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Садо-мазо</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Бандаж">
                            Бандаж
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Госпожа">
                            Госпожа
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Ролевые игры">
                            Ролевые игры
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Лёгкая доминация">
                            Лёгкая доминация
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Порка">
                            Порка
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Рабыня">
                            Рабыня
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Фетиш">
                            Фетиш
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Садо-мазо_Трамплинг">
                            Трамплинг
                        </label>
                    </div>

                    {{-- Лесби-шоу --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Лесби-шоу</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Лесби-шоу_Откровенное">
                            Откровенное
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Лесби-шоу_Лёгкое">
                            Лёгкое
                        </label>
                    </div>

                    {{-- Золотой дождь --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Золотой дождь</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Золотой дождь_Выдача">
                            Выдача
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Золотой дождь_Приём">
                            Приём
                        </label>
                    </div>

                    {{-- Дополнительно --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Дополнительно</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Дополнительно_Экскорт">
                            Экскорт
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Дополнительно_Фото/видео">
                            Фото/видео
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Дополнительно_Услуги семейной паре">
                            Услуги семейной паре
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Дополнительно_GFE">
                            GFE
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Дополнительно_Целуюсь">
                            Целуюсь
                        </label>
                    </div>

                    {{-- Стриптиз --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Стриптиз</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Стриптиз_Профи">
                            Профи
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Стриптиз_Не профи">
                            Не профи
                        </label>
                    </div>

                    {{-- Экстрим --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Экстрим</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Экстрим_Страпон">
                            Страпон
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Экстрим_Игрушки">
                            Игрушки
                        </label>
                    </div>

                    {{-- Фистинг --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Фистинг</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Фистинг_Анальный">
                            Анальный
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Фистинг_Классический">
                            Классический
                        </label>
                    </div>

                    {{-- Копро --}}
                    <div class="column-checkbox-formFilterModal">
                        <h5>Копро</h5>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Копро_Выдача">
                            Выдача
                        </label>
                        <label class="flex-column-checkbox-formFilterModal">
                            <input type="checkbox" name="service[]" value="Копро_Приём">
                            Приём
                        </label>
                    </div>
                </div>

                {{-- Доп параметры (Select поля из БД) --}}
                <div class="additionalParams" style="margin-top: 20px;">
                    <h5 style="text-align: center; margin-bottom: 15px;">Доп параметры</h5>
                    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 15px; padding: 0 20px;">
                        <div class="select-wrapper">
                            <label style="display: block; margin-bottom: 5px; font-size: 14px;">Цвет волос</label>
                            <select name="hair_color" id="hairColorSelect" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Не выбрано</option>
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <label style="display: block; margin-bottom: 5px; font-size: 14px;">Интим стрижка</label>
                            <select name="intimate_trim" id="intimateTrimSelect" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Не выбрано</option>
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <label style="display: block; margin-bottom: 5px; font-size: 14px;">Национальность</label>
                            <select name="nationality" id="nationalitySelect" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Не выбрано</option>
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <label style="display: block; margin-bottom: 5px; font-size: 14px;">Округ</label>
                            <select name="district" id="districtSelect" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Не выбрано</option>
                            </select>
                        </div>
                        <div class="select-wrapper">
                            <label style="display: block; margin-bottom: 5px; font-size: 14px;">Район</label>
                            <select name="region" id="regionSelect" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                                <option value="">Не выбрано</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Нижние чекбоксы --}}
            <div class="lastCheckBoxBottomModal">
                <label class="flex-column-checkbox-formFilterModal">
                    <input type="checkbox" name="verified" value="1">
                    Фото проверено
                </label>
                <label class="flex-column-checkbox-formFilterModal">
                    <input type="checkbox" name="has_video" value="1">
                    С видео
                </label>
                <label class="flex-column-checkbox-formFilterModal">
                    <input type="checkbox" name="has_reviews" value="1">
                    С отзывами
                </label>
            </div>
            
            <div class="btn-formFilterModal">
                <button class="btn-formFilterModal__btn" type="button">Применить</button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Загрузка опций для select полей из БД
    fetch('/api/filter-options')
        .then(response => response.json())
        .then(data => {
            // Цвет волос
            const hairColorSelect = document.getElementById('hairColorSelect');
            data.hair_colors.forEach(color => {
                const option = document.createElement('option');
                option.value = color;
                option.textContent = color;
                hairColorSelect.appendChild(option);
            });

            // Интим стрижка
            const intimateTrimSelect = document.getElementById('intimateTrimSelect');
            data.intimate_trims.forEach(trim => {
                const option = document.createElement('option');
                option.value = trim;
                option.textContent = trim;
                intimateTrimSelect.appendChild(option);
            });

            // Национальность
            const nationalitySelect = document.getElementById('nationalitySelect');
            data.nationalities.forEach(nationality => {
                const option = document.createElement('option');
                option.value = nationality;
                option.textContent = nationality;
                nationalitySelect.appendChild(option);
            });

            // Округ
            const districtSelect = document.getElementById('districtSelect');
            data.districts.forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Ошибка загрузки опций фильтров:', error));

    // Переключение дополнительных фильтров
    const moreFiltersToggle = document.getElementById('moreFiltersToggle');
    const additionalFilters = document.getElementById('additionalFilters');
    const moreFiltersArrow = document.getElementById('moreFiltersArrow');

    if (moreFiltersToggle && additionalFilters) {
        moreFiltersToggle.addEventListener('click', function() {
            if (additionalFilters.style.display === 'none' || additionalFilters.style.display === '') {
                additionalFilters.style.display = 'block';
                additionalFilters.style.opacity = '0';
                setTimeout(() => {
                    additionalFilters.style.opacity = '1';
                }, 10);
                moreFiltersArrow.style.transform = 'rotate(180deg)';
            } else {
                additionalFilters.style.opacity = '0';
                setTimeout(() => {
                    additionalFilters.style.display = 'none';
                }, 300);
                moreFiltersArrow.style.transform = 'rotate(0deg)';
            }
        });
    }
});
</script>
