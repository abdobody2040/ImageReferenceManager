<?php
// Show event details

// Get event ID from URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    flash('Invalid event ID', 'danger');
    header('Location: /events');
    exit;
}

try {
    // Get event details
    $stmt = $pdo->prepare("
        SELECT e.*, et.name as event_type_name
        FROM event e
        LEFT JOIN event_type et ON e.event_type_id = et.id
        WHERE e.id = ?
    ");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        flash('Event not found', 'danger');
        header('Location: /events');
        exit;
    }
    
    // Check access permissions
    if ($event['status'] !== 'approved' && $event['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
        flash('You do not have permission to view this event', 'danger');
        header('Location: /events');
        exit;
    }
    
    // Get related data
    
    // Event type
    $stmt = $pdo->prepare("SELECT id, name FROM event_type WHERE id = ?");
    $stmt->execute([$event['event_type_id']]);
    $event_type = $stmt->fetch();
    
    // Event categories
    $stmt = $pdo->prepare("
        SELECT ec.id, ec.name
        FROM event_category ec
        JOIN event_category_junction ecj ON ec.id = ecj.category_id
        WHERE ecj.event_id = ?
    ");
    $stmt->execute([$id]);
    $event_categories = $stmt->fetchAll();
    
    // Venue (if not online)
    $venue = null;
    if (!$event['is_online'] && !empty($event['venue_id'])) {
        $stmt = $pdo->prepare("SELECT id, name, governorate FROM venue WHERE id = ?");
        $stmt->execute([$event['venue_id']]);
        $venue = $stmt->fetch();
    }
    
    // Service request
    $service_request = null;
    if (!empty($event['service_request_id'])) {
        $stmt = $pdo->prepare("SELECT id, name FROM service_request WHERE id = ?");
        $stmt->execute([$event['service_request_id']]);
        $service_request = $stmt->fetch();
    }
    
    // Employee code
    $employee_code = null;
    if (!empty($event['employee_code_id'])) {
        $stmt = $pdo->prepare("SELECT id, code, name FROM employee_code WHERE id = ?");
        $stmt->execute([$event['employee_code_id']]);
        $employee_code = $stmt->fetch();
    }
    
    // Creator
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ?");
    $stmt->execute([$event['user_id']]);
    $creator = $stmt->fetch();
    
    // Include view
    include 'views/events/show.php';
    
} catch (PDOException $e) {
    // Log error
    error_log('Error retrieving event: ' . $e->getMessage());
    
    // Show error message
    flash('Database error: ' . $e->getMessage(), 'danger');
    header('Location: /events');
    exit;
}
?>