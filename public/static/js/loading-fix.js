
document.addEventListener('DOMContentLoaded', function() {
    // Hide all loading overlays
    const hideLoadingOverlays = () => {
        const overlays = document.querySelectorAll('.loading-overlay, #loading_overlay');
        overlays.forEach(overlay => {
            if (overlay) {
                overlay.style.display = 'none';
                overlay.style.visibility = 'hidden';
            }
        });
    };

    // Initial hide
    hideLoadingOverlays();

    // Observer for dynamically added elements
    const observer = new MutationObserver(hideLoadingOverlays);
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
