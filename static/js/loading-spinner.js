/**
 * Loading spinner utility
 * Compatible with both the custom overlay and the pre-existing loading_overlay
 */

// Global spinner counter to manage multiple concurrent loading states
let spinnerCounter = 0;

// Show loading spinner
function showSpinner(message = 'Please wait while we process your request...') {
    // Increment the counter
    spinnerCounter++;
    
    // First, check if the app's existing loading overlay exists
    const existingOverlay = document.getElementById('loading_overlay');
    if (existingOverlay) {
        // Update message if possible
        const messageElement = existingOverlay.querySelector('.loading-message');
        if (messageElement) {
            messageElement.textContent = message;
        }
        existingOverlay.style.display = 'flex';
        return; // Use the existing overlay
    }
    
    // Check if our custom spinner already exists
    let spinner = document.getElementById('global-loading-spinner');
    
    if (!spinner) {
        // Create spinner element if it doesn't exist
        spinner = document.createElement('div');
        spinner.id = 'global-loading-spinner';
        spinner.className = 'loading-overlay';
        spinner.style.position = 'fixed';
        spinner.style.top = '0';
        spinner.style.left = '0';
        spinner.style.width = '100%';
        spinner.style.height = '100%';
        spinner.style.background = 'rgba(255, 255, 255, 0.8)';
        spinner.style.display = 'flex';
        spinner.style.justifyContent = 'center';
        spinner.style.alignItems = 'center';
        spinner.style.zIndex = '9999';
        
        spinner.innerHTML = `
            <div class="spinner-container" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); text-align: center; max-width: 90%;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-center" id="spinner-message">${message}</p>
            </div>
        `;
        document.body.appendChild(spinner);
    } else {
        // Update message if spinner already exists
        const messageElement = spinner.querySelector('#spinner-message');
        if (messageElement) {
            messageElement.textContent = message;
        }
        spinner.style.display = 'flex';
    }
}

// Hide loading spinner
function hideSpinner() {
    // Decrement the counter
    spinnerCounter--;
    
    // Only hide spinners if counter reaches zero
    if (spinnerCounter <= 0) {
        spinnerCounter = 0;
        
        // Hide the existing app overlay if it exists
        const existingOverlay = document.getElementById('loading_overlay');
        if (existingOverlay) {
            existingOverlay.style.display = 'none';
        }
        
        // Hide our custom spinner if it exists
        const spinner = document.getElementById('global-loading-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    }
}

// Force hide all spinners regardless of counter
function forceHideSpinner() {
    spinnerCounter = 0;
    
    // Force hide the existing app overlay if it exists
    const existingOverlay = document.getElementById('loading_overlay');
    if (existingOverlay) {
        existingOverlay.style.display = 'none';
    }
    
    // Force hide our custom spinner if it exists
    const spinner = document.getElementById('global-loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
}

// Add timeout to automatically hide spinner after a maximum wait time
function setupSpinnerTimeout(maxWaitTime = 15000) {
    setTimeout(() => {
        // Check if either spinner is still showing
        const existingOverlay = document.getElementById('loading_overlay');
        const spinner = document.getElementById('global-loading-spinner');
        
        if ((existingOverlay && existingOverlay.style.display !== 'none') || 
            (spinner && spinner.style.display !== 'none')) {
            forceHideSpinner();
            
            // Show error message if showErrorMessage function exists
            if (typeof showErrorMessage === 'function') {
                showErrorMessage('The operation is taking longer than expected. Please try again later.');
            } else {
                console.error('The operation is taking longer than expected.');
                
                // Create fallback error alert
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-danger alert-dismissible fade show';
                alertDiv.style.position = 'fixed';
                alertDiv.style.bottom = '20px';
                alertDiv.style.right = '20px';
                alertDiv.style.zIndex = '9999';
                alertDiv.innerHTML = `
                    <strong>Error!</strong> The operation is taking longer than expected. Please try again later.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                document.body.appendChild(alertDiv);
                
                // Auto-remove after 10 seconds
                setTimeout(() => {
                    alertDiv.remove();
                }, 10000);
            }
        }
    }, maxWaitTime);
}