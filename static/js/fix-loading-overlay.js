/**
 * Emergency fix for loading overlay issue
 * This script ensures the loading overlay is hidden
 */

// Hide loading overlay immediately when the script loads
(function() {
    console.log("Fixing loading overlay...");
    
    // Function to hide loading overlays
    function hideAllLoadingOverlays() {
        // Hide the main loading overlay
        const loadingOverlay = document.getElementById('loading_overlay');
        if (loadingOverlay) {
            console.log("Found loading overlay, hiding it");
            loadingOverlay.style.display = 'none';
        }
        
        // Hide any custom spinner
        const globalSpinner = document.getElementById('global-loading-spinner');
        if (globalSpinner) {
            console.log("Found global spinner, hiding it");
            globalSpinner.style.display = 'none';
        }
        
        // Reset spinner counter if it exists
        if (typeof spinnerCounter !== 'undefined') {
            spinnerCounter = 0;
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