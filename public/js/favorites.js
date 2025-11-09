document.addEventListener('DOMContentLoaded', function () {
    console.log('Favorites.js loaded!');

    function getFavorites() {
        try {
            const favorites = localStorage.getItem('favorites');
            return favorites ? JSON.parse(favorites) : [];
        } catch (error) {
            console.error('Не удалось прочитать favorites из localStorage', error);
            return [];
        }
    }

    function saveFavorites(favorites) {
        try {
            localStorage.setItem('favorites', JSON.stringify(favorites));
            updateCounter();
        } catch (error) {
            console.error('Не удалось сохранить favorites в localStorage', error);
        }
    }

    function updateCounter() {
        const count = getFavorites().length;
        document.querySelectorAll('.favorites-counter').forEach(function (el) {
            el.textContent = count;
        });
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
        const favorites = getFavorites();
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

    function showPopup(message, type) {
        const existingPopup = document.querySelector('.favorite-popup');
        if (existingPopup) {
            existingPopup.remove();
        }

        const iconSvg = type === 'success'
            ? '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#FF0042"/></svg>'
            : '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z" fill="#ccc"/></svg>';

        const popup = document.createElement('div');
        popup.className = `favorite-popup favorite-popup--${type}`;
        popup.innerHTML = `
            <div class="favorite-popup__content">
                <div class="favorite-popup__icon">${iconSvg}</div>
                <div class="favorite-popup__message">${message}</div>
            </div>
        `;

        document.body.appendChild(popup);

        requestAnimationFrame(function () {
            popup.classList.add('favorite-popup--show');
        });

        setTimeout(function () {
            popup.classList.remove('favorite-popup--show');
            setTimeout(function () { popup.remove(); }, 300);
        }, 2500);
    }

    function updateFavoriteIcon(girlId, isFavorite) {
        document.querySelectorAll(`.girlCard[data-girl-id="${girlId}"]`).forEach(function (cardEl) {
            const girlNameElement = cardEl.querySelector('.name-girlCard p');
            const girlName = girlNameElement ? girlNameElement.textContent.trim() : '';
            const iconLink = cardEl.querySelector('.name-girlCard .favorite-toggle');
            if (!iconLink) {
                return;
            }
            if (isFavorite) {
                iconLink.classList.add('is-active');
                iconLink.setAttribute('aria-label', `Удалить из избранного ${girlName}`);
            } else {
                iconLink.classList.remove('is-active');
                iconLink.setAttribute('aria-label', `Добавить в избранное ${girlName}`);
            }
        });
    }

    function initFavorites() {
        const favorites = getFavorites();
        console.log('Избранных девушек:', favorites.length);
        favorites.forEach(function (girlId) {
            updateFavoriteIcon(girlId, true);
        });
    }

    function extractGirlIdFromCard(card) {
        if (!card) {
            return null;
        }
        const girlId = card.getAttribute('data-girl-id');
        if (girlId) {
            return girlId;
        }
        const link = card.querySelector('a[href*="/girl/"]');
        if (link) {
            return link.getAttribute('href').split('/').pop();
        }
        return null;
    }

    document.addEventListener('click', function (event) {
        const toggle = event.target.closest('.favorite-toggle');
        if (!toggle) {
            return;
        }
        event.preventDefault();

        let girlId = toggle.getAttribute('data-girl-id');
        if (!girlId) {
            girlId = extractGirlIdFromCard(toggle.closest('.girlCard'));
        }

        if (!girlId) {
            console.error('Не удалось найти ID девушки!');
            return;
        }

        const card = toggle.closest('.girlCard');
        const girlNameEl = card ? card.querySelector('.name-girlCard p') : null;
        const girlName = girlNameEl ? girlNameEl.textContent.trim() : '';

        if (isInFavorites(girlId)) {
            console.log('Удаляем из избранного');
            removeFromFavorites(girlId);
            updateFavoriteIcon(girlId, false);
            showPopup(`${girlName} удалена из избранного`, 'remove');
            document.dispatchEvent(new CustomEvent('favoriteRemoved', { detail: girlId }));
        } else {
            console.log('Добавляем в избранное');
            addToFavorites(girlId);
            updateFavoriteIcon(girlId, true);
            showPopup(`${girlName} добавлена в избранное`, 'success');
        }
    });

    initFavorites();
    updateCounter();

    window.updateFavoritesAfterLoad = function () {
        requestAnimationFrame(function () {
            initFavorites();
            updateCounter();
        });
    };
});

