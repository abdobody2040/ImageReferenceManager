<?php
// Delete event type

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    flash('Invalid event type ID', 'danger');
    header('Location: /settings');
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Check if the event type is in use
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event WHERE event_type_id = ?");
    $stmt->execute([$id]);
    $in_use = $stmt->fetchColumn() > 0;
    
    if ($in_use) {
        flash('Cannot delete this event type because it is being used by one or more events', 'danger');
        header('Location: /settings');
        exit;
    }
    
    // Delete the event type
    $stmt = $pdo->prepare("DELETE FROM event_type WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Event type not found');
    }
    
    // Commit transaction
    $pdo->commit();
    
    flash('Event type deleted successfully', 'success');
    
} catch (Exception $e) {
    // Roll back transaction
    $pdo->rollBack();
    
    // Log error
    error_log('Error deleting event type: ' . $e->getMessage());
    
    flash('Error: ' . $e->getMessage(), 'danger');
}

// Redirect back to settings page
header('Location: /settings');
exit;
?>