/**
 * Inline loading fix to be included directly in the HTML
 * This must be placed before any other scripts to ensure it runs first
 */

// Self-executing function to avoid polluting global scope
(function() {
    // Function to hide loading overlay that runs immediately 
    function hideLoadingOverlay() {
        var overlays = document.querySelectorAll('.loading-overlay, #loading-overlay, #loading_overlay');
        overlays.forEach(function(overlay) {
            if (overlay) {
                overlay.style.display = 'none';
                console.log('Hiding overlay immediately');
            }
        });
    }

    // Run immediately
    hideLoadingOverlay();
    
    // Also run when DOM is ready
    document.addEventListener('DOMContentLoaded', hideLoadingOverlay);
    
    // And again after a short delay
    setTimeout(hideLoadingOverlay, 500);
    setTimeout(hideLoadingOverlay, 1000);
})();