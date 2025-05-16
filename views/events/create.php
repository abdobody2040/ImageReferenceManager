<?php
$pageTitle = 'Create Event - ' . getSetting('app_name', 'PharmaEvents');
$app_name = getSetting('app_name', 'PharmaEvents');

ob_start();
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Create Event</h1>
        <a href="/events" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Events
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Event Details</h6>
        </div>
        <div class="card-body">
            <form action="/events/create" method="post" enctype="multipart/form-data" class="needs-validation" id="event_form" novalidate>
                <!-- Loading overlay -->
                <div class="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
                    <div class="spinner-border text-light" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                
                <!-- Basic Information -->
                <div class="mb-4">
                    <h5>Basic Information</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Event Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required
                                value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            <div class="invalid-feedback">Please provide an event name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="requester_name" class="form-label">Requester Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="requester_name" name="requester_name" required
                                value="<?php echo isset($_POST['requester_name']) ? htmlspecialchars($_POST['requester_name']) : ''; ?>">
                            <div class="invalid-feedback">Please provide a requester name (at least 4 characters).</div>
                        </div>
                        <div class="col-md-6">
                            <label for="event_type_id" class="form-label">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="event_type_id" name="event_type_id" required>
                                <option value="">Select Event Type</option>
                                <?php foreach ($event_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>"><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select an event type.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="categories" class="form-label">Categories</label>
                            <select class="form-select" id="categories" name="categories[]" multiple>
                                <?php foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Hold Ctrl (or Cmd) to select multiple categories.</small>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1">
                                <label class="form-check-label" for="is_online">This is an online event</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Date and Time -->
                <div class="mb-4">
                    <h5>Date and Time</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                            <div class="invalid-feedback">Please provide a start date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" required>
                            <div class="invalid-feedback">Please provide a start time.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                            <div class="invalid-feedback">Please provide an end date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" required>
                            <div class="invalid-feedback">Please provide an end time.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="registration_deadline_date" class="form-label">Registration Deadline Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="registration_deadline_date" name="registration_deadline_date" required>
                            <div class="invalid-feedback">Please provide a registration deadline date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="registration_deadline_time" class="form-label">Registration Deadline Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="registration_deadline_time" name="registration_deadline_time" required>
                            <div class="invalid-feedback">Please provide a registration deadline time.</div>
                        </div>
                    </div>
                </div>

                <!-- Venue Information (hidden if online event) -->
                <div class="mb-4" id="venue-fields">
                    <h5>Venue Information</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="governorate" class="form-label">Governorate <span class="text-danger">*</span></label>
                            <select class="form-select" id="governorate" name="governorate" data-required="true">
                                <option value="">Select Governorate</option>
                                <?php foreach ($governorates as $governorate): ?>
                                    <option value="<?php echo $governorate; ?>"><?php echo $governorate; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a governorate.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="venue_id" class="form-label">Venue <span class="text-danger">*</span></label>
                            <select class="form-select" id="venue_id" name="venue_id" data-required="true">
                                <option value="">Select Venue</option>
                                <?php foreach ($venues as $venue): ?>
                                    <option value="<?php echo $venue['id']; ?>" data-governorate="<?php echo $venue['governorate']; ?>"><?php echo htmlspecialchars($venue['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a venue.</div>
                        </div>
                    </div>
                </div>

                <!-- Services and Support -->
                <div class="mb-4">
                    <h5>Services and Support</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="service_request_id" class="form-label">Service Request</label>
                            <select class="form-select" id="service_request_id" name="service_request_id">
                                <option value="">None</option>
                                <?php foreach ($service_requests as $service): ?>
                                    <option value="<?php echo $service['id']; ?>"><?php echo htmlspecialchars($service['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="employee_code_id" class="form-label">Employee Code</label>
                            <select class="form-select" id="employee_code_id" name="employee_code_id">
                                <option value="">None</option>
                                <?php foreach ($employee_codes as $code): ?>
                                    <option value="<?php echo $code['id']; ?>"><?php echo htmlspecialchars($code['code'] . ' - ' . $code['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Event Image -->
                <div class="mb-4">
                    <h5>Event Image</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_source" id="image_source_none" value="none" checked>
                                <label class="form-check-label" for="image_source_none">No Image</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_source" id="image_source_url" value="url">
                                <label class="form-check-label" for="image_source_url">Image URL</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_source" id="image_source_file" value="file">
                                <label class="form-check-label" for="image_source_file">Upload Image</label>
                            </div>
                        </div>
                        <div class="col-md-12" id="image-url-field" style="display: none;">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://example.com/image.jpg">
                            <div class="invalid-feedback">Please provide a valid URL.</div>
                        </div>
                        <div class="col-md-12" id="image-file-field" style="display: none;">
                            <label for="image_file" class="form-label">Image File</label>
                            <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                            <div class="invalid-feedback">Please provide a valid image file.</div>
                            <div id="image-preview" style="display: none;" class="mt-2"></div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h5>Description</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">Event Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="reset" class="btn btn-secondary me-md-2">Reset</button>
                    <button type="submit" class="btn btn-primary" id="submit_event_btn">Create Event</button>
                </div>
            </form>
            
            <!-- Event form validation script -->
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('event_form');
                const submitButton = document.getElementById('submit_event_btn');
                const venueFields = document.getElementById('venue-fields');
                const isOnlineCheckbox = document.getElementById('is_online');
                
                // Initialize form behaviors
                initFormBehaviors();
                
                // Handle form submission
                if (form && submitButton) {
                    form.addEventListener('submit', async function(e) {
                        e.preventDefault();
                        
                        try {
                            // Validate form
                            if (!validateEventForm()) {
                                return false;
                            }
                            
                            // Show loading overlay
                            const loadingOverlay = document.querySelector('.loading-overlay');
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'flex';
                                console.log('Loading overlay displayed');
                            }
                            
                            // Disable submit button
                            submitButton.disabled = true;
                            submitButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> Processing...';
                            
                            // Submit the form after a slight delay to ensure the loading overlay shows
                            setTimeout(() => {
                                form.submit();
                            }, 100);
                        } catch (error) {
                            console.error('Form submission error:', error);
                            alert('An error occurred while submitting the form. Please try again.');
                            
                            // Hide loading overlay and restore button in case of error
                            const loadingOverlay = document.querySelector('.loading-overlay');
                            if (loadingOverlay) {
                                loadingOverlay.style.display = 'none';
                            }
                            
                            submitButton.disabled = false;
                            submitButton.innerHTML = 'Create Event';
                        }
                    });
                }
                
                // Initialize form behaviors
                function initFormBehaviors() {
                    // Handle online event toggle
                    if (isOnlineCheckbox && venueFields) {
                        isOnlineCheckbox.addEventListener('change', function() {
                            toggleVenueFields();
                        });
                        
                        // Initial state
                        toggleVenueFields();
                    }
                    
                    // Image source toggle
                    const imageSourceRadios = document.querySelectorAll('input[name="image_source"]');
                    const imageUrlField = document.getElementById('image-url-field');
                    const imageFileField = document.getElementById('image-file-field');
                    
                    imageSourceRadios.forEach(radio => {
                        radio.addEventListener('change', function() {
                            if (this.value === 'url') {
                                imageUrlField.style.display = 'block';
                                imageFileField.style.display = 'none';
                            } else if (this.value === 'file') {
                                imageUrlField.style.display = 'none';
                                imageFileField.style.display = 'block';
                            } else {
                                imageUrlField.style.display = 'none';
                                imageFileField.style.display = 'none';
                            }
                        });
                    });
                    
                    // Governorate change handler
                    const governorateSelect = document.getElementById('governorate');
                    if (governorateSelect) {
                        governorateSelect.addEventListener('change', function() {
                            const selectedGovernorate = this.value;
                            updateVenueOptions(selectedGovernorate);
                        });
                    }
                    
                    // Image upload preview
                    const imageFileInput = document.getElementById('image_file');
                    const imagePreview = document.getElementById('image-preview');
                    
                    if (imageFileInput && imagePreview) {
                        imageFileInput.addEventListener('change', function() {
                            const file = this.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = function(e) {
                                    imagePreview.innerHTML = `<img src="${e.target.result}" class="img-thumbnail" style="max-width: 300px; max-height: 200px;">`;
                                    imagePreview.style.display = 'block';
                                };
                                reader.readAsDataURL(file);
                            } else {
                                imagePreview.innerHTML = '';
                                imagePreview.style.display = 'none';
                            }
                        });
                    }
                    
                    // Set min date to today
                    const dateInputs = document.querySelectorAll('input[type="date"]');
                    const today = new Date().toISOString().split('T')[0];
                    dateInputs.forEach(input => {
                        input.min = today;
                    });
                }
                
                // Toggle venue fields based on online event checkbox
                function toggleVenueFields() {
                    if (isOnlineCheckbox.checked) {
                        venueFields.style.display = 'none';
                        // Remove required attribute from venue fields
                        const venueRequiredFields = venueFields.querySelectorAll('[data-required="true"]');
                        venueRequiredFields.forEach(field => {
                            field.removeAttribute('required');
                        });
                    } else {
                        venueFields.style.display = 'block';
                        // Add required attribute to venue fields
                        const venueRequiredFields = venueFields.querySelectorAll('[data-required="true"]');
                        venueRequiredFields.forEach(field => {
                            field.setAttribute('required', '');
                        });
                    }
                }
                
                // Update venue options based on selected governorate
                function updateVenueOptions(governorate) {
                    const venueSelect = document.getElementById('venue_id');
                    if (!venueSelect) return;
                    
                    // Reset selection
                    venueSelect.value = '';
                    
                    // Hide/show options based on governorate
                    Array.from(venueSelect.options).forEach(option => {
                        if (option.value === '') return; // Skip placeholder option
                        
                        const optionGovernorate = option.getAttribute('data-governorate');
                        if (governorate === '' || optionGovernorate === governorate) {
                            option.style.display = '';
                        } else {
                            option.style.display = 'none';
                        }
                    });
                }
                
                // Validate event form before submission
                function validateEventForm() {
                    let isValid = true;
                    
                    // Reset previous validation state
                    form.querySelectorAll('.is-invalid').forEach(element => {
                        element.classList.remove('is-invalid');
                    });
                    
                    // Validate required fields
                    form.querySelectorAll('[required]').forEach(field => {
                        if (!field.value.trim()) {
                            field.classList.add('is-invalid');
                            isValid = false;
                        }
                    });
                    
                    // Validate requester name length
                    const requesterName = document.getElementById('requester_name');
                    if (requesterName && requesterName.value.trim().length < 4) {
                        requesterName.classList.add('is-invalid');
                        isValid = false;
                    }
                    
                    // Validate dates
                    const startDate = document.getElementById('start_date').value;
                    const startTime = document.getElementById('start_time').value;
                    const endDate = document.getElementById('end_date').value;
                    const endTime = document.getElementById('end_time').value;
                    const deadlineDate = document.getElementById('registration_deadline_date').value;
                    const deadlineTime = document.getElementById('registration_deadline_time').value;
                    
                    if (startDate && startTime && endDate && endTime && deadlineDate && deadlineTime) {
                        try {
                            // Create date objects for comparison
                            const startDateTime = new Date(`${startDate}T${startTime}`);
                            const endDateTime = new Date(`${endDate}T${endTime}`);
                            const deadlineDateTime = new Date(`${deadlineDate}T${deadlineTime}`);
                            const now = new Date();
                            
                            // Ensure dates are valid
                            if (isNaN(startDateTime) || isNaN(endDateTime) || isNaN(deadlineDateTime)) {
                                alert('Please enter valid dates and times.');
                                isValid = false;
                            } else {
                                // Start date should be in the future
                                if (startDateTime < now) {
                                    document.getElementById('start_date').classList.add('is-invalid');
                                    document.getElementById('start_time').classList.add('is-invalid');
                                    alert('Start date and time must be in the future.');
                                    isValid = false;
                                }
                                
                                // End date should be after start date
                                if (endDateTime < startDateTime) {
                                    document.getElementById('end_date').classList.add('is-invalid');
                                    document.getElementById('end_time').classList.add('is-invalid');
                                    alert('End date and time must be after the start date and time.');
                                    isValid = false;
                                }
                                
                                // Registration deadline should be before start date
                                if (deadlineDateTime >= startDateTime) {
                                    document.getElementById('registration_deadline_date').classList.add('is-invalid');
                                    document.getElementById('registration_deadline_time').classList.add('is-invalid');
                                    alert('Registration deadline must be before the start date and time.');
                                    isValid = false;
                                }
                            }
                        } catch (e) {
                            console.error('Date validation error:', e);
                            alert('Error validating dates. Please ensure all dates are correctly formatted.');
                            isValid = false;
                        }
                    }
                    
                    // Validate venue fields if not an online event
                    if (!isOnlineCheckbox.checked) {
                        const governorate = document.getElementById('governorate');
                        const venue = document.getElementById('venue_id');
                        
                        if (governorate && !governorate.value) {
                            governorate.classList.add('is-invalid');
                            isValid = false;
                        }
                        
                        if (venue && !venue.value) {
                            venue.classList.add('is-invalid');
                            isValid = false;
                        }
                    }
                    
                    // Validate image upload if file option is selected
                    const imageSource = document.querySelector('input[name="image_source"]:checked').value;
                    if (imageSource === 'file') {
                        const imageFile = document.getElementById('image_file');
                        if (imageFile && imageFile.files.length === 0) {
                            imageFile.classList.add('is-invalid');
                            alert('Please select an image file to upload.');
                            isValid = false;
                        }
                    } else if (imageSource === 'url') {
                        const imageUrl = document.getElementById('image_url');
                        if (imageUrl && !isValidUrl(imageUrl.value)) {
                            imageUrl.classList.add('is-invalid');
                            alert('Please enter a valid image URL.');
                            isValid = false;
                        }
                    }
                    
                    return isValid;
                }
                
                // Validate URL format
                function isValidUrl(url) {
                    try {
                        new URL(url);
                        return true;
                    } catch (e) {
                        return false;
                    }
                }
            });
            </script>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>