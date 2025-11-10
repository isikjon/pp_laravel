document.addEventListener('DOMContentLoaded', function() {
    const noImagePath = '/img/noimage.png';
    const excludeClasses = ['status-photoGirl', 'video-photoGirl', 'favorite-toggle__icon'];
    
    function shouldHandleImage(img) {
        if (!img.src || img.src.includes('data:image')) {
            return false;
        }
        
        for (var i = 0; i < excludeClasses.length; i++) {
            if (img.closest('.' + excludeClasses[i])) {
                return false;
            }
        }
        
        if (img.src.includes('/img/') && !img.src.includes('prostitutki-today') && !img.src.includes('files.')) {
            return false;
        }
        
        return true;
    }
    
    function handleImageError(img) {
        if (!shouldHandleImage(img)) {
            return;
        }
        
        if (img.src !== window.location.origin + noImagePath && !img.dataset.fallbackApplied) {
            img.dataset.fallbackApplied = 'true';
            img.src = noImagePath;
            img.removeAttribute('srcset');
            if (img.dataset.src) {
                img.removeAttribute('data-src');
            }
        }
    }
    
    function attachErrorHandler(img) {
        if (!shouldHandleImage(img)) {
            return;
        }
        
        if (img.complete && img.naturalWidth === 0) {
            handleImageError(img);
        }
        
        if (!img.dataset.errorHandlerAttached) {
            img.dataset.errorHandlerAttached = 'true';
            img.addEventListener('error', function() {
                handleImageError(this);
            });
        }
    }
    
    const selectors = [
        '.photoGirl__img',
        '.photo-flexWrapperGirlCard',
        '#girlGallery img',
        '.gridzy-container img',
        '.salon-gallery img'
    ];
    
    selectors.forEach(function(selector) {
        const images = document.querySelectorAll(selector);
        images.forEach(function(img) {
            attachErrorHandler(img);
        });
    });
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            mutation.addedNodes.forEach(function(node) {
                if (node.nodeType === 1) {
                    selectors.forEach(function(selector) {
                        const newImages = node.querySelectorAll ? node.querySelectorAll(selector) : [];
                        newImages.forEach(function(img) {
                            attachErrorHandler(img);
                        });
                    });
                    
                    if (node.tagName === 'IMG') {
                        attachErrorHandler(node);
                    }
                }
            });
        });
    });
    
    const observeTargets = ['.girlsSection', '.photoGirlCardWrap', '.gridzy-container', '.mainContent', '.salon-gallery'];
    observeTargets.forEach(function(targetSelector) {
        const target = document.querySelector(targetSelector);
        if (target) {
            observer.observe(target, {
                childList: true,
                subtree: true
            });
        }
    });
});

