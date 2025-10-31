$(document).ready(function() {
    console.log('Favorites.js loaded!');
    
    function getFavorites() {
        const favorites = localStorage.getItem('favorites');
        return favorites ? JSON.parse(favorites) : [];
    }
    
    function saveFavorites(favorites) {
        localStorage.setItem('favorites', JSON.stringify(favorites));
        updateCounter();
    }
    
    function updateCounter() {
        const count = getFavorites().length;
        $('.favorites-counter').text(count);
        console.log('Счетчик обновлен:', count);
    }
    
    function addToFavorites(girlId) {
        const favorites = getFavorites();
        if (!favorites.includes(girlId)) {
            favorites.push(girlId);
            saveFavorites(favorites);
            return true;
        }
        return false;
    }
    
    function removeFromFavorites(girlId) {
        let favorites = getFavorites();
        const index = favorites.indexOf(girlId);
        if (index > -1) {
            favorites.splice(index, 1);
            saveFavorites(favorites);
            return true;
        }
        return false;
    }
    
    function isInFavorites(girlId) {
        return getFavorites().includes(girlId);
    }
    
    function showPopup(message, type = 'success') {
        const existingPopup = $('.favorite-popup');
        if (existingPopup.length) {
            existingPopup.remove();
        }
        
        const icon = type === 'success' 
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#FF0042"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#ccc"/></svg>';
        
        const popup = $(`
            <div class="favorite-popup favorite-popup--${type}">
                <div class="favorite-popup__content">
                    <div class="favorite-popup__icon">${icon}</div>
                    <div class="favorite-popup__message">${message}</div>
                </div>
            </div>
        `);
        
        $('body').append(popup);
        
        setTimeout(() => {
            popup.addClass('favorite-popup--show');
        }, 10);
        
        setTimeout(() => {
            popup.removeClass('favorite-popup--show');
            setTimeout(() => {
                popup.remove();
            }, 300);
        }, 2500);
    }
    
    function updateFavoriteIcon(girlId, isFavorite) {
        const cards = $(`.girlCard[data-girl-id="${girlId}"]`);
        
        cards.each(function() {
            const icon = $(this).find('.name-girlCard a[href="#!"] img');
            if (isFavorite) {
                icon.attr('src', '/img/flexBottomHeader-8-2.svg');
                icon.closest('a').addClass('is-favorite');
            } else {
                icon.attr('src', '/img/flexBottomHeader-8.svg');
                icon.closest('a').removeClass('is-favorite');
            }
        });
    }
    
    function initFavorites() {
        const favorites = getFavorites();
        console.log('Избранных девушек:', favorites.length);
        
        favorites.forEach(girlId => {
            updateFavoriteIcon(girlId, true);
        });
    }
    
    $(document).on('click', '.name-girlCard a[href="#!"]', function(e) {
        e.preventDefault();
        
        console.log('=== КЛИК ПО СЕРДЕЧКУ ===');
        
        let girlId = $(this).attr('data-girl-id');
        console.log('ID из data-атрибута:', girlId);
        
        if (!girlId) {
            const card = $(this).closest('.girlCard');
            girlId = card.attr('data-girl-id');
            console.log('ID из карточки:', girlId);
        }
        
        if (!girlId) {
            const card = $(this).closest('.girlCard');
            let girlLink = card.find('a[href*="/girl/"]').first().attr('href');
            
            if (!girlLink) {
                girlLink = card.find('.photoGirl').attr('href');
            }
            
            if (girlLink) {
                girlId = girlLink.split('/').pop();
                console.log('ID из ссылки:', girlId);
            }
        }
        
        if (!girlId) {
            console.error('Не удалось найти ID девушки!');
            return;
        }
        
        const card = $(this).closest('.girlCard');
        const girlName = card.find('.name-girlCard p').first().text().trim();
        
        console.log('Имя девушки:', girlName);
        
        if (isInFavorites(girlId)) {
            console.log('Удаляем из избранного');
            removeFromFavorites(girlId);
            updateFavoriteIcon(girlId, false);
            showPopup(`${girlName} удалена из избранного`, 'remove');
            $(document).trigger('favoriteRemoved', [girlId]);
        } else {
            console.log('Добавляем в избранное');
            addToFavorites(girlId);
            updateFavoriteIcon(girlId, true);
            showPopup(`${girlName} добавлена в избранное`, 'success');
        }
    });
    
    initFavorites();
    updateCounter();
    
    window.updateFavoritesAfterLoad = function() {
        setTimeout(() => {
            initFavorites();
            updateCounter();
        }, 100);
    };
});

