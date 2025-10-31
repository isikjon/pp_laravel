<style>
.search-rightHeaderTop {
    position: relative;
}

.search-dropdown {
    position: absolute;
    top: calc(100% + 5px);
    left: 0;
    right: 0;
    background: #FFFFFF;
    border-radius: 10px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
    max-height: 400px;
    overflow-y: auto;
    display: none;
    z-index: 1000;
}

.search-dropdown.active {
    display: block;
}

.search-dropdown-item {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    cursor: pointer;
    transition: background 0.3s;
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid #F5F5F5;
}

.search-dropdown-item:last-child {
    border-bottom: none;
}

.search-dropdown-item:hover {
    background: #F9F9F9;
}

.search-dropdown-photo {
    width: 50px;
    height: 50px;
    border-radius: 8px;
    object-fit: cover;
    margin-right: 15px;
    flex-shrink: 0;
}

.search-dropdown-info {
    flex: 1;
    min-width: 0;
}

.search-dropdown-name {
    font-family: "Noto Sans", sans-serif;
    font-size: 16px;
    font-weight: 600;
    color: #1A1A1A;
    margin-bottom: 4px;
}

.search-dropdown-details {
    font-family: "Noto Sans", sans-serif;
    font-size: 14px;
    color: #666;
    display: flex;
    align-items: center;
    gap: 10px;
}

.search-dropdown-metro {
    color: #7E1D32;
}

.search-dropdown-empty {
    padding: 20px;
    text-align: center;
    color: #999;
    font-family: "Noto Sans", sans-serif;
}

.search-dropdown-loading {
    padding: 20px;
    text-align: center;
    color: #999;
    font-family: "Noto Sans", sans-serif;
}

.search-dropdown::-webkit-scrollbar {
    width: 6px;
}

.search-dropdown::-webkit-scrollbar-track {
    background: #F5F5F5;
    border-radius: 10px;
}

.search-dropdown::-webkit-scrollbar-thumb {
    background: #7E1D32;
    border-radius: 10px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-rightHeaderTop input[name="query"]');
    const searchForm = document.querySelector('.search-rightHeaderTop');
    let searchTimeout = null;
    let searchDropdown = null;
    
    if (!searchInput || !searchForm) return;
    
    searchDropdown = document.createElement('div');
    searchDropdown.className = 'search-dropdown';
    searchForm.appendChild(searchDropdown);
    
    searchForm.addEventListener('submit', function(e) {
        e.preventDefault();
    });
    
    searchInput.addEventListener('input', function(e) {
        const query = e.target.value.trim();
        
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            searchDropdown.classList.remove('active');
            return;
        }
        
        searchDropdown.innerHTML = '<div class="search-dropdown-loading">Поиск...</div>';
        searchDropdown.classList.add('active');
        
        searchTimeout = setTimeout(async () => {
            try {
                const response = await fetch(`{{ route('search') }}?query=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data.success && data.girls && data.girls.length > 0) {
                    searchDropdown.innerHTML = data.girls.map(girl => `
                        <a href="/girl/${girl.id}" class="search-dropdown-item">
                            <img src="${girl.photo}" alt="${girl.name}" class="search-dropdown-photo">
                            <div class="search-dropdown-info">
                                <div class="search-dropdown-name">${girl.name}</div>
                                <div class="search-dropdown-details">
                                    <span class="search-dropdown-metro">${girl.metro}</span>
                                    <span>•</span>
                                    <span>${girl.phone}</span>
                                </div>
                            </div>
                        </a>
                    `).join('');
                } else {
                    searchDropdown.innerHTML = '<div class="search-dropdown-empty">Девушки не найдены</div>';
                }
            } catch (error) {
                console.error('Search error:', error);
                searchDropdown.innerHTML = '<div class="search-dropdown-empty">Ошибка поиска</div>';
            }
        }, 300);
    });
    
    document.addEventListener('click', function(e) {
        if (!searchForm.contains(e.target)) {
            searchDropdown.classList.remove('active');
        }
    });
    
    searchInput.addEventListener('focus', function() {
        if (searchInput.value.trim().length >= 2) {
            searchDropdown.classList.add('active');
        }
    });
});
</script>

