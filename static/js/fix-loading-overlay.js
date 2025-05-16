// Single unified loading overlay fix
(function() {
    function hideLoadingOverlays() {
        const overlays = document.querySelectorAll('.loading-overlay, #loading-overlay, #loading_overlay, .spinner-container');
        overlays.forEach(overlay => {
            if (overlay) overlay.style.display = 'none';
        });
    }

    // Run immediately and after DOM loads
    hideLoadingOverlays();
    document.addEventListener('DOMContentLoaded', hideLoadingOverlays);
})();