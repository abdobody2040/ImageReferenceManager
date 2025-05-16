/**
 * Emergency fix for all loading and chart issues
 */

// Immediately execute when script loads
(function() {
    // This will run before page loads fully
    console.log("Running emergency fix script...");
    
    // Fix 1: Hide any loading overlays immediately
    function hideAllLoadingElements() {
        const selectors = [
            '#loading-overlay', '.loading-overlay', 
            '#global-loading-spinner', '.spinner-container',
            '.loading-indicator', '.loader'
        ];
        
        selectors.forEach(selector => {
            const elements = document.querySelectorAll(selector);
            if (elements.length > 0) {
                elements.forEach(el => {
                    el.style.display = 'none';
                    console.log(`Hiding element: ${selector}`);
                });
            }
        });
        
        // Also reset any spinner counters
        window.spinnerCounter = 0;
        
        // Clear any spinner timeouts
        if (window.spinnerTimeout) {
            clearTimeout(window.spinnerTimeout);
            window.spinnerTimeout = null;
        }
    }
    
    // Run immediately
    hideAllLoadingElements();
    
    // Run when DOM loads
    document.addEventListener('DOMContentLoaded', function() {
        console.log("DOM content loaded - applying fixes");
        
        // Hide loading again
        hideAllLoadingElements();
        
        // Run multiple times
        setTimeout(hideAllLoadingElements, 500);
        setTimeout(hideAllLoadingElements, 1000);
        setTimeout(hideAllLoadingElements, 2000);
        
        // Apply chart fixes if we're on dashboard
        if (document.querySelector('.dashboard-container')) {
            console.log("Dashboard detected, fixing charts");
            
            // Fix Chart.js integration
            if (window.Chart) {
                console.log("Chart.js detected, applying compatibility patches");
                
                // Force valid defaults
                Chart.defaults.maintainAspectRatio = false;
                Chart.defaults.responsive = true;
                
                // Add error handling
                const originalAcquireContext = Chart.prototype.acquireContext;
                Chart.prototype.acquireContext = function(canvas, options) {
                    try {
                        return originalAcquireContext.call(this, canvas, options);
                    } catch (e) {
                        console.error("Chart.js error:", e);
                        const container = canvas.parentNode;
                        if (container) {
                            container.innerHTML = '<div class="alert alert-warning">Chart could not be rendered</div>';
                        }
                        return null;
                    }
                };
            }
        }
    });
    
    // Also run on window load (when all resources loaded)
    window.addEventListener('load', hideAllLoadingElements);
    
    // Run periodically just to be sure
    setInterval(hideAllLoadingElements, 5000);
    
    // Add error handling for potential AJAX errors
    window.addEventListener('error', function(e) {
        console.log("Caught error:", e.message);
        hideAllLoadingElements();
    });
})();