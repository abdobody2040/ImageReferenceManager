<?php
// Delete event route

// Get event ID from URL parameter
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    flash('Invalid event ID', 'danger');
    header('Location: /events');
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Check if event exists and user has permission to delete it
    $stmt = $pdo->prepare("SELECT id, name, user_id FROM event WHERE id = ?");
    $stmt->execute([$id]);
    $event = $stmt->fetch();
    
    if (!$event) {
        flash('Event not found', 'danger');
        header('Location: /events');
        exit;
    }
    
    // Check if user has permission to delete the event
    if ($event['user_id'] != $_SESSION['user_id'] && !isAdmin()) {
        flash('You do not have permission to delete this event', 'danger');
        header('Location: /events');
        exit;
    }
    
    // Delete event categories junction entries first (foreign key constraint)
    $stmt = $pdo->prepare("DELETE FROM event_category_junction WHERE event_id = ?");
    $stmt->execute([$id]);
    
    // Delete the event
    $stmt = $pdo->prepare("DELETE FROM event WHERE id = ?");
    $stmt->execute([$id]);
    
    // Commit transaction
    $pdo->commit();
    
    // Success message
    flash('Event "' . $event['name'] . '" has been deleted', 'success');
    
} catch (PDOException $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    // Log error
    error_log('Error deleting event: ' . $e->getMessage());
    
    // Show error message
    flash('Database error: ' . $e->getMessage(), 'danger');
}

// Redirect to events list
header('Location: /events');
exit;
?>