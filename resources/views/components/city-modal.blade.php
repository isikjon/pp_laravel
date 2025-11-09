<div id="cityModal" class="modal" style="display: none;">
    <div class="modal-content modal-content-city">
        <span class="close closeCityModal">
            <img src="{{ cached_asset('img/close.svg') }}" alt="">
        </span>
        <div class="cityModalContent">
            <h3 style="text-align: center; margin-bottom: 30px; font-size: 24px; color: #7E1D32;">Выберите город</h3>
            <div class="cityList">
                <div class="cityItem" data-city="moscow">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 25 25" fill="none">
                        <path d="M4.36489 8.96968C6.33489 0.309678 19.1649 0.319678 21.1249 8.97968C22.2749 14.0597 19.1149 18.3597 16.3449 21.0197C14.3349 22.9597 11.1549 22.9597 9.13489 21.0197C6.37489 18.3597 3.21489 14.0497 4.36489 8.96968Z" fill="#7E1D32"/>
                        <path d="M12.7449 13.9097C14.468 13.9097 15.8649 12.5128 15.8649 10.7897C15.8649 9.06655 14.468 7.66968 12.7449 7.66968C11.0217 7.66968 9.62488 9.06655 9.62488 10.7897C9.62488 12.5128 11.0217 13.9097 12.7449 13.9097Z" fill="white"/>
                    </svg>
                    <span>Москва</span>
                </div>
                <div class="cityItem" data-city="spb">
                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 25 25" fill="none">
                        <path d="M4.36489 8.96968C6.33489 0.309678 19.1649 0.319678 21.1249 8.97968C22.2749 14.0597 19.1149 18.3597 16.3449 21.0197C14.3349 22.9597 11.1549 22.9597 9.13489 21.0197C6.37489 18.3597 3.21489 14.0497 4.36489 8.96968Z" fill="#7E1D32"/>
                        <path d="M12.7449 13.9097C14.468 13.9097 15.8649 12.5128 15.8649 10.7897C15.8649 9.06655 14.468 7.66968 12.7449 7.66968C11.0217 7.66968 9.62488 9.06655 9.62488 10.7897C9.62488 12.5128 11.0217 13.9097 12.7449 13.9097Z" fill="white"/>
                    </svg>
                    <span>Санкт-Петербург</span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.modal-content-city {
    max-width: 500px;
    padding: 40px;
}

.cityList {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.cityItem {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 20px;
    border: 2px solid #f0f0f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s;
}

.cityItem:hover {
    border-color: #7E1D32;
    background-color: #fff5f7;
}

.cityItem span {
    font-size: 18px;
    font-weight: 500;
    color: #333;
}

.cityItem svg {
    flex-shrink: 0;
}
</style>

