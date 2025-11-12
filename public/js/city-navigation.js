// Dynamically add city parameter to all internal links that don't have it
document.addEventListener('DOMContentLoaded', function() {
    function updateLinksWithCity() {
        const urlParams = new URLSearchParams(window.location.search);
        const currentCity = urlParams.get('city') || localStorage.getItem('selectedCity') || 'moscow';
        
        // Get all internal links
        const links = document.querySelectorAll('a[href^="/"], a[href*="' + window.location.hostname + '"]');
        
        links.forEach(function(link) {
            try {
                const href = link.getAttribute('href');
                if (!href) return;
                
                // Skip if it's an anchor link or external link
                if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) {
                    return;
                }
                
                // Parse URL
                let url;
                try {
                    url = new URL(href, window.location.origin);
                } catch (e) {
                    // If it's a relative URL, create a full URL
                    url = new URL(href, window.location.origin);
                }
                
                // Only modify internal links
                if (url.hostname !== window.location.hostname && url.hostname !== '') {
                    return;
                }
                
                // Skip admin and API routes
                if (url.pathname.startsWith('/admin') || url.pathname.startsWith('/api')) {
                    return;
                }
                
                // Add city parameter if it doesn't exist
                if (!url.searchParams.has('city')) {
                    url.searchParams.set('city', currentCity);
                    link.setAttribute('href', url.pathname + url.search + (url.hash || ''));
                }
            } catch (e) {
                // Silently fail for invalid URLs
                console.debug('Error updating link:', e);
            }
        });
    }
    
    // Update links on page load
    updateLinksWithCity();
    
    // Update links when DOM changes (for dynamically added content)
    const observer = new MutationObserver(function(mutations) {
        let shouldUpdate = false;
        mutations.forEach(function(mutation) {
            if (mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1 && (node.tagName === 'A' || node.querySelectorAll('a').length > 0)) {
                        shouldUpdate = true;
                    }
                });
            }
        });
        if (shouldUpdate) {
            updateLinksWithCity();
        }
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
