<div class="metro-modal" id="metroModal">
    <div class="metro-modal-overlay"></div>
    <div class="metro-modal-content">
        <button class="metro-modal-close" id="closeMetroModal">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M18 6L6 18M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </button>
        <h2 class="metro-modal-title">Выберите метро</h2>
        <div class="metro-modal-search">
            <input type="text" id="metroSearch" placeholder="Поиск станции метро...">
        </div>
        <div class="metro-modal-list" id="metroList">
            <div class="metro-modal-loader">Загрузка...</div>
        </div>
    </div>
</div>

<style>
.metro-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 10000;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.metro-modal.active {
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 1;
}

.metro-modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    backdrop-filter: blur(5px);
}

.metro-modal-content {
    position: relative;
    background: #FFFFFF;
    border-radius: 20px;
    padding: 40px;
    width: 90%;
    max-width: 600px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
    z-index: 1;
}

.metro-modal-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 8px;
    color: #666;
    transition: color 0.3s;
}

.metro-modal-close:hover {
    color: #7E1D32;
}

.metro-modal-title {
    font-family: "Noto Sans", sans-serif;
    font-size: 28px;
    font-weight: 700;
    color: #1A1A1A;
    margin-bottom: 20px;
}

.metro-modal-search {
    margin-bottom: 20px;
}

.metro-modal-search input {
    width: 100%;
    padding: 12px 20px;
    border: 1px solid #E0E0E0;
    border-radius: 10px;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    outline: none;
    transition: border-color 0.3s;
}

.metro-modal-search input:focus {
    border-color: #7E1D32;
}

.metro-modal-list {
    overflow-y: auto;
    max-height: 400px;
    margin-right: -10px;
    padding-right: 10px;
}

.metro-modal-list::-webkit-scrollbar {
    width: 6px;
}

.metro-modal-list::-webkit-scrollbar-track {
    background: #F5F5F5;
    border-radius: 10px;
}

.metro-modal-list::-webkit-scrollbar-thumb {
    background: #7E1D32;
    border-radius: 10px;
}

.metro-modal-loader {
    text-align: center;
    padding: 40px;
    color: #999;
    font-family: "Noto Sans", sans-serif;
}

.metro-item {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 8px;
    cursor: pointer;
    transition: all 0.3s;
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    color: #1A1A1A;
    background: #F9F9F9;
}

.metro-item:hover {
    background: #7E1D32;
    color: #FFFFFF;
    transform: translateX(5px);
}

.metro-item.hidden {
    display: none;
}

@media screen and (max-width: 768px) {
    .metro-modal-content {
        padding: 30px 20px;
        width: 95%;
        max-height: 90vh;
    }
    
    .metro-modal-title {
        font-size: 24px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const metroModal = document.getElementById('metroModal');
    const closeMetroModalBtn = document.getElementById('closeMetroModal');
    const modalOverlay = metroModal?.querySelector('.metro-modal-overlay');
    const metroSearch = document.getElementById('metroSearch');
    const metroList = document.getElementById('metroList');
    let allMetros = [];
    
    window.openMetroModal = function() {
        if (metroModal) {
            metroModal.classList.add('active');
            document.body.style.overflow = 'hidden';
            loadMetros();
        }
    };
    
    function closeMetroModal() {
        if (metroModal) {
            metroModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }
    
    if (closeMetroModalBtn) {
        closeMetroModalBtn.addEventListener('click', closeMetroModal);
    }
    
    if (modalOverlay) {
        modalOverlay.addEventListener('click', closeMetroModal);
    }
    
    async function loadMetros() {
        try {
            metroList.innerHTML = '<div class="metro-modal-loader">Загрузка...</div>';
            
            const selectedCity = localStorage.getItem('selectedCity') || 'moscow';
            const response = await fetch(`{{ route("metro.list") }}?city=${selectedCity}`);
            const data = await response.json();
            
            if (data.success && data.metros) {
                allMetros = data.metros;
                renderMetros(allMetros);
            } else {
                metroList.innerHTML = '<div class="metro-modal-loader">Ошибка загрузки данных</div>';
            }
        } catch (error) {
            console.error('Error loading metros:', error);
            metroList.innerHTML = '<div class="metro-modal-loader">Ошибка загрузки данных</div>';
        }
    }
    
    function renderMetros(metros) {
        if (metros.length === 0) {
            metroList.innerHTML = '<div class="metro-modal-loader">Станции не найдены</div>';
            return;
        }
        
        metroList.innerHTML = metros.map(metro => 
            `<div class="metro-item" data-metro="${metro}">м. ${metro}</div>`
        ).join('');
        
        document.querySelectorAll('.metro-item').forEach(item => {
            item.addEventListener('click', function() {
                const metro = this.getAttribute('data-metro');
                selectMetro(metro);
            });
        });
    }
    
    function selectMetro(metro) {
        localStorage.setItem('selectedMetro', metro);
        updateHeaderMetro(metro);
        closeMetroModal();
        window.location.href = `/?metro=${encodeURIComponent(metro)}`;
    }
    
    function updateHeaderMetro(metro) {
        const metroElements = document.querySelectorAll('.modal-headerMetro');
        metroElements.forEach(element => {
            element.textContent = metro;
        });
    }
    
    function initializeMetroDisplay() {
        const urlParams = new URLSearchParams(window.location.search);
        const metroFromUrl = urlParams.get('metro');
        const metroFromStorage = localStorage.getItem('selectedMetro');
        const cityFromUrl = urlParams.get('city');
        
        if (cityFromUrl) {
            localStorage.removeItem('selectedMetro');
            updateHeaderMetro('Выберите метро');
            return;
        }
        
        const selectedMetro = metroFromUrl || metroFromStorage;
        
        if (selectedMetro) {
            updateHeaderMetro(selectedMetro);
            if (metroFromUrl && metroFromUrl !== metroFromStorage) {
                localStorage.setItem('selectedMetro', metroFromUrl);
            }
        } else {
            updateHeaderMetro('Выберите метро');
        }
    }
    
    initializeMetroDisplay();
    
    if (metroSearch) {
        metroSearch.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase().trim();
            
            if (searchTerm === '') {
                renderMetros(allMetros);
            } else {
                const filtered = allMetros.filter(metro => 
                    metro.toLowerCase().includes(searchTerm)
                );
                renderMetros(filtered);
            }
        });
    }
    
    document.addEventListener('click', function(e) {
        if (e.target.matches('[data-metro-trigger]') || e.target.closest('[data-metro-trigger]')) {
            e.preventDefault();
            window.openMetroModal();
        }
    });
});
</script>

