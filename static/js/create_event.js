// Create Event JavaScript for PharmaEvents

document.addEventListener('DOMContentLoaded', function() {
    // Initialize date and time pickers
    flatpickr('.date-picker', {
        dateFormat: 'Y-m-d',
        allowInput: true
    });
    
    flatpickr('.time-picker', {
        enableTime: true,
        noCalendar: true,
        dateFormat: 'h:i K',
        time_24hr: false,
        allowInput: true
    });
    
    // Initialize select2 for multi-select
    if (typeof $.fn.select2 !== 'undefined') {
        $('.select2').select2({
            theme: 'bootstrap4',
            width: '100%'
        });
    }
    
    // Venue is now free text, no dropdown handling needed
    
    // Online event toggle logic
    const onlineCheckbox = document.getElementById('is_online');
    const venueFields = document.getElementById('venue_fields');
    
    if (onlineCheckbox && venueFields) {
        onlineCheckbox.addEventListener('change', function() {
            if (this.checked) {
                venueFields.classList.add('d-none');
            } else {
                venueFields.classList.remove('d-none');
            }
        });
        
        // Initial state
        if (onlineCheckbox.checked) {
            venueFields.classList.add('d-none');
        }
    }
    
    // Form validation and submission
    const form = document.getElementById('event_form');
    const submitButton = document.getElementById('submit_event_btn');
    
    if (form && submitButton) {
        // Listen for form submission
        form.addEventListener('submit', function(event) {
            // First validate the form
            if (!validateEventForm()) {
                event.preventDefault();
                return false;
            }
            
            // Show loading overlay
            const loadingOverlay = document.getElementById('loading_overlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }
            
            // Disable submit button to prevent double submission
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
            
            // Let the form submit naturally
            return true;
        });
    }
});

// Update venue options based on selected governorate
function updateVenueOptions(governorate) {
    const venueSelect = document.getElementById('venue_id');
    if (!venueSelect) return;
    
    // Disable select during update
    venueSelect.disabled = true;
    
    // Get all venue options
    const venueOptions = venueSelect.querySelectorAll('option');
    
    // Show only venues in the selected governorate
    venueOptions.forEach(option => {
        const venueGovernorate = option.getAttribute('data-governorate');
        
        if (!venueGovernorate || option.value === '') {
            // Always show the default "Select a venue" option
            option.style.display = '';
        } else if (venueGovernorate === governorate) {
            option.style.display = '';
        } else {
            option.style.display = 'none';
        }
    });
    
    // Reset selection if current selection is now hidden
    const selectedOption = venueSelect.options[venueSelect.selectedIndex];
    if (selectedOption && selectedOption.style.display === 'none') {
        venueSelect.value = '';
    }
    
    // Re-enable select
    venueSelect.disabled = false;
}

// Validate the event form
function validateEventForm() {
    let isValid = true;
    const form = document.getElementById('event_form');
    
    // Reset previous error messages
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
    
    // Check required fields
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.classList.remove('is-invalid');
        
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            addErrorMessage(field, 'This field is required');
            isValid = false;
        }
    });
    
    // Validate date ranges
    const startDate = new Date(
        document.getElementById('start_date').value + 'T' + 
        document.getElementById('start_time').value
    );
    
    const endDate = new Date(
        document.getElementById('end_date').value + 'T' + 
        document.getElementById('end_time').value
    );
    
    const deadlineDate = new Date(
        document.getElementById('deadline_date').value + 'T' + 
        document.getElementById('deadline_time').value
    );
    
    // Check if dates are valid
    if (isNaN(startDate.getTime())) {
        addErrorMessage(document.getElementById('start_date'), 'Invalid start date');
        isValid = false;
    }
    
    if (isNaN(endDate.getTime())) {
        addErrorMessage(document.getElementById('end_date'), 'Invalid end date');
        isValid = false;
    }
    
    if (isNaN(deadlineDate.getTime())) {
        addErrorMessage(document.getElementById('deadline_date'), 'Invalid deadline date');
        isValid = false;
    }
    
    // Check date logic
    if (isValid) {
        if (endDate <= startDate) {
            addErrorMessage(document.getElementById('end_date'), 'End date must be after start date');
            isValid = false;
        }
        
        if (deadlineDate >= startDate) {
            addErrorMessage(document.getElementById('deadline_date'), 'Registration deadline must be before start date');
            isValid = false;
        }
    }
    
    // Check if at least one category is selected
    const categories = document.getElementById('categories');
    if (categories && (!categories.value || categories.value.length === 0)) {
        addErrorMessage(categories, 'Please select at least one category');
        isValid = false;
    }
    
    // Check venue selection for offline events
    const isOnline = document.getElementById('is_online').checked;
    if (!isOnline) {
        const governorate = document.getElementById('governorate');
        if (!governorate.value) {
            addErrorMessage(governorate, 'Please select a governorate for offline events');
            isValid = false;
        }
    }
    
    return isValid;
}

// Add error message below a field
function addErrorMessage(field, message) {
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback error-message';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}
