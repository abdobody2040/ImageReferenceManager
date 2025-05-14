/**
 * Main JavaScript functionality for PharmaEvents
 * Enhanced with better error handling and form validation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize UI components
    initializeSidebar();
    initializeScrollToTop();
    
    // Initialize form functionality
    initFormToggles();
    initImageUploadPreview();
    initDatetimePickers();
    initConfirmationDialogs();
    
    // Initialize form validation for all forms with needs-validation class
    initFormValidation();
    
    // Initialize auto-dismissible alerts
    initAutoDismissAlerts();
    
    // Handle AJAX errors globally
    setupGlobalAjaxErrorHandling();
});

// Form field toggle functions
function initFormToggles() {
    // Toggle venue fields based on event format
    const isOnlineCheckbox = document.getElementById('is_online');
    if (isOnlineCheckbox) {
        toggleVenueFields();
        isOnlineCheckbox.addEventListener('change', toggleVenueFields);
    }
    
    // Toggle image source fields
    const imageSourceRadios = document.querySelectorAll('input[name="image_source"]');
    if (imageSourceRadios.length > 0) {
        toggleImageFields();
        imageSourceRadios.forEach(radio => {
            radio.addEventListener('change', toggleImageFields);
        });
    }
}

// Toggle venue fields based on event format (online/in-person)
function toggleVenueFields() {
    const isOnline = document.getElementById('is_online').checked;
    const venueFields = document.getElementById('venue-fields');
    
    if (venueFields) {
        if (isOnline) {
            venueFields.style.display = 'none';
            // Make venue fields not required
            document.querySelectorAll('#venue-fields input, #venue-fields select').forEach(el => {
                if (el.hasAttribute('required')) {
                    el.setAttribute('data-required', 'true');
                    el.removeAttribute('required');
                }
            });
        } else {
            venueFields.style.display = 'block';
            // Restore required attribute
            document.querySelectorAll('#venue-fields input[data-required="true"], #venue-fields select[data-required="true"]').forEach(el => {
                el.setAttribute('required', '');
            });
        }
    }
}

// Toggle image fields based on image source (url/file)
function toggleImageFields() {
    const imageSource = document.querySelector('input[name="image_source"]:checked')?.value;
    const urlField = document.getElementById('image-url-field');
    const fileField = document.getElementById('image-file-field');
    
    if (urlField && fileField) {
        if (imageSource === 'url') {
            urlField.style.display = 'block';
            fileField.style.display = 'none';
            document.getElementById('image_url').setAttribute('required', '');
            document.getElementById('image_file').removeAttribute('required');
        } else if (imageSource === 'file') {
            urlField.style.display = 'none';
            fileField.style.display = 'block';
            document.getElementById('image_url').removeAttribute('required');
            document.getElementById('image_file').setAttribute('required', '');
        } else {
            // No image
            urlField.style.display = 'none';
            fileField.style.display = 'none';
            document.getElementById('image_url').removeAttribute('required');
            document.getElementById('image_file').removeAttribute('required');
        }
    }
}

// Initialize image upload preview
function initImageUploadPreview() {
    const imageInput = document.getElementById('image_file');
    const previewContainer = document.getElementById('image-preview');
    
    if (imageInput && previewContainer) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" class="img-preview">`;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.innerHTML = '';
                previewContainer.style.display = 'none';
            }
        });
    }
}

// Initialize datetime pickers
function initDatetimePickers() {
    // This is a placeholder for integrating a datetime picker library
    // In a real implementation, you would initialize a library like flatpickr here
}

// Initialize confirmation dialogs
function initConfirmationDialogs() {
    const confirmLinks = document.querySelectorAll('.confirm-action');
    
    confirmLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-confirm') || 'Are you sure you want to perform this action?';
            
            if (confirm(message)) {
                window.location = this.getAttribute('href');
            }
        });
    });
}

// Show loading indicator (legacy method, prefer loading-spinner.js)
function showLoading() {
    if (typeof showSpinner === 'function') {
        // Use the newer loading spinner system if available
        showSpinner();
        return;
    }
    
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(loadingOverlay);
    
    // Add CSS for loading overlay if not already in styles.css
    const style = document.createElement('style');
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
    `;
    document.head.appendChild(style);
}

// Define showSpinner as a fallback if loading-spinner.js isn't loaded
if (typeof showSpinner === 'undefined') {
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
}

// Format date for display
function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return '';
    
    const date = new Date(dateTimeStr);
    return date.toLocaleString('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// Format date for input fields
function formatDateForInput(date) {
    if (!date) return '';
    
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    
    return `${year}-${month}-${day}`;
}

// Format time for input fields
function formatTimeForInput(date) {
    if (!date) return '';
    
    const d = new Date(date);
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return `${hours}:${minutes}`;
}

// Show confirmation dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// Initialize sidebar functionality
function initializeSidebar() {
    // Toggle sidebar
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarToggleTop = document.getElementById('sidebarToggleTop');
    
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
            document.querySelector('.sidebar').classList.toggle('toggled');
        });
    }
    
    if (sidebarToggleTop) {
        sidebarToggleTop.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-toggled');
            document.querySelector('.sidebar').classList.toggle('toggled');
        });
    }
    
    // Close sidebar when window is less than 768px
    const mediaQuery = window.matchMedia('(max-width: 768px)');
    function handleScreenChange(e) {
        if (e.matches && document.querySelector('.sidebar')) {
            document.querySelector('.sidebar').classList.add('toggled');
        }
    }
    mediaQuery.addEventListener('change', handleScreenChange);
    handleScreenChange(mediaQuery);
}

// Initialize scroll to top functionality
function initializeScrollToTop() {
    const scrollToTopButton = document.querySelector('.scroll-to-top');
    if (scrollToTopButton) {
        // Show/hide scroll to top button based on scroll position
        window.addEventListener('scroll', function() {
            if (window.pageYOffset > 100) {
                scrollToTopButton.style.display = 'block';
            } else {
                scrollToTopButton.style.display = 'none';
            }
        });
        
        // Smooth scroll to top
        scrollToTopButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
}

// Initialize form validation
function initFormValidation() {
    // Fetch all forms that need validation
    const forms = document.querySelectorAll('.needs-validation');
    
    // Loop over them and prevent submission
    Array.from(forms).forEach(form => {
        form.addEventListener('submit', event => {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Find the first invalid element and focus it
                const invalidElement = form.querySelector(':invalid');
                if (invalidElement) {
                    invalidElement.focus();
                    
                    // If the element is inside a collapsed section, expand it
                    const section = invalidElement.closest('.collapse:not(.show)');
                    if (section) {
                        const trigger = document.querySelector(`[data-bs-toggle="collapse"][data-bs-target="#${section.id}"]`);
                        if (trigger) {
                            trigger.click();
                        }
                    }
                }
            }
            
            form.classList.add('was-validated');
        }, false);
    });
}

// Initialize auto-dismiss for alerts
function initAutoDismissAlerts() {
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    alerts.forEach(alert => {
        if (!alert.classList.contains('alert-danger')) {
            // Auto-dismiss non-error alerts after 5 seconds
            setTimeout(() => {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                } else {
                    alert.remove();
                }
            }, 5000);
        }
    });
}

// Setup global AJAX error handling
function setupGlobalAjaxErrorHandling() {
    // Intercept all fetch requests to handle errors
    const originalFetch = window.fetch;
    window.fetch = function() {
        return originalFetch.apply(this, arguments)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Network response was not ok: ${response.status} ${response.statusText}`);
                }
                return response;
            })
            .catch(error => {
                console.error('Fetch error:', error);
                showErrorMessage('Network error occurred. Please try again later.');
                throw error;
            });
    };
}

// Show error message in a toast or alert
function showErrorMessage(message) {
    // Create alert element
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-danger alert-dismissible fade show position-fixed bottom-0 end-0 m-3';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        <strong>Error!</strong> ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Add to document
    document.body.appendChild(alertDiv);
    
    // Remove after 10 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 10000);
}