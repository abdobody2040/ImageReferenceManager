/**
 * Loading spinner utility
 */

// Global spinner counter to manage multiple concurrent loading states
let spinnerCounter = 0;

// Show loading spinner
function showSpinner(message = 'Please wait while we process your request...') {
    // Increment the counter
    spinnerCounter++;
    
    // Check if spinner already exists
    let spinner = document.getElementById('global-loading-spinner');
    
    if (!spinner) {
        // Create spinner element if it doesn't exist
        spinner = document.createElement('div');
        spinner.id = 'global-loading-spinner';
        spinner.className = 'loading-overlay';
        spinner.innerHTML = `
            <div class="spinner-container">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-3 text-center" id="spinner-message">${message}</p>
            </div>
        `;
        document.body.appendChild(spinner);
        
        // Add styles if not already in CSS
        if (!document.getElementById('spinner-styles')) {
            const style = document.createElement('style');
            style.id = 'spinner-styles';
            style.textContent = `
                .loading-overlay {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(255, 255, 255, 0.8);
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    z-index: 9999;
                }
                .spinner-container {
                    background: white;
                    padding: 30px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    text-align: center;
                    max-width: 90%;
                }
            `;
            document.head.appendChild(style);
        }
    } else {
        // Update message if spinner already exists
        document.getElementById('spinner-message').textContent = message;
        spinner.style.display = 'flex';
    }
}

// Hide loading spinner
function hideSpinner() {
    // Decrement the counter
    spinnerCounter--;
    
    // Only hide spinner if counter reaches zero
    if (spinnerCounter <= 0) {
        spinnerCounter = 0;
        const spinner = document.getElementById('global-loading-spinner');
        if (spinner) {
            spinner.style.display = 'none';
        }
    }
}

// Force hide spinner regardless of counter
function forceHideSpinner() {
    spinnerCounter = 0;
    const spinner = document.getElementById('global-loading-spinner');
    if (spinner) {
        spinner.style.display = 'none';
    }
}

// Add timeout to automatically hide spinner after a maximum wait time
function setupSpinnerTimeout(maxWaitTime = 15000) {
    setTimeout(() => {
        if (document.getElementById('global-loading-spinner')?.style.display !== 'none') {
            forceHideSpinner();
            showErrorMessage('The operation is taking longer than expected. Please try again later.');
        }
    }, maxWaitTime);
}