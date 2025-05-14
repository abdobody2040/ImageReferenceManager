<?php
// Delete user

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    flash('Invalid user ID', 'danger');
    header('Location: /settings');
    exit;
}

// Prevent deleting the current user
if ($id == $_SESSION['user_id']) {
    flash('You cannot delete your own account', 'danger');
    header('Location: /settings');
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Check if user exists
    $stmt = $pdo->prepare("SELECT id, email FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if (!$user) {
        throw new Exception('User not found');
    }
    
    // Check for events associated with this user
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event WHERE user_id = ?");
    $stmt->execute([$id]);
    $event_count = $stmt->fetchColumn();
    
    if ($event_count > 0) {
        // Handle events created by this user
        // Option 1: Transfer events to current admin
        $stmt = $pdo->prepare("UPDATE event SET user_id = ? WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id'], $id]);
        
        // Option 2: Delete all events (would require additional cascading) - not used here
        // $stmt = $pdo->prepare("DELETE FROM event WHERE user_id = ?");
        // $stmt->execute([$id]);
    }
    
    // Delete the user
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    
    // Commit transaction
    $pdo->commit();
    
    flash('User deleted successfully. All their events have been transferred to your account.', 'success');
    
} catch (Exception $e) {
    // Roll back transaction
    $pdo->rollBack();
    
    // Log error
    error_log('Error deleting user: ' . $e->getMessage());
    
    flash('Error: ' . $e->getMessage(), 'danger');
}

// Redirect back to settings page
header('Location: /settings');
exit;
?>