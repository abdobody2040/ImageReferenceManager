/**
 * Unified Loading Handler for PharmaEvents
 * This script provides a consistent way to handle loading overlays across the application
 */

(function() {
    // Execute when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Initializing unified loading handler...');
        initLoadingHandler();
        
        // Also handle any existing loading overlays
        hideAllLoadingOverlays();
    });
    
    // Initialize loading handler
    function initLoadingHandler() {
        // Create global loading overlay if it doesn't exist
        createGlobalLoadingOverlay();
        
        // Handle form submissions
        setupFormSubmissionHandlers();
        
        // Expose methods globally
        window.showLoading = showLoading;
        window.hideLoading = hideLoading;
        window.hideAllLoadingOverlays = hideAllLoadingOverlays;
    }
    
    // Create a global loading overlay element
    function createGlobalLoadingOverlay() {
        // Only create if it doesn't already exist
        if (!document.querySelector('#global_loading_overlay')) {
            const overlay = document.createElement('div');
            overlay.id = 'global_loading_overlay';
            overlay.className = 'loading-overlay';
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
            
            console.log('Global loading overlay created');
        }
    }
    
    // Set up handlers for all form submissions
    function setupFormSubmissionHandlers() {
        document.querySelectorAll('form').forEach(form => {
            // Skip forms that have already been handled or that have skip-loading attribute
            if (form.getAttribute('data-loading-handled') === 'true' || 
                form.getAttribute('data-skip-loading') === 'true') {
                return;
            }
            
            form.setAttribute('data-loading-handled', 'true');
            
            form.addEventListener('submit', function(e) {
                // Only handle forms that don't have custom handlers
                if (this.getAttribute('data-custom-submit') === 'true') {
                    return;
                }
                
                // Show loading overlay
                showLoading();
                
                // Disable submit buttons
                this.querySelectorAll('button[type="submit"], input[type="submit"]').forEach(button => {
                    if (!button.disabled) {
                        button.disabled = true;
                        
                        if (button.tagName === 'BUTTON') {
                            button.setAttribute('data-original-html', button.innerHTML);
                            button.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                        }
                    }
                });
            });
        });
    }
    
    // Show loading overlay
    function showLoading() {
        console.log('Showing loading overlay');
        
        // Try to use the global overlay first
        const globalOverlay = document.querySelector('#global_loading_overlay');
        if (globalOverlay) {
            globalOverlay.style.display = 'flex';
            return;
        }
        
        // If no global overlay, try to use any existing overlay
        const existingOverlay = document.querySelector('.loading-overlay');
        if (existingOverlay) {
            existingOverlay.style.display = 'flex';
            return;
        }
        
        // If no overlay exists, create one
        createGlobalLoadingOverlay();
        document.querySelector('#global_loading_overlay').style.display = 'flex';
    }
    
    // Hide loading overlay
    function hideLoading() {
        console.log('Hiding loading overlay');
        
        // Hide all loading overlays to be thorough
        document.querySelectorAll('.loading-overlay, #global_loading_overlay, #loading_overlay').forEach(overlay => {
            if (overlay) {
                overlay.style.display = 'none';
            }
        });
        
        // Re-enable all submit buttons
        document.querySelectorAll('button[type="submit"][disabled], input[type="submit"][disabled]').forEach(button => {
            button.disabled = false;
            
            if (button.tagName === 'BUTTON' && button.hasAttribute('data-original-html')) {
                button.innerHTML = button.getAttribute('data-original-html');
            }
        });
    }
    
    // Hide all loading overlays (more aggressive approach)
    function hideAllLoadingOverlays() {
        console.log('Hiding all loading elements...');
        
        // Hide by ID
        ['loading_overlay', 'global_loading_overlay', 'loadingOverlay'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.style.display = 'none';
                console.log(`Hidden loading element by ID: ${id}`);
            }
        });
        
        // Hide by class
        ['.loading-overlay', '.spinner-container', '.loading'].forEach(className => {
            document.querySelectorAll(className).forEach(element => {
                element.style.display = 'none';
            });
            console.log(`Hidden loading elements by class: ${className}`);
        });
        
        // Re-enable all submit buttons
        document.querySelectorAll('button[disabled], input[disabled]').forEach(button => {
            if (button.type === 'submit' || button.type === 'button') {
                button.disabled = false;
                
                if (button.tagName === 'BUTTON' && button.hasAttribute('data-original-html')) {
                    button.innerHTML = button.getAttribute('data-original-html');
                }
            }
        });
        
        console.log('All loading elements should be hidden now');
    }
    
    // Set up automatic timeout to hide loading overlays after 15 seconds
    setTimeout(function() {
        hideAllLoadingOverlays();
        console.log('Automatic timeout reached - all loading elements hidden');
    }, 15000);
})();