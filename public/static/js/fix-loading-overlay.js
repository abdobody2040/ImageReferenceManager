/**
 * Emergency fix for loading overlay issue
 * This script ensures the loading overlay is hidden
 */

// Hide loading overlay immediately when the script loads
(function() {
    console.log("Fixing loading overlay...");
    
    // Function to hide loading overlays
    function hideAllLoadingOverlays() {
        // Hide any loading overlay by ID
        const overlayIds = ['loading_overlay', 'loading-overlay', 'global-loading-spinner', 'spinner'];
        overlayIds.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                console.log("Found loading overlay, hiding it");
                element.style.display = 'none';
            }
        });
        
        // Hide any loading overlay by class
        const overlayClasses = ['.loading-overlay', '.spinner-container', '.loading-container', '.spinner-wrapper'];
        overlayClasses.forEach(className => {
            const elements = document.querySelectorAll(className);
            elements.forEach(element => {
                element.style.display = 'none';
            });
        });
        
        // Hide any spinner elements
        const spinners = document.querySelectorAll('.spinner-border, .spinner-grow');
        spinners.forEach(spinner => {
            const parent = spinner.closest('.loading-indicator, .spinner-wrapper');
            if (parent) {
                parent.style.display = 'none';
            }
        });
        
        // Reset spinner counter if it exists
        if (typeof spinnerCounter !== 'undefined') {
            spinnerCounter = 0;
        }
        
        // Reset any spinner timeout
        if (typeof spinnerTimeout !== 'undefined' && spinnerTimeout) {
            clearTimeout(spinnerTimeout);
        }
    }

    // Initial hide
    hideAllLoadingOverlays();
    
    // Set up event listeners to ensure overlay stays hidden
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOMContentLoaded triggered");
        hideAllLoadingOverlays();
        
        // Also hide after a short delay
        setTimeout(hideAllLoadingOverlays, 500);
        setTimeout(hideAllLoadingOverlays, 1000);
        setTimeout(hideAllLoadingOverlays, 2000);
    });
    
    // Check periodically to ensure overlay remains hidden
    setInterval(hideAllLoadingOverlays, 5000);
    
    // If page is already loaded, run immediately
    if (document.readyState === 'complete' || document.readyState === 'interactive') {
        console.log("Document already loaded, hiding overlays");
        hideAllLoadingOverlays();
    }
})();