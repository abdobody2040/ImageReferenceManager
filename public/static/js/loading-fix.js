
// Unified loading overlay fix
(function() {
    function hideLoadingElements() {
        const elements = document.querySelectorAll('.loading-overlay, #loading-overlay, #loading_overlay, .spinner-container, .loading-indicator, .loader, .spinner');
        elements.forEach(el => {
            if (el) {
                el.style.display = 'none';
                el.remove();
            }
        });
        
        // Reset counters
        window.spinnerCounter = 0;
        window.loadingCounter = 0;
        
        // Clear timeouts
        if (window.spinnerTimeout) clearTimeout(window.spinnerTimeout);
        if (window.loadingTimeout) clearTimeout(window.loadingTimeout);
    }
    
    // Run immediately and periodically
    hideLoadingElements();
    setInterval(hideLoadingElements, 1000);
    
    // Run on DOM ready
    document.addEventListener('DOMContentLoaded', hideLoadingElements);
    window.addEventListener('load', hideLoadingElements);
})();
