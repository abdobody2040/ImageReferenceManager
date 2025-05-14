<?php
// Approve event route - Admin only

// Check if user is an admin
if (!isAdmin()) {
    flash('You do not have permission to approve events', 'danger');
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
    // Check if event exists and is not already approved
    $stmt = $pdo->prepare("SELECT id, name, status FROM event WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        flash('Event not found', 'danger');
        header('Location: /events');
        exit;
    }
    
    if ($event['status'] === 'approved') {
        flash('Event is already approved', 'info');
        header('Location: /events/' . $id);
        exit;
    }
    
    // Update event status to approved
    $stmt = $pdo->prepare("UPDATE event SET status = 'approved' WHERE id = ?");
    $stmt->execute([$id]);
    
    // Success message
    flash('Event "' . $event['name'] . '" has been approved', 'success');
    
} catch (PDOException $e) {
    // Log error
    error_log('Error approving event: ' . $e->getMessage());
    
    // Show error message
    flash('Database error: ' . $e->getMessage(), 'danger');
}

// Redirect back to event page
header('Location: /events/' . $id);
exit;
?>