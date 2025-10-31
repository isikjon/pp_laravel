{{-- Модальное окно с фильтрами --}}
<div id="modal__project1" class="modal">
    <div class="modal-content modal-content-1">
        <span class="close close1">
            <img src="{{ asset('img/close.svg') }}" alt="">
        </span>
        <form class="formFilterModal" id="filtersForm">
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
                            <input type="number" name="price_from" min="1500" max="150000" placeholder="1500">
                            <span>до</span>
                            <input type="number" name="price_to" min="1500" max="150000" placeholder="150000">
                        </div>
                    </div>
                    <div class="block-column-checkbox-formFilterModal__number">
                        <span>2 часа</span>
                        <div class="flex-block-column-checkbox-formFilterModal__number">
                            <span>от</span>
                            <input type="number" min="1500" max="150000" placeholder="1500">
                            <span>до</span>
                            <input type="number" min="1500" max="150000" placeholder="150000">
                        </div>
                    </div>
                </div>
            </div>
            
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

