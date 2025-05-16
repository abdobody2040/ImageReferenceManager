/**
 * Universal loading spinner and overlay fix
 * This script works across both PHP and Python versions of the application
 * It implements multiple strategies to ensure loading indicators disappear
 */

// Execute immediately
(function() {
    // Wait for DOM to be ready
    function ready(callback) {
        if (document.readyState === 'complete' || document.readyState === 'interactive') {
            setTimeout(callback, 1);
        } else {
            document.addEventListener('DOMContentLoaded', callback);
        }
    }

    // Universal loading overlay hiding function
    function hideAllLoadingElements() {
        console.log("Running universal loading fix...");
        
        // Common loading element IDs
        ['loading-overlay', 'loading_overlay', 'global-spinner', 'loader', 'spinner']
            .forEach(function(id) {
                var element = document.getElementById(id);
                if (element) {
                    element.style.display = 'none';
                    console.log("Hidden loading element by ID: " + id);
                }
            });
        
        // Common loading element classes
        ['.loading-overlay', '.spinner-container', '.spinner', '.loading-indicator', 
         '.loading-wrapper', '.loader', '.loading', '.loading-screen']
            .forEach(function(className) {
                var elements = document.querySelectorAll(className);
                elements.forEach(function(element) {
                    element.style.display = 'none';
                    console.log("Hidden loading element by class: " + className);
                });
            });
        
        // Reset global variables that might be used by loading systems
        if (typeof window.spinnerCounter !== 'undefined') {
            window.spinnerCounter = 0;
        }
        
        if (typeof window.loadingCounter !== 'undefined') {
            window.loadingCounter = 0;
        }
        
        // Clear any spinner/loading timeouts
        if (typeof window.spinnerTimeout !== 'undefined' && window.spinnerTimeout) {
            clearTimeout(window.spinnerTimeout);
        }
        
        if (typeof window.loadingTimeout !== 'undefined' && window.loadingTimeout) {
            clearTimeout(window.loadingTimeout);
        }
        
        // Patch any spinner show functions to do nothing
        if (typeof window.showSpinner === 'function') {
            window.originalShowSpinner = window.showSpinner;
            window.showSpinner = function() {
                console.log("Spinner show prevented");
                setTimeout(hideAllLoadingElements, 10);
                return false;
            };
        }
        
        // For Chart.js dashboards, ensure no more loading indicators appear
        if (typeof window.loadDashboardStats === 'function') {
            try {
                var dashboardContainer = document.querySelector('.dashboard-container');
                if (dashboardContainer) {
                    dashboardContainer.classList.add('loaded');
                    console.log("Dashboard marked as loaded");
                }
            } catch (e) {
                console.error("Error patching dashboard:", e);
            }
        }
    }
    
    // Run immediately
    hideAllLoadingElements();
    
    // Run when DOM is ready
    ready(function() {
        hideAllLoadingElements();
        
        // Run multiple times with delays
        [100, 500, 1000, 2000, 5000].forEach(function(delay) {
            setTimeout(hideAllLoadingElements, delay);
        });
        
        // Also run periodically
        setInterval(hideAllLoadingElements, 5000);
    });
    
    // Also patch window.onload
    var originalOnload = window.onload;
    window.onload = function(e) {
        if (originalOnload) {
            originalOnload(e);
        }
        hideAllLoadingElements();
    };
})();