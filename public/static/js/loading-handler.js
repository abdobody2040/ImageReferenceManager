/**
 * Unified Loading Handler for PharmaEvents
 * This script fixes loading overlay issues across the application
 */

(function() {
    // Execute when DOM is fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        initLoadingHandler();
    });

    // Initialize on page load to handle any existing loading overlays
    window.addEventListener('load', function() {
        console.log('Page fully loaded, checking for loading overlays');
        hideAllLoadingOverlays();
    });

    // Initialize loading handler
    function initLoadingHandler() {
        console.log('Initializing loading handler');
        
        // Create global loading overlay if it doesn't exist
        if (!document.querySelector('.loading-overlay')) {
            createGlobalLoadingOverlay();
        }
        
        // Handle all form submissions
        handleFormSubmissions();
        
        // Add manual show/hide methods to window object
        window.showLoadingOverlay = showLoadingOverlay;
        window.hideLoadingOverlay = hideLoadingOverlay;
        
        // Set timeout to automatically hide any loading overlays
        setupAutomaticTimeout();
    }

    // Create a global loading overlay
    function createGlobalLoadingOverlay() {
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.id = 'loading_overlay';
        overlay.style.display = 'none';
        overlay.style.position = 'fixed';
        overlay.style.top = '0';
        overlay.style.left = '0';
        overlay.style.width = '100%';
        overlay.style.height = '100%';
        overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
        overlay.style.zIndex = '9999';
        overlay.style.justifyContent = 'center';
        overlay.style.alignItems = 'center';
        
        const spinner = document.createElement('div');
        spinner.className = 'spinner-border text-light';
        spinner.setAttribute('role', 'status');
        
        const span = document.createElement('span');
        span.className = 'visually-hidden';
        span.textContent = 'Loading...';
        
        spinner.appendChild(span);
        overlay.appendChild(spinner);
        document.body.appendChild(overlay);
        
        console.log('Created global loading overlay');
    }

    // Handle all form submissions to show loading overlay
    function handleFormSubmissions() {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                // Don't show loading for forms with data-no-loading attribute
                if (this.getAttribute('data-no-loading') === 'true') {
                    return;
                }
                
                // Show loading overlay
                showLoadingOverlay();
                
                // Disable submit buttons
                this.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(button => {
                    button.disabled = true;
                    if (button.tagName === 'BUTTON') {
                        button.dataset.originalText = button.innerHTML;
                        button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                    }
                });
            });
        });
    }

    // Show loading overlay
    function showLoadingOverlay() {
        console.log('Showing loading overlay');
        const overlay = document.querySelector('.loading-overlay');
        if (overlay) {
            overlay.style.display = 'flex';
        } else {
            console.warn('Loading overlay not found, creating one');
            createGlobalLoadingOverlay();
            document.querySelector('.loading-overlay').style.display = 'flex';
        }
    }

    // Hide loading overlay
    function hideLoadingOverlay() {
        console.log('Hiding loading overlay');
        const overlays = document.querySelectorAll('.loading-overlay, #loading_overlay');
        overlays.forEach(overlay => {
            if (overlay) {
                overlay.style.display = 'none';
            }
        });
        
        // Re-enable all submit buttons
        document.querySelectorAll('button[type="submit"][disabled], input[type="submit"][disabled]').forEach(button => {
            button.disabled = false;
            if (button.tagName === 'BUTTON' && button.dataset.originalText) {
                button.innerHTML = button.dataset.originalText;
            }
        });
    }

    // Hide all loading overlays
    function hideAllLoadingOverlays() {
        // Hide by ID
        const loadingById = document.getElementById('loading_overlay');
        if (loadingById) {
            loadingById.style.display = 'none';
            console.log('Hidden loading element by ID: loading_overlay');
        }
        
        // Hide by class
        const loadingByClass = document.querySelectorAll('.loading-overlay');
        if (loadingByClass.length > 0) {
            loadingByClass.forEach(el => {
                el.style.display = 'none';
            });
            console.log('Hidden loading element by class: .loading-overlay');
        }
        
        // Also hide any spinner elements
        const spinners = document.querySelectorAll('.spinner-border, .spinner-grow');
        spinners.forEach(spinner => {
            const parent = spinner.parentElement;
            if (parent && parent.classList.contains('loading-overlay')) {
                parent.style.display = 'none';
            }
        });
    }

    // Set up automatic timeout to hide loading overlay
    function setupAutomaticTimeout() {
        window.loadingTimeoutId = setTimeout(function() {
            console.log('Loading timeout triggered, hiding all overlays');
            hideAllLoadingOverlays();
        }, 15000); // 15 seconds timeout
    }

    // Expose these functions globally
    window.hideAllLoadingOverlays = hideAllLoadingOverlays;
    window.showLoadingOverlay = showLoadingOverlay;
    window.hideLoadingOverlay = hideLoadingOverlay;
})();