$(document).ready(function() {
    let currentPage = 1;
    let loading = false;
    let hasMore = true;
    let currentFilters = {};
    
    $('.filtersBtn').on('click', function(e) {
        e.preventDefault();
        $('#modal__project1').fadeIn(300);
    });
    
    $('.close1').on('click', function() {
        $('#modal__project1').fadeOut(300);
    });
    
    $(window).on('click', function(event) {
        if (event.target.id === 'modal__project1') {
            $('#modal__project1').fadeOut(300);
        }
    });
    
    $('.btn-formFilterModal__btn').on('click', function(e) {
        e.preventDefault();
        
        currentPage = 1;
        currentFilters = getFilters();
        
        $('#modal__project1').fadeOut(300);
        
        loadGirls(1, false);
    });
    
    $('.more-info').on('click', function(e) {
        e.preventDefault();
        if (hasMore && !loading) {
            loadGirls(currentPage + 1, true);
        }
    });
    
    $(document).on('click', '.block-paginationGirls', function(e) {
        e.preventDefault();
        const pageText = $(this).text().trim();
        
        if (pageText === '...') {
            return;
        }
        
        const page = parseInt(pageText);
        if (!isNaN(page) && !loading) {
            currentPage = page - 1;
            loadGirls(page, false);
            
            $('html, body').animate({
                scrollTop: $('.girlsSection').offset().top - 100
            }, 500);
        }
    });
    
    $(document).on('click', '.arrowPagination-next', function(e) {
        e.preventDefault();
        if (loading || !hasMore) return;
        
        const newPage = currentPage + 1;
        loadGirls(newPage, false);
        
        $('html, body').animate({
            scrollTop: $('.girlsSection').offset().top - 100
        }, 500);
    });
    
    $(document).on('click', '.arrowPagination-prev', function(e) {
        e.preventDefault();
        if (loading || currentPage <= 1) return;
        
        const newPage = currentPage - 1;
        loadGirls(newPage, false);
        
        $('html, body').animate({
            scrollTop: $('.girlsSection').offset().top - 100
        }, 500);
    });
    
    function getFilters() {
        const formData = $('#filtersForm').serializeArray();
        const filters = {};

        formData.forEach(function(item) {
            if (item.name.includes('[]')) {
                const key = item.name.replace('[]', '');
                if (!filters[key]) {
                    filters[key] = [];
                }
                filters[key].push(item.value);
            } else {
                if (item.value) {
                    filters[item.name] = item.value;
                }
            }
        });

        return filters;
    }
    
    function showLoader() {
        const loader = `
            <div class="girls-loader">
                <div class="spinner"></div>
                <p>Загрузка...</p>
            </div>
        `;
        
        if (!$('.girls-loader').length) {
            $('.girlsSection').before(loader);
        }
    }
    
    function hideLoader() {
        $('.girls-loader').fadeOut(300, function() {
            $(this).remove();
        });
    }
    
    function showSectionLoader() {
        showLoader();
    }
    
    function hideSectionLoader() {
        hideLoader();
    }
    
    function loadGirls(page, append = false) {
        if (loading) {
            return;
        }
        
        loading = true;
        
        if (append) {
            showLoader();
        } else {
            showSectionLoader();
        }
        
        const filters = append ? currentFilters : getFilters();
        filters.page = page;

        $.ajax({
            url: '/',
            method: 'GET',
            data: filters,
            dataType: 'json',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (append) {
                    appendGirls(response.girls);
                    hideLoader();
                } else {
                    replaceGirls(response.girls);
                    hideSectionLoader();
                }

                hasMore = response.hasMore;
                currentPage = page;

                if (!hasMore) {
                    $('.more-info').hide();
                } else {
                    $('.more-info').show();
                }

                loading = false;
                
                if (!append) {
                    updatePagination(page, response.total);
                }
                
                if (typeof window.updateFavoritesAfterLoad === 'function') {
                    window.updateFavoritesAfterLoad();
                }
            },
            error: function() {
                hideLoader();
                hideSectionLoader();
                loading = false;
                
                alert('Произошла ошибка при загрузке данных. Попробуйте еще раз.');
            }
        });
    }
    
    function replaceGirls(girls) {
        const container = $('.girlsSection');
        
        if (!container.length) {
            return;
        }
        
        if (girls.length === 0) {
            container.html('<div class="no-results"><p>По вашему запросу ничего не найдено. Попробуйте изменить фильтры.</p></div>');
        } else {
            let html = '';
            girls.forEach(function(girl) {
                html += createGirlCard(girl);
            });
            container.html(html);
        }
    }
    
    function appendGirls(girls) {
        const container = $('.girlsSection');
        
        girls.forEach(function(girl) {
            const card = $(createGirlCard(girl));
            card.hide();
            container.append(card);
            card.fadeIn(400);
        });
    }
    
    function createGirlCard(girl) {
        return `
            <div class="girlCard" data-girl-id="${girl.id}">
                <div class="wrapper-girlCard">
                    <a href="/girl/${girl.id}" class="photoGirl" style="display: block; position: relative;">
                        ${girl.hasStatus ? '<div class="status-photoGirl"><img src="/img/status-photoGirl.png" alt=""></div>' : ''}
                        ${girl.hasVideo ? '<div class="video-photoGirl"><img src="/img/video-photoGirl.png" alt=""></div>' : ''}
                        <img src="${girl.photo}" alt="" class="photoGirl__img">
                    </a>
                    <div class="right-wrapper-girlCard">
                        <div class="name-girlCard">
                            <a href="/girl/${girl.id}" style="color: inherit; text-decoration: none;">
                                <p>${girl.name}</p>
                            </a>
                            <a href="#!" data-girl-id="${girl.id}">
                                <img src="/img/${girl.favorite ? 'flexBottomHeader-8-2.svg' : 'flexBottomHeader-8.svg'}" alt="">
                            </a>
                        </div>
                        <p class="ageGirlCard">${girl.age} года</p>
                        <div class="line-right-wrapper-girlCard"></div>
                        <div class="infoParameters-right-wrapper-girlCard">
                            <div class="block-infoParameters-right-wrapper-girlCard">
                                <p>Рост:</p>
                                <span>${girl.height} см</span>
                            </div>
                            <div class="block-infoParameters-right-wrapper-girlCard">
                                <p>Вес:</p>
                                <span>${girl.weight} кг</span>
                            </div>
                            <div class="block-infoParameters-right-wrapper-girlCard">
                                <p>Грудь:</p>
                                <span>${girl.bust} размер</span>
                            </div>
                        </div>
                        <div class="line-right-wrapper-girlCard"></div>
                        <a href="tel:${girl.phone}" class="tel-right-wrapper-girlCard">${girl.phone}</a>
                        <div class="infoTownWhatsapp">
                            <p>${girl.city},</p>
                            <a href="#!">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" viewBox="0 0 25 25" fill="none">
                                    <rect width="25" height="25" rx="12.5" fill="#48C95F"/>
                                    <path d="M17.4543 7.53906C16.2195 6.30859 14.5732 5.625 12.8354 5.625C9.22256 5.625 6.29573 8.54167 6.29573 12.1419C6.29573 13.2812 6.61585 14.4206 7.16463 15.3776L6.25 18.75L9.72561 17.8385C10.686 18.3398 11.7378 18.6133 12.8354 18.6133C16.4482 18.6133 19.375 15.6966 19.375 12.0964C19.3293 10.4102 18.689 8.76953 17.4543 7.53906ZM15.9909 14.4661C15.8537 14.8307 15.2134 15.1953 14.8933 15.2409C14.6189 15.2865 14.253 15.2865 13.8872 15.1953C13.6585 15.1042 13.3384 15.013 12.9726 14.8307C11.3262 14.1471 10.2744 12.5065 10.1829 12.3698C10.0915 12.2786 9.49695 11.5039 9.49695 10.6836C9.49695 9.86328 9.90854 9.4987 10.0457 9.31641C10.1829 9.13411 10.3659 9.13411 10.503 9.13411C10.5945 9.13411 10.7317 9.13411 10.8232 9.13411C10.9146 9.13411 11.0518 9.08854 11.189 9.40755C11.3262 9.72656 11.6463 10.5469 11.6921 10.5924C11.7378 10.6836 11.7378 10.7747 11.6921 10.8659C11.6463 10.957 11.6006 11.0482 11.5091 11.1393C11.4177 11.2305 11.3262 11.3672 11.2805 11.4128C11.189 11.5039 11.0976 11.5951 11.189 11.7318C11.2805 11.9141 11.6006 12.4154 12.1037 12.8711C12.7439 13.418 13.247 13.6003 13.4299 13.6914C13.6128 13.7826 13.7043 13.737 13.7957 13.6458C13.8872 13.5547 14.2073 13.1901 14.2988 13.0078C14.3902 12.8255 14.5274 12.8711 14.6646 12.9167C14.8018 12.9622 15.625 13.3724 15.7622 13.4635C15.9451 13.5547 16.0366 13.6003 16.0823 13.6458C16.128 13.7826 16.128 14.1016 15.9909 14.4661Z" fill="white"/>
                                </svg>
                            </a>
                        </div>
                        <p class="metro-right-wrapper-girlCard">${girl.metro}</p>
                        <div class="blockPrecises-right-wrapper-girlCard">
                            <div class="blockPrecises-right-wrapper-girlCard__top blockPrecises-right-wrapper-girlCard__top-1">
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>1 час</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>${girl.price1h.toLocaleString('ru-RU')}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div class="blockPrecises-right-wrapper-girlCard__topBlock">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>2 часа</p>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                            <path d="M9.66668 5.49998C9.66668 7.79998 7.80001 9.66665 5.50001 9.66665C3.20001 9.66665 1.33334 7.79998 1.33334 5.49998C1.33334 3.19998 3.20001 1.33331 5.50001 1.33331C7.80001 1.33331 9.66668 3.19998 9.66668 5.49998Z" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M7.04583 6.82498L5.75416 6.05415C5.52916 5.92082 5.34583 5.59998 5.34583 5.33748V3.62915" stroke="#292D32" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </div>
                                    <span>${girl.price2h.toLocaleString('ru-RU')}</span>
                                </div>
                            </div>
                            <div class="blockPrecises-right-wrapper-girlCard__top">
                                <div style="background: url(/img/bgAnal.png) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Анал</p>
                                    </div>
                                    <span>${girl.priceAnal ? girl.priceAnal.toLocaleString('ru-RU') : '-'}</span>
                                </div>
                                <div class="line-blockPrecises-right-wrapper-girlCard__top"></div>
                                <div style="background: url(/img/bgNight.png) center center no-repeat;" class="blockPrecises-right-wrapper-girlCard__topBlock blockPrecises-right-wrapper-girlCard__topBlock-2">
                                    <div class="hourFlex-blockPrecises-right-wrapper-girlCard__top">
                                        <p>Ночь</p>
                                    </div>
                                    <span>${girl.priceNight.toLocaleString('ru-RU')}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bottom-girlCard">
                    <div class="flex-bottom-girlCard">
                        <img src="/img/flex-bottom-girlCard-1.svg" alt="">
                        <p style="color: #00A81E">${girl.verified || 'Фото проверены'}</p>
                    </div>
                    <div class="right-bottom-girlCard">
                        ${girl.outcall ? '<div class="flex-bottom-girlCard"><img src="/img/flex-bottom-girlCard-2.svg" alt=""><p>Выезд</p></div>' : ''}
                        ${girl.apartment ? '<div class="flex-bottom-girlCard"><img src="/img/flex-bottom-girlCard-3.svg" alt=""><p>Апартаменты</p></div>' : ''}
                    </div>
                </div>
            </div>
        `;
    }
     
     function updatePagination(currentPageNum, totalItems) {
         const perPage = 20;
         const totalPages = Math.ceil(totalItems / perPage);
         
         let paginationHTML = '';
         
         if (totalPages <= 7) {
            for (let i = 1; i <= totalPages; i++) {
                const activeClass = i === currentPageNum ? ' block-paginationGirls__active' : '';
                paginationHTML += `<a href="#!" class="block-paginationGirls${activeClass}">${i}</a>`;
            }
         } else {
             if (currentPageNum <= 4) {
                 for (let i = 1; i <= 5; i++) {
                     const activeClass = i === currentPageNum ? ' block-paginationGirls__active' : '';
                     paginationHTML += `<a href="#!" class="block-paginationGirls${activeClass}">${i}</a>`;
                 }
                 paginationHTML += `<a href="#!" class="block-paginationGirls">...</a>`;
                 paginationHTML += `<a href="#!" class="block-paginationGirls">${totalPages}</a>`;
             } else if (currentPageNum >= totalPages - 3) {
                 paginationHTML += `<a href="#!" class="block-paginationGirls">1</a>`;
                 paginationHTML += `<a href="#!" class="block-paginationGirls">...</a>`;
                 for (let i = totalPages - 4; i <= totalPages; i++) {
                     const activeClass = i === currentPageNum ? ' block-paginationGirls__active' : '';
                     paginationHTML += `<a href="#!" class="block-paginationGirls${activeClass}">${i}</a>`;
                 }
             } else {
                 paginationHTML += `<a href="#!" class="block-paginationGirls">1</a>`;
                 paginationHTML += `<a href="#!" class="block-paginationGirls">...</a>`;
                 for (let i = currentPageNum - 1; i <= currentPageNum + 1; i++) {
                     const activeClass = i === currentPageNum ? ' block-paginationGirls__active' : '';
                     paginationHTML += `<a href="#!" class="block-paginationGirls${activeClass}">${i}</a>`;
                 }
                 paginationHTML += `<a href="#!" class="block-paginationGirls">...</a>`;
                 paginationHTML += `<a href="#!" class="block-paginationGirls">${totalPages}</a>`;
             }
         }
         
         $('.pagination__paginationGirls').html(paginationHTML);
         
         $('.arrowPagination-prev').css('opacity', currentPageNum === 1 ? '0.5' : '1');
         $('.arrowPagination-next').css('opacity', currentPageNum === totalPages ? '0.5' : '1');
     }
});
