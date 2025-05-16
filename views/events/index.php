<?php
$pageTitle = 'Events - ' . getSetting('app_name', 'PharmaEvents');
$app_name = getSetting('app_name', 'PharmaEvents');

// Additional JavaScript for events list
$pageScripts = '<script src="/static/js/events.js"></script>';

ob_start();
?>

<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0">Events</h1>
        <div>
            <a href="/events/export" class="d-none d-sm-inline-block btn btn-secondary shadow-sm me-2">
                <i class="fas fa-download fa-sm text-white-50 me-1"></i> Export CSV
            </a>
            <a href="/events/create" class="d-none d-sm-inline-block btn btn-primary shadow-sm">
                <i class="fas fa-plus-circle fa-sm text-white-50 me-1"></i> Create New Event
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Filters</h6>
        </div>
        <div class="card-body">
            <form method="get" action="/events" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Search</label>
                    <input type="text" class="form-control" id="search" name="search" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" placeholder="Name, description...">
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">Event Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">All Types</option>
                        <?php foreach ($event_types as $type): ?>
                            <option value="<?php echo $type['id']; ?>" <?php echo isset($_GET['type']) && $_GET['type'] == $type['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($type['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All Statuses</option>
                        <option value="pending" <?php echo isset($_GET['status']) && $_GET['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="approved" <?php echo isset($_GET['status']) && $_GET['status'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                        <option value="rejected" <?php echo isset($_GET['status']) && $_GET['status'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="format" class="form-label">Format</label>
                    <select class="form-select" id="format" name="format">
                        <option value="">All Formats</option>
                        <option value="online" <?php echo isset($_GET['format']) && $_GET['format'] == 'online' ? 'selected' : ''; ?>>Online</option>
                        <option value="in-person" <?php echo isset($_GET['format']) && $_GET['format'] == 'in-person' ? 'selected' : ''; ?>>In-Person</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="date_from" class="form-label">Date From</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="<?php echo isset($_GET['date_from']) ? htmlspecialchars($_GET['date_from']) : ''; ?>">
                </div>
                <div class="col-md-3">
                    <label for="date_to" class="form-label">Date To</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="<?php echo isset($_GET['date_to']) ? htmlspecialchars($_GET['date_to']) : ''; ?>">
                </div>
                <div class="col-md-6">
                    <label class="invisible">Actions</label>
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-primary me-md-2">Apply Filters</button>
                        <button type="button" class="btn btn-secondary" id="clear-filters">Clear Filters</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- View Toggle -->
    <div class="d-flex justify-content-end mb-3 view-toggle">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary active" data-view="grid">
                <i class="fas fa-th-large"></i>
            </button>
            <button type="button" class="btn btn-outline-primary" data-view="list">
                <i class="fas fa-list"></i>
            </button>
        </div>
    </div>

    <?php if (empty($events)): ?>
    <!-- No Events -->
    <div class="alert alert-info">
        <p class="mb-0">No events found matching your criteria.</p>
    </div>
    <?php else: ?>

    <!-- Grid View (default) -->
    <div id="grid-view" class="row">
        <?php foreach ($events as $event): ?>
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card event-card shadow">
                <div class="card-img-top-wrapper position-relative">
                    <img src="<?php echo !empty($event['image_url']) ? $event['image_url'] : (!empty($event['image_file']) ? $event['image_file'] : '/static/img/event-placeholder.jpg'); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($event['name']); ?>">
                    <span class="position-absolute top-0 start-0 badge <?php echo getEventBadgeClass($event['is_online']); ?> m-2">
                        <?php echo $event['is_online'] ? 'Online' : 'In-Person'; ?>
                    </span>
                    <span class="position-absolute top-0 end-0 badge bg-<?php echo $event['status'] === 'pending' ? 'warning' : ($event['status'] === 'approved' ? 'success' : 'danger'); ?> m-2">
                        <?php echo ucfirst($event['status']); ?>
                    </span>
                </div>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                    <p class="card-text text-muted">
                        <i class="far fa-calendar-alt me-1"></i> <?php echo formatDateTime($event['start_datetime']); ?>
                    </p>
                    <p class="card-text text-muted">
                        <i class="far fa-user me-1"></i> <?php echo htmlspecialchars($event['requester_name']); ?>
                    </p>
                    <p class="card-text">
                        <?php 
                            $description = $event['description'];
                            echo htmlspecialchars(strlen($description) > 100 ? substr($description, 0, 100) . '...' : $description); 
                        ?>
                    </p>
                </div>
                <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between">
                    <a href="/events/<?php echo $event['id']; ?>" class="btn btn-sm btn-info">View Details</a>
                    <?php if ($event['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                    <div>
                        <a href="/events/<?php echo $event['id']; ?>/edit" class="btn btn-sm btn-primary">Edit</a>
                        <a href="/events/<?php echo $event['id']; ?>/delete" class="btn btn-sm btn-danger confirm-action" data-confirm="Are you sure you want to delete this event?">Delete</a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- List View (hidden by default) -->
    <div id="list-view" class="card shadow mb-4" style="display: none;">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Requester</th>
                            <th>Format</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($event['name']); ?></td>
                            <td><?php echo htmlspecialchars($event['requester_name']); ?></td>
                            <td>
                                <span class="badge <?php echo getEventBadgeClass($event['is_online']); ?>">
                                    <?php echo $event['is_online'] ? 'Online' : 'In-Person'; ?>
                                </span>
                            </td>
                            <td><?php echo formatDateTime($event['start_datetime']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $event['status'] === 'pending' ? 'warning' : ($event['status'] === 'approved' ? 'success' : 'danger'); ?>">
                                    <?php echo ucfirst($event['status']); ?>
                                </span>
                            </td>
                            <td>
                                <a href="/events/<?php echo $event['id']; ?>" class="btn btn-sm btn-info">View</a>
                                <?php if ($event['user_id'] == $_SESSION['user_id'] || isAdmin()): ?>
                                <a href="/events/<?php echo $event['id']; ?>/edit" class="btn btn-sm btn-primary">Edit</a>
                                <a href="/events/<?php echo $event['id']; ?>/delete" class="btn btn-sm btn-danger confirm-action" data-confirm="Are you sure you want to delete this event?">Delete</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Pagination -->
    <?php if ($pagination['total_pages'] > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $pagination['current_page'] == 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo buildPaginationUrl($pagination['current_page'] - 1); ?>" tabindex="-1">Previous</a>
            </li>
            
            <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                <li class="page-item <?php echo $pagination['current_page'] == $i ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo buildPaginationUrl($i); ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <li class="page-item <?php echo $pagination['current_page'] == $pagination['total_pages'] ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo buildPaginationUrl($pagination['current_page'] + 1); ?>">Next</a>
            </li>
        </ul>
    </nav>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';

// Helper function to build pagination URL
function buildPaginationUrl($page) {
    $params = $_GET;
    $params['page'] = $page;
    return '/events?' . http_build_query($params);
}
?>