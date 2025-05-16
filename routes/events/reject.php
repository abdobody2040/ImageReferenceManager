<?php
// Reject event route - Admin only

// Check if user is an admin
if (!isAdmin()) {
    flash('You do not have permission to reject events', 'danger');
    header('Location: /events');
    exit;
}

// Get event ID from URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    flash('Invalid event ID', 'danger');
    header('Location: /events');
    exit;
}

try {
    // Check if event exists and is not already rejected
    $stmt = $pdo->prepare("SELECT id, name, status FROM event WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        flash('Event not found', 'danger');
        header('Location: /events');
        exit;
    }
    
    if ($event['status'] === 'rejected') {
        flash('Event is already rejected', 'info');
        header('Location: /events/' . $id);
        exit;
    }
    
    // Update event status to rejected
    $stmt = $pdo->prepare("UPDATE event SET status = 'rejected' WHERE id = ?");
    $stmt->execute([$id]);
    
    // Success message
    flash('Event "' . $event['name'] . '" has been rejected', 'success');
    
} catch (PDOException $e) {
    // Log error
    error_log('Error rejecting event: ' . $e->getMessage());
    
    // Show error message
    flash('Database error: ' . $e->getMessage(), 'danger');
}

// Redirect back to event page
header('Location: /events/' . $id);
exit;
?>