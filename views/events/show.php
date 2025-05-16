<?php
$pageTitle = $event['name'] . ' - ' . getSetting('app_name', 'PharmaEvents');
$app_name = getSetting('app_name', 'PharmaEvents');

ob_start();
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0"><?php echo htmlspecialchars($event['name']); ?></h1>
        <div>
            <?php if ($event['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                <a href="/events/<?php echo $event['id']; ?>/edit" class="d-none d-sm-inline-block btn btn-primary shadow-sm me-2">
                    <i class="fas fa-edit fa-sm text-white-50 me-1"></i> Edit Event
                </a>
            <?php endif; ?>
            <a href="/events" class="d-none d-sm-inline-block btn btn-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-white-50 me-1"></i> Back to Events
            </a>
        </div>
    </div>

    <!-- Status Alert for Pending/Rejected Events -->
    <?php if ($event['status'] === 'pending'): ?>
        <div class="alert alert-warning">
            <i class="fas fa-clock me-2"></i> This event is pending approval from an administrator.
            <?php if (isAdmin()): ?>
                <div class="mt-2">
                    <a href="/events/<?php echo $event['id']; ?>/approve" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> Approve
                    </a>
                    <a href="/events/<?php echo $event['id']; ?>/reject" class="btn btn-danger btn-sm ms-2">
                        <i class="fas fa-times"></i> Reject
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php elseif ($event['status'] === 'rejected'): ?>
        <div class="alert alert-danger">
            <i class="fas fa-ban me-2"></i> This event has been rejected.
            <?php if (isAdmin()): ?>
                <div class="mt-2">
                    <a href="/events/<?php echo $event['id']; ?>/approve" class="btn btn-success btn-sm">
                        <i class="fas fa-check"></i> Approve
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Event Details -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Event Details</h6>
                    <span class="badge <?php echo getEventBadgeClass($event['is_online']); ?>">
                        <?php echo $event['is_online'] ? 'Online Event' : 'In-Person Event'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (!empty($event['image_url']) || !empty($event['image_file'])): ?>
                        <div class="text-center mb-4">
                            <img src="<?php echo !empty($event['image_url']) ? $event['image_url'] : $event['image_file']; ?>" 
                                alt="<?php echo htmlspecialchars($event['name']); ?>" 
                                class="img-fluid rounded" style="max-height: 300px;">
                        </div>
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Date and Time</h5>
                            <p><i class="far fa-calendar-alt me-2"></i> <strong>Start:</strong> <?php echo formatDateTime($event['start_datetime']); ?></p>
                            <p><i class="far fa-calendar-alt me-2"></i> <strong>End:</strong> <?php echo formatDateTime($event['end_datetime']); ?></p>
                            <p><i class="far fa-clock me-2"></i> <strong>Registration Deadline:</strong> <?php echo formatDateTime($event['registration_deadline']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Requester</h5>
                            <p><i class="far fa-user me-2"></i> <?php echo htmlspecialchars($event['requester_name']); ?></p>
                            <p><i class="fas fa-tag me-2"></i> <strong>Type:</strong> <?php echo htmlspecialchars($event_type['name']); ?></p>
                            <?php if (!empty($event_categories)): ?>
                                <p>
                                    <i class="fas fa-tags me-2"></i> <strong>Categories:</strong>
                                    <?php foreach ($event_categories as $category): ?>
                                        <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($category['name']); ?></span>
                                    <?php endforeach; ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <?php if (!$event['is_online']): ?>
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <h5>Location</h5>
                            <?php if ($venue): ?>
                                <p><i class="fas fa-map-marker-alt me-2"></i> <strong>Venue:</strong> <?php echo htmlspecialchars($venue['name']); ?></p>
                                <p><i class="fas fa-map me-2"></i> <strong>Governorate:</strong> <?php echo htmlspecialchars($event['governorate']); ?></p>
                            <?php else: ?>
                                <p><i class="fas fa-map me-2"></i> <strong>Governorate:</strong> <?php echo htmlspecialchars($event['governorate']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Description</h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    <?php if (!empty($event['description'])): ?>
                                        <p><?php echo nl2br(htmlspecialchars($event['description'])); ?></p>
                                    <?php else: ?>
                                        <p class="text-muted">No description provided.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Status</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-<?php echo $event['status'] === 'pending' ? 'warning' : ($event['status'] === 'approved' ? 'success' : 'danger'); ?> fs-6">
                            <?php echo ucfirst($event['status']); ?>
                        </span>
                    </div>
                    <p><i class="far fa-calendar-check me-2"></i> <strong>Created:</strong> <?php echo formatDateTime($event['created_at']); ?></p>
                    <p><i class="far fa-user me-2"></i> <strong>Created By:</strong> <?php echo htmlspecialchars($creator['email']); ?></p>
                </div>
            </div>

            <!-- Services Card -->
            <?php if ($service_request || $employee_code): ?>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Services & Support</h6>
                </div>
                <div class="card-body">
                    <?php if ($service_request): ?>
                        <p><i class="fas fa-concierge-bell me-2"></i> <strong>Service Request:</strong> <?php echo htmlspecialchars($service_request['name']); ?></p>
                    <?php endif; ?>
                    
                    <?php if ($employee_code): ?>
                        <p><i class="fas fa-id-badge me-2"></i> <strong>Employee Code:</strong> <?php echo htmlspecialchars($employee_code['code']); ?> (<?php echo htmlspecialchars($employee_code['name']); ?>)</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Actions</h6>
                </div>
                <div class="card-body">
                    <?php if ($event['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                        <a href="/events/<?php echo $event['id']; ?>/edit" class="btn btn-primary btn-block mb-2 w-100">
                            <i class="fas fa-edit me-1"></i> Edit Event
                        </a>
                        <a href="/events/<?php echo $event['id']; ?>/delete" class="btn btn-danger btn-block w-100 confirm-action" data-confirm="Are you sure you want to delete this event?">
                            <i class="fas fa-trash me-1"></i> Delete Event
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>