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
            event.preventDefault();
            
            // Basic validation
            const description = document.getElementById('description').value.trim();
            const requesterName = document.getElementById('requester_name').value.trim();
            const startDate = document.getElementById('start_date').value;
            const startTime = document.getElementById('start_time').value;
            const endDate = document.getElementById('end_date').value;
            const endTime = document.getElementById('end_time').value;
            const deadlineDate = document.getElementById('deadline_date').value;
            const deadlineTime = document.getElementById('deadline_time').value;
            
            // Check required fields
            if (!startDate || !startTime || !endDate || !endTime || !deadlineDate || !deadlineTime) {
                alert('All date and time fields are required');
                return false;
            }

            if (requesterName.length < 4) {
                alert('Requester name must be at least 4 characters long');
                return false;
            }

            if (!description) {
                alert('Event description is required');
                return false;
            }

            // Validate dates
            const startDateTime = new Date(startDate + 'T' + startTime);
            const endDateTime = new Date(endDate + 'T' + endTime);
            const deadlineDateTime = new Date(deadlineDate + 'T' + deadlineTime);
            const now = new Date();

            if (deadlineDateTime < now) {
                alert('Registration deadline cannot be in the past');
                return false;
            }

            if (startDateTime < now) {
                alert('Start date and time cannot be in the past');
                return false;
            }

            if (deadlineDateTime > startDateTime) {
                alert('Registration deadline must be before the event starts');
                return false;
            }

            // Show loading overlay
            const loadingOverlay = document.getElementById('loading_overlay');
            if (loadingOverlay) {
                loadingOverlay.style.display = 'flex';
            }

            // Disable submit button to prevent double submission
            // Clear any previous validations
            const allInputs = form.querySelectorAll('input, textarea');
            allInputs.forEach(input => {
                input.classList.remove('is-invalid');
            });

            // Validate required fields again
            let isValid = true;
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('is-invalid');
                    isValid = false;
                }
            });

            if (!isValid) {
                return false;
            }

            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';

            // Submit the form
            form.submit();
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

    // Get date and time values
    const startDateVal = document.getElementById('start_date').value;
    const startTimeVal = document.getElementById('start_time').value;
    const endDateVal = document.getElementById('end_date').value;
    const endTimeVal = document.getElementById('end_time').value;
    const deadlineDateVal = document.getElementById('deadline_date').value;
    const deadlineTimeVal = document.getElementById('deadline_time').value;
    
    // Format for proper parsing
    let startDateObj, endDateObj, deadlineDateObj;
    const now = new Date();
    
    try {
        // Parse date strings properly using local date objects
        // Format: yyyy-mm-dd hh:mm
        startDateObj = new Date(`${startDateVal} ${startTimeVal}`);
        endDateObj = new Date(`${endDateVal} ${endTimeVal}`);
        deadlineDateObj = new Date(`${deadlineDateVal} ${deadlineTimeVal}`);
        
        // Debug info to console
        console.log('Start Date:', startDateVal, startTimeVal, startDateObj);
        console.log('End Date:', endDateVal, endTimeVal, endDateObj);
        console.log('Deadline:', deadlineDateVal, deadlineTimeVal, deadlineDateObj);
    } catch (e) {
        console.error('Date parsing error:', e);
    }

    // Validate start date
    if (!startDateVal || !startTimeVal || isNaN(startDateObj.getTime())) {
        document.getElementById('start_date').classList.add('is-invalid');
        document.getElementById('start_time').classList.add('is-invalid');
        addErrorMessage(document.getElementById('start_date'), 'Please enter valid start date and time');
        isValid = false;
    }

    // Validate end date
    if (!endDateVal || !endTimeVal || isNaN(endDateObj.getTime())) {
        document.getElementById('end_date').classList.add('is-invalid');
        document.getElementById('end_time').classList.add('is-invalid');
        addErrorMessage(document.getElementById('end_date'), 'Please enter valid end date and time');
        isValid = false;
    }

    // Validate deadline date
    if (!deadlineDateVal || !deadlineTimeVal || isNaN(deadlineDateObj.getTime())) {
        document.getElementById('deadline_date').classList.add('is-invalid');
        document.getElementById('deadline_time').classList.add('is-invalid');
        addErrorMessage(document.getElementById('deadline_date'), 'Please enter valid deadline date and time');
        isValid = false;
    }

    // Check date logic only if all dates are valid
    if (isValid) {
        // End date should be equal to or after start date
        if (endDateObj < startDateObj) {
            document.getElementById('end_date').classList.add('is-invalid');
            addErrorMessage(document.getElementById('end_date'), 'End date must be after or equal to start date');
            isValid = false;
        }
        
        // Deadline must be before start date
        if (deadlineDateObj >= startDateObj) {
            document.getElementById('deadline_date').classList.add('is-invalid');
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

document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const form = document.getElementById('create_event_form');
    const startDate = document.getElementById('start_date');
    const startTime = document.getElementById('start_time');
    const endDate = document.getElementById('end_date');
    const endTime = document.getElementById('end_time');
    const deadlineDate = document.getElementById('deadline_date');
    const deadlineTime = document.getElementById('deadline_time');
    const isOnlineCheckbox = document.getElementById('is_online');
    const venueFields = document.getElementById('venue_fields');
    const imageInput = document.getElementById('event_banner');
    const imagePreview = document.getElementById('image_preview');
    const imagePreviewContainer = document.getElementById('image_preview_container');

    // Set min dates to today
    const today = new Date();
    const todayStr = today.toISOString().split('T')[0];

    startDate.min = todayStr;
    endDate.min = todayStr;
    deadlineDate.min = todayStr;

    // Event Handlers
    if (isOnlineCheckbox) {
        isOnlineCheckbox.addEventListener('change', function() {
            if (this.checked) {
                venueFields.classList.add('d-none');
            } else {
                venueFields.classList.remove('d-none');
            }
        });
    }

    // Image preview handler
    if (imageInput) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreviewContainer.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Form validation
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            // Basic validation
            const startDateTime = new Date(startDate.value + 'T' + startTime.value);
            const endDateTime = new Date(endDate.value + 'T' + endTime.value);
            const deadlineDateTime = new Date(deadlineDate.value + 'T' + deadlineTime.value);
            const now = new Date();

            if (deadlineDateTime < now) {
                alert('Registration deadline cannot be in the past');
                return;
            }

            if (startDateTime < now) {
                alert('Start date and time cannot be in the past');
                return;
            }

            // Removed the condition that endDateTime > startDateTime so dates can be on the same day

            if (deadlineDateTime > startDateTime) {
                alert('Registration deadline must be before the event starts');
                return;
            }

            // If all validation passes, submit the form
            form.submit();
        });
    }
});