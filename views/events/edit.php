<?php
$pageTitle = 'Edit Event - ' . getSetting('app_name', 'PharmaEvents');
$app_name = getSetting('app_name', 'PharmaEvents');

ob_start();
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Edit Event</h1>
        <a href="/events/<?php echo $event['id']; ?>" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Event
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Event Details</h6>
        </div>
        <div class="card-body">
            <form action="/events/<?php echo $event['id']; ?>/edit" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <!-- Basic Information -->
                <div class="mb-4">
                    <h5>Basic Information</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Event Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($event['name']); ?>" required>
                            <div class="invalid-feedback">Please provide an event name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="requester_name" class="form-label">Requester Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="requester_name" name="requester_name" value="<?php echo htmlspecialchars($event['requester_name']); ?>" required>
                            <div class="invalid-feedback">Please provide a requester name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="event_type_id" class="form-label">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select" id="event_type_id" name="event_type_id" required>
                                <option value="">Select Event Type</option>
                                <?php foreach ($event_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>" <?php echo ($event['event_type_id'] == $type['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select an event type.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="categories" class="form-label">Categories</label>
                            <select class="form-select" id="categories" name="categories[]" multiple>
                                <?php 
                                $event_category_ids = array_map(function($cat) { return $cat['id']; }, $event_categories);
                                foreach ($categories as $category): ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo in_array($category['id'], $event_category_ids) ? 'selected' : ''; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <small class="form-text text-muted">Hold Ctrl (or Cmd) to select multiple categories.</small>
                        </div>
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_online" name="is_online" value="1" <?php echo $event['is_online'] ? 'checked' : ''; ?>>
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
                        <?php 
                        // Format dates for input fields
                        $start_datetime = new DateTime($event['start_datetime']);
                        $end_datetime = new DateTime($event['end_datetime']);
                        $registration_deadline = new DateTime($event['registration_deadline']);
                        ?>
                        <div class="col-md-6">
                            <label for="start_date" class="form-label">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="start_date" name="start_date" value="<?php echo $start_datetime->format('Y-m-d'); ?>" required>
                            <div class="invalid-feedback">Please provide a start date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="start_time" name="start_time" value="<?php echo $start_datetime->format('H:i'); ?>" required>
                            <div class="invalid-feedback">Please provide a start time.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="end_date" name="end_date" value="<?php echo $end_datetime->format('Y-m-d'); ?>" required>
                            <div class="invalid-feedback">Please provide an end date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="end_time" name="end_time" value="<?php echo $end_datetime->format('H:i'); ?>" required>
                            <div class="invalid-feedback">Please provide an end time.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="registration_deadline_date" class="form-label">Registration Deadline Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="registration_deadline_date" name="registration_deadline_date" value="<?php echo $registration_deadline->format('Y-m-d'); ?>" required>
                            <div class="invalid-feedback">Please provide a registration deadline date.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="registration_deadline_time" class="form-label">Registration Deadline Time <span class="text-danger">*</span></label>
                            <input type="time" class="form-control" id="registration_deadline_time" name="registration_deadline_time" value="<?php echo $registration_deadline->format('H:i'); ?>" required>
                            <div class="invalid-feedback">Please provide a registration deadline time.</div>
                        </div>
                    </div>
                </div>

                <!-- Venue Information (hidden if online event) -->
                <div class="mb-4" id="venue-fields" <?php echo $event['is_online'] ? 'style="display: none;"' : ''; ?>>
                    <h5>Venue Information</h5>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="governorate" class="form-label">Governorate <span class="text-danger">*</span></label>
                            <select class="form-select" id="governorate" name="governorate" data-required="true" <?php echo $event['is_online'] ? '' : 'required'; ?>>
                                <option value="">Select Governorate</option>
                                <?php foreach ($governorates as $governorate): ?>
                                    <option value="<?php echo $governorate; ?>" <?php echo ($event['governorate'] == $governorate) ? 'selected' : ''; ?>><?php echo $governorate; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback">Please select a governorate.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="venue_id" class="form-label">Venue <span class="text-danger">*</span></label>
                            <select class="form-select" id="venue_id" name="venue_id" data-required="true" <?php echo $event['is_online'] ? '' : 'required'; ?>>
                                <option value="">Select Venue</option>
                                <?php foreach ($venues as $venue): ?>
                                    <option value="<?php echo $venue['id']; ?>" 
                                            data-governorate="<?php echo $venue['governorate']; ?>" 
                                            <?php echo ($event['venue_id'] == $venue['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($venue['name']); ?>
                                    </option>
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
                                    <option value="<?php echo $service['id']; ?>" <?php echo ($event['service_request_id'] == $service['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($service['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="employee_code_id" class="form-label">Employee Code</label>
                            <select class="form-select" id="employee_code_id" name="employee_code_id">
                                <option value="">None</option>
                                <?php foreach ($employee_codes as $code): ?>
                                    <option value="<?php echo $code['id']; ?>" <?php echo ($event['employee_code_id'] == $code['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($code['code'] . ' - ' . $code['name']); ?></option>
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
                            <?php 
                            $image_source = 'none';
                            if (!empty($event['image_url'])) {
                                $image_source = 'url';
                            } elseif (!empty($event['image_file'])) {
                                $image_source = 'file';
                            }
                            ?>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_source" id="image_source_none" value="none" <?php echo $image_source === 'none' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="image_source_none">No Image</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_source" id="image_source_url" value="url" <?php echo $image_source === 'url' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="image_source_url">Image URL</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="image_source" id="image_source_file" value="file" <?php echo $image_source === 'file' ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="image_source_file">Upload Image</label>
                            </div>
                        </div>
                        <div class="col-md-12" id="image-url-field" style="display: <?php echo $image_source === 'url' ? 'block' : 'none'; ?>;">
                            <label for="image_url" class="form-label">Image URL</label>
                            <input type="url" class="form-control" id="image_url" name="image_url" placeholder="https://example.com/image.jpg" value="<?php echo htmlspecialchars($event['image_url'] ?? ''); ?>">
                            <div class="invalid-feedback">Please provide a valid URL.</div>
                            <?php if (!empty($event['image_url'])): ?>
                                <div class="mt-2">
                                    <p>Current image:</p>
                                    <img src="<?php echo htmlspecialchars($event['image_url']); ?>" alt="Current event image" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-12" id="image-file-field" style="display: <?php echo $image_source === 'file' ? 'block' : 'none'; ?>;">
                            <label for="image_file" class="form-label">Image File</label>
                            <input type="file" class="form-control" id="image_file" name="image_file" accept="image/*">
                            <div class="invalid-feedback">Please provide a valid image file.</div>
                            <input type="hidden" name="current_image_file" value="<?php echo htmlspecialchars($event['image_file'] ?? ''); ?>">
                            <?php if (!empty($event['image_file'])): ?>
                                <div class="mt-2">
                                    <p>Current image:</p>
                                    <img src="<?php echo htmlspecialchars($event['image_file']); ?>" alt="Current event image" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
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
                            <textarea class="form-control" id="description" name="description" rows="5"><?php echo htmlspecialchars($event['description'] ?? ''); ?></textarea>
                        </div>
                    </div>
                </div>

                <!-- Form Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <a href="/events/<?php echo $event['id']; ?>" class="btn btn-secondary me-md-2">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>