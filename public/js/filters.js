document.addEventListener('DOMContentLoaded', function () {
    let currentPage = typeof window.__CURRENT_PAGE === 'number' ? window.__CURRENT_PAGE : 1;
    let isLoading = false;
    let deferredGirls = Array.isArray(window.__DEFERRED_GIRLS) ? window.__DEFERRED_GIRLS : [];
    let hasDeferredGirls = deferredGirls.length > 0;
    let hasMorePages = !!window.__HAS_MORE_PAGES;
    let hasMore = hasMorePages;
    let lastAppliedFilters = {};
    
    const mobileMediaQuery = typeof window.matchMedia === 'function' ? window.matchMedia('(max-width: 768px)') : null;
    let mobileVisibleCount = mobileMediaQuery && mobileMediaQuery.matches ? 1 : Infinity;
    let imageObserver = null;

    function loadDeferredImage(img) {
        if (!img || img.dataset.deferredLoaded === 'true') return;
        const src = img.getAttribute('data-src');
        if (src) {
            img.src = src;
            img.dataset.deferredLoaded = 'true';
            img.removeAttribute('data-src');
        }
    }

    function observeDeferredImages(scope) {
        const images = (scope && scope.querySelectorAll ? scope : document).querySelectorAll('.deferred-image');
        if (!images.length) return;

        if ('IntersectionObserver' in window) {
            if (!imageObserver) {
                imageObserver = new IntersectionObserver(function (entries) {
                    entries.forEach(function (entry) {
                        if (!entry.isIntersecting) return;
                        const img = entry.target;
                        imageObserver.unobserve(img);
                        delete img.dataset.deferredObserving;
                        loadDeferredImage(img);
                    });
                }, { rootMargin: '200px 0px', threshold: 0.01 });
            }
            images.forEach(function (img) {
                if (img.dataset.deferredLoaded !== 'true' && img.dataset.deferredObserving !== 'true') {
                    img.dataset.deferredObserving = 'true';
                    imageObserver.observe(img);
                }
            });
        } else {
            images.forEach(loadDeferredImage);
        }
    }

    function scrollToGirlsSection() {
        const section = document.querySelector('.girlsSection');
        if (section && typeof section.scrollIntoView === 'function') {
            section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }

    function applyMobileVisibility(container) {
        if (!container) return;
        const cards = container.querySelectorAll('.girlCard');

        if (!mobileMediaQuery || !mobileMediaQuery.matches) {
            mobileVisibleCount = Infinity;
            cards.forEach(function (card) {
                card.classList.remove('is-mobile-hidden', 'mobile-hidden-default');
            });
            return;
        }

        const visibleCards = Array.from(cards).filter(function (card) {
            return !card.classList.contains('is-mobile-hidden') && !card.classList.contains('mobile-hidden-default');
        }).length;

        if (visibleCards === 0 && (!Number.isFinite(mobileVisibleCount) || mobileVisibleCount < 1)) {
            mobileVisibleCount = 1;
        } else if (visibleCards > mobileVisibleCount) {
            mobileVisibleCount = visibleCards;
        }

        cards.forEach(function (card, index) {
            if (index < mobileVisibleCount) {
                card.classList.remove('is-mobile-hidden', 'mobile-hidden-default');
            } else {
                card.classList.add('is-mobile-hidden', 'mobile-hidden-default');
            }
        });
    }

    function handleMediaQueryChange(e) {
        mobileVisibleCount = e.matches ? 1 : Infinity;
        applyMobileVisibility(document.querySelector('.girlsSection'));
    }

    function removeSkeleton() {
        document.querySelectorAll('.girlCard--skeleton[data-skeleton="append"]').forEach(el => el.remove());
    }

    window.observeDeferredImages = observeDeferredImages;
    observeDeferredImages(document);

    if (mobileMediaQuery) {
        if (typeof mobileMediaQuery.addEventListener === 'function') {
            mobileMediaQuery.addEventListener('change', handleMediaQueryChange);
        } else if (typeof mobileMediaQuery.addListener === 'function') {
            mobileMediaQuery.addListener(handleMediaQueryChange);
        }
    }

    const moreInfoBtn = document.querySelector('.more-info');
    if (!hasDeferredGirls && !hasMorePages && moreInfoBtn) {
        moreInfoBtn.style.display = 'none';
    }

    let filterOptionsPromise = null;

    function populateSelect(selectId, options) {
        const select = document.getElementById(selectId);
        if (!select || !Array.isArray(options) || select.dataset.populated === 'true') return;

        const fragment = document.createDocumentFragment();
        options.forEach(function (option) {
            if (!option) return;
            const optionEl = document.createElement('option');
            optionEl.value = option;
            optionEl.textContent = option;
            fragment.appendChild(optionEl);
        });
        select.appendChild(fragment);
        select.dataset.populated = 'true';
    }

    function serializeFilters() {
        const form = document.getElementById('filtersForm');
        if (!form) return {};

        const formData = new FormData(form);
        const filters = {};

        for (let [name, value] of formData.entries()) {
            if (name.includes('[]')) {
                const cleanName = name.replace('[]', '');
                if (!filters[cleanName]) filters[cleanName] = [];
                filters[cleanName].push(value);
            } else if (value) {
                filters[name] = value;
            }
        }

        return filters;
    }

    function showLoader() {
        if (document.querySelector('.girls-loader')) return;
        const section = document.querySelector('.girlsSection');
        if (!section) return;

        const loader = document.createElement('div');
        loader.className = 'girls-loader';
        loader.innerHTML = '<div class="spinner"></div><p>Загрузка...</p>';
        section.insertAdjacentElement('beforebegin', loader);
    }

    function hideLoader() {
        const loader = document.querySelector('.girls-loader');
        if (!loader) return;
        
        loader.style.opacity = '0';
        loader.style.transition = 'opacity 0.3s';
        setTimeout(() => loader.remove(), 300);
    }

    function showSkeletons(count = 4) {
        const section = document.querySelector('.girlsSection');
        if (!section) return;

        removeSkeleton();
        let html = '';
        for (let i = 0; i < count; i++) {
            html += `
            <div class="girlCard girlCard--skeleton" data-skeleton="append" aria-hidden="true">
                <div class="wrapper-girlCard">
                    <div class="photoGirl">
                        <div class="skeleton-box skeleton-box--photo"></div>
                    </div>
                    <div class="right-wrapper-girlCard">
                        <div class="skeleton-box skeleton-box--title"></div>
                        <div class="skeleton-box skeleton-box--subtitle"></div>
                        <div class="skeleton-row">
                            <div class="skeleton-box skeleton-box--chip"></div>
                            <div class="skeleton-box skeleton-box--chip"></div>
                        </div>
                        <div class="skeleton-box skeleton-box--line"></div>
                        <div class="skeleton-box skeleton-box--line"></div>
                    </div>
                </div>
                <div class="bottom-girlCard">
                    <div class="skeleton-box skeleton-box--chip"></div>
                </div>
            </div>`;
        }
        section.insertAdjacentHTML('beforeend', html);
    }

    function loadGirls(page, isAppend = false) {
        if (isLoading) return;
        isLoading = true;

        if (isAppend) {
            showLoader();
            showSkeletons();
        } else {
            showLoader();
        }

        const params = isAppend ? lastAppliedFilters : serializeFilters();
        params.page = page;
        const city = localStorage.getItem('selectedCity') || 'moscow';
        params.city = city;

        const queryString = new URLSearchParams(params).toString();

        fetch('/?' + queryString, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (isAppend) {
                appendGirls(data.girls);
                hideLoader();
                removeSkeleton();
            } else {
                replaceGirls(data.girls);
                hideLoader();
            }

            hasMore = data.hasMore;
            currentPage = page;

            const moreInfoBtn = document.querySelector('.more-info');
            if (moreInfoBtn) {
                moreInfoBtn.style.display = hasMore ? 'block' : 'none';
            }

            hasMorePages = hasMore;
            isLoading = false;

            if (!isAppend) {
                renderPagination(page, data.total);
            }

            if (typeof window.updateFavoritesAfterLoad === 'function') {
                window.updateFavoritesAfterLoad();
            }
        })
        .catch(() => {
            hideLoader();
            removeSkeleton();
            isLoading = false;
            alert('Произошла ошибка при загрузке данных. Попробуйте еще раз.');
        });
    }

    function replaceGirls(girls) {
        const section = document.querySelector('.girlsSection');
        if (!section) return;

        removeSkeleton();
        deferredGirls = [];
        hasDeferredGirls = false;

        if (girls.length === 0) {
            section.innerHTML = '<div class="no-results"><p>По вашему запросу ничего не найдено. Попробуйте изменить фильтры.</p></div>';
        } else {
            let html = '';
            girls.forEach(girl => {
                html += buildGirlCard(girl);
            });
            section.innerHTML = html;

            const firstCard = section.querySelector('.girlCard');
            if (firstCard) {
                firstCard.classList.remove('is-mobile-hidden', 'mobile-hidden-default');
                firstCard.setAttribute('data-mobile-initial-hidden', 'false');
            }
        }

        if (mobileMediaQuery && mobileMediaQuery.matches) {
            mobileVisibleCount = 1;
        }

        applyMobileVisibility(section);

        if (typeof window.observeDeferredImages === 'function') {
            window.observeDeferredImages(section);
        }

        document.dispatchEvent(new CustomEvent('girlCards:mutated', { detail: { scope: section } }));
    }

    function appendGirls(girls) {
        const section = document.querySelector('.girlsSection');
        if (!section) return;

        removeSkeleton();

        girls.forEach(girl => {
            const html = buildGirlCard(girl);
            section.insertAdjacentHTML('beforeend', html);
            const lastCard = section.lastElementChild;
            if (lastCard && typeof window.observeDeferredImages === 'function') {
                window.observeDeferredImages(lastCard);
            }
        });

        applyMobileVisibility(section);

        if (section) {
            document.dispatchEvent(new CustomEvent('girlCards:mutated', { detail: { scope: section } }));
        }
    }

    function buildGirlCard(e) {
        return `
            <div class="girlCard is-mobile-hidden mobile-hidden-default" data-girl-id="${e.id}" data-mobile-initial-hidden="true">
                <div class="wrapper-girlCard">
                    <a href="/girl/${e.id}" class="photoGirl" aria-label="Открыть анкету ${e.name}">
                        ${e.hasStatus ? '<div class="status-photoGirl" aria-hidden="true"><img src="/img/status-photoGirl.png" alt="Фото проверено" loading="lazy" decoding="async" width="56" height="56"></div>' : ''}
                        ${e.hasVideo ? '<div class="video-photoGirl" aria-hidden="true"><img src="/img/video-photoGirl.png" alt="Есть видео" loading="lazy" decoding="async" width="56" height="56"></div>' : ''}
                        <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==" data-src="${e.photo}" alt="Фото ${e.name}" class="photoGirl__img deferred-image" loading="lazy" decoding="async" fetchpriority="auto" width="210" height="315">
                    </a>
                    <div class="right-wrapper-girlCard">
                        <div class="name-girlCard">
                            <a href="/girl/${e.id}" style="color: inherit; text-decoration: none;" aria-label="Перейти в анкету ${e.name}">
                                <p>${e.name}</p>
                            </a>
                            <a href="#" data-girl-id="${e.id}" class="favorite-toggle${e.favorite ? ' is-active' : ''}" aria-label="${e.favorite ? 'Удалить из избранного ' : 'Добавить в избранное '}${e.name}">
                                <span class="favorite-toggle__icon" aria-hidden="true"></span>
                            </a>
                        </div>
                        <p class="ageGirlCard">${e.age} года</p>
                        <div class="line-right-wrapper-girlCard"></div>
                        <div class="infoParameters-right-wrapper-girlCard">
                            <div class="block-infoParameters-right-wrapper-girlCard">
                                <p>Рост:</p>
                                <span>${e.height} см</span>
                            </div>
                            <div class="block-infoParameters-right-wrapper-girlCard">
                                <p>Вес:</p>
                                <span>${e.weight} кг</span>
                            </div>
                            <div class="block-infoParameters-right-wrapper-girlCard">
                                <p>Грудь:</p>
                                <span>${e.bust} размер</span>
                            </div>
                        </div>
                        <div class="line-right-wrapper-girlCard"></div>
                        <a href="tel:${e.phone}" class="tel-right-wrapper-girlCard">${e.phone}</a>
                        <div class="infoTownWhatsapp">
                            <p>${e.city},</p>
                            <a href="#" aria-label="Написать в WhatsApp ${e.name}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none" role="img" aria-label="WhatsApp">
                                    <rect width="25" height="25" rx="12.5" fill="#48C95F"></rect>
                                    <path d="M17.4543 7.53906C16.2195 6.30859 14.5732 5.625 12.8354 5.625C9.22256 5.625 6.29573 8.54167 6.29573 12.1419C6.29573 13.2812 6.61585 14.4206 7.16463 15.3776L6.25 18.75L9.72561 17.8385C10.686 18.3398 11.7378 18.6133 12.8354 18.6133C16.4482 18.6133 19.375 15.6966 19.375 12.0964C19.3293 10.4102 18.689 8.76953 17.4543 7.53906ZM15.9909 14.4661C15.8537 14.8307 15.2134 15.1953 14.8933 15.2409C14.6189 15.2865 14.253 15.2865 13.8872 15.1953C13.6585 15.1042 13.3384 15.013 12.9726 14.8307C11.3262 14.1471 10.2744 12.5065 10.1829 12.3698C10.0915 12.2786 9.49695 11.5039 9.49695 10.6836C9.49695 9.86328 9.90854 9.4987 10.0457 9.31641C10.1829 9.13411 10.3659 9.13411 10.503 9.13411C10.5945 9.13411 10.7317 9.13411 10.8232 9.13411C10.9146 9.13411 11.0518 9.08854 11.189 9.40755C11.3262 9.72656 11.6463 10.5469 11.6921 10.5924C11.7378 10.6836 11.7378 10.7747 11.6921 10.8659C11.6463 10.957 11.6006 11.0482 11.5091 11.1393C11.4177 11.2305 11.3262 11.3672 11.2805 11.4128C11.189 11.5039 11.0976 11.5951 11.189 11.7318C11.2805 11.9141 11.6006 12.4154 12.1037 12.8711C12.7439 13.418 13.247 13.6003 13.4299 13.6914C13.6128 13.7826 13.7043 13.737 13.7957 13.6458C13.8872 13.5547 14.2073 13.1901 14.2988 13.0078C14.3902 12.8255 14.5274 12.8711 14.6646 12.9167C14.8018 12.9622 15.625 13.3724 15.7622 13.4635C15.9451 13.5547 16.0366 13.6003 16.0823 13.6458C16.128 13.7826 16.128 14.1016 15.9909 14.4661Z" fill="white"></path>
                                </svg>
                            </a>
                        </div>
                        <p class="metro-right-wrapper-girlCard">${e.metro}</p>
                        <div class="blockPrecises-right-wrapper-girlCard">
                            <div class="blockPrecises-right-wrapper-girlCard__top blockPrecises-right-wrapper-girlCard__top-1">
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>1 час</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <span>${e.price1h}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>2 часа</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"></path>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"></path>
                                        </svg>
                                    </div>
                                    <span>${e.price2h}</span>
                                </div>
                            </div>
                            <div class="blockPrecises-right-wrapper-girlCard__top">
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock" data-theme="anal">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Анал</p>
                                    </div>
                                    <span>${e.priceAnal}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock" data-theme="night">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Ночь</p>
                                    </div>
                                    <span>${e.priceNight}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bottom-girlCard">
                    <div class="flex-bottom-girlCard">
                        <img src="/img/flex-bottom-girlCard-1.svg" alt="" loading="lazy" decoding="async" width="18" height="18" aria-hidden="true">
                        <p class="verified-status-text">${e.verified || 'Фото проверены'}</p>
                    </div>
                    <div class="right-bottom-girlCard">
                        ${e.outcall ? '<div class="flex-bottom-girlCard"><img src="/img/flex-bottom-girlCard-2.svg" alt="" loading="lazy" decoding="async" width="18" height="18" aria-hidden="true"><p>Выезд</p></div>' : ''}
                        ${e.apartment ? '<div class="flex-bottom-girlCard"><img src="/img/flex-bottom-girlCard-3.svg" alt="" loading="lazy" decoding="async" width="18" height="18" aria-hidden="true"><p>Апартаменты</p></div>' : ''}
                    </div>
                </div>
            </div>
        `;
    }

    function renderPagination(currentPage, total) {
        const perPage = 20;
        const totalPages = Math.max(1, Math.ceil(total / perPage));
        const container = document.querySelector('.paginationGirls');
        
        if (!container) return;
        if (totalPages <= 1) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'flex';

        const ellipsis = '<span class="block-paginationGirls" data-ellipsis="true">...</span>';
        const pageLink = (page) => `<a href="#!" class="block-paginationGirls${page === currentPage ? ' block-paginationGirls__active' : ''}" data-page="${page}">${page}</a>`;
        const pages = [];

        if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) pages.push(pageLink(i));
        } else if (currentPage <= 4) {
            for (let i = 1; i <= 5; i++) pages.push(pageLink(i));
            pages.push(ellipsis);
            pages.push(pageLink(totalPages));
        } else if (currentPage >= totalPages - 3) {
            pages.push(pageLink(1));
            if (totalPages > 5) pages.push(ellipsis);
            for (let i = Math.max(2, totalPages - 4); i <= totalPages; i++) pages.push(pageLink(i));
        } else {
            pages.push(pageLink(1));
            pages.push(ellipsis);
            for (let i = currentPage - 1; i <= currentPage + 1; i++) pages.push(pageLink(i));
            pages.push(ellipsis);
            pages.push(pageLink(totalPages));
        }

        const prevImg = '<img src="/img/arrowLeft.svg" alt="" width="36" height="36" decoding="async">';
        const nextImg = '<img src="/img/arrowNext.svg" alt="" width="36" height="36" decoding="async">';

        const nextBtn = currentPage >= totalPages
            ? `<span class="arrowPagination arrowPagination-next" aria-disabled="true" style="opacity: 0.5; cursor: not-allowed;">${nextImg}</span>`
            : `<a href="#!" class="arrowPagination arrowPagination-next" aria-label="Следующая страница" data-page="${currentPage + 1}">${nextImg}</a>`;

        const prevBtn = currentPage <= 1
            ? `<span class="arrowPagination arrowPagination-prev" aria-disabled="true" style="opacity: 0.5; cursor: not-allowed;">${prevImg}</span>`
            : `<a href="#!" class="arrowPagination arrowPagination-prev" aria-label="Предыдущая страница" data-page="${currentPage - 1}">${prevImg}</a>`;

        container.innerHTML = prevBtn + '<div class="pagination__paginationGirls">' + pages.join('') + '</div>' + nextBtn;
    }

    document.querySelectorAll('.filtersBtn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();

            if (!filterOptionsPromise) {
                filterOptionsPromise = fetch('/api/filter-options')
                    .then(response => response.json())
                    .then(data => {
                        populateSelect('hairColorSelect', data.hair_colors);
                        populateSelect('intimateTrimSelect', data.intimate_trims);
                        populateSelect('nationalitySelect', data.nationalities);
                        populateSelect('districtSelect', data.districts);
                        populateSelect('regionSelect', data.regions);
                    })
                    .catch(() => {
                        filterOptionsPromise = null;
                    });
            }

            const modal = document.getElementById('modal__project1');
            if (modal) {
                modal.style.display = 'block';
                setTimeout(() => modal.style.opacity = '1', 10);
            }
        });
    });

    const closeBtn = document.querySelector('.close1');
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            const modal = document.getElementById('modal__project1');
            if (modal) {
                modal.style.opacity = '0';
                setTimeout(() => modal.style.display = 'none', 300);
            }
        });
    }

    window.addEventListener('click', function (e) {
        if (e.target.id === 'modal__project1') {
            e.target.style.opacity = '0';
            setTimeout(() => e.target.style.display = 'none', 300);
        }
    });

    document.querySelectorAll('.btn-formFilterModal__btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            currentPage = 1;
            lastAppliedFilters = serializeFilters();
            
            const modal = document.getElementById('modal__project1');
            if (modal) {
                modal.style.opacity = '0';
                setTimeout(() => modal.style.display = 'none', 300);
            }
            
            loadGirls(1, false);
        });
    });

    if (moreInfoBtn) {
        moreInfoBtn.addEventListener('click', function (e) {
            e.preventDefault();

            function showMoreMobileCards() {
                if (!mobileMediaQuery || !mobileMediaQuery.matches) return 0;
                const section = document.querySelector('.girlsSection');
                if (!section) return 0;

                const cards = section.querySelectorAll('.girlCard');
                if (mobileVisibleCount >= cards.length) return 0;

                const oldCount = mobileVisibleCount;
                mobileVisibleCount = Math.min(cards.length, mobileVisibleCount + 2);
                applyMobileVisibility(section);
                return mobileVisibleCount - oldCount;
            }

            const shown = showMoreMobileCards();
            if (shown > 0) {
                const hiddenCards = document.querySelectorAll('.girlsSection .girlCard.is-mobile-hidden, .girlsSection .girlCard.mobile-hidden-default');
                if (hiddenCards.length > 0) return;
            }

            if (hasDeferredGirls && deferredGirls.length) {
                appendGirls(deferredGirls);
                deferredGirls = [];
                hasDeferredGirls = false;
                if (!hasMorePages) {
                    this.style.display = 'none';
                }
                return;
            }

            if (hasMore || hasMorePages) {
                if (!isLoading) {
                    loadGirls(currentPage + 1, true);
                }
            } else {
                this.style.display = 'none';
            }
        });
    }

    document.addEventListener('click', function (e) {
        const pageBtn = e.target.closest('.block-paginationGirls');
        if (pageBtn && !pageBtn.dataset.ellipsis) {
            e.preventDefault();
            const text = pageBtn.textContent.trim();
            if (text === '...') return;

            const page = parseInt(text);
            if (!isNaN(page) && !isLoading) {
                currentPage = page - 1;
                loadGirls(page, false);
                scrollToGirlsSection();
            }
        }

        const nextBtn = e.target.closest('.arrowPagination-next');
        if (nextBtn && nextBtn.getAttribute('aria-disabled') !== 'true') {
            e.preventDefault();
            if (isLoading || !hasMore) return;
            loadGirls(currentPage + 1, false);
            scrollToGirlsSection();
        }

        const prevBtn = e.target.closest('.arrowPagination-prev');
        if (prevBtn && prevBtn.getAttribute('aria-disabled') !== 'true') {
            e.preventDefault();
            if (isLoading || currentPage <= 1) return;
            loadGirls(currentPage - 1, false);
            scrollToGirlsSection();
        }
    });
});
