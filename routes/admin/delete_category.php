<?php
// Delete category

// Get ID from URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    flash('Invalid category ID', 'danger');
    header('Location: /settings');
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Delete from junction table first
    $stmt = $pdo->prepare("DELETE FROM event_category_junction WHERE category_id = ?");
    $stmt->execute([$id]);
    
    // Delete the category
    $stmt = $pdo->prepare("DELETE FROM event_category WHERE id = ?");
    $stmt->execute([$id]);
    
    if ($stmt->rowCount() === 0) {
        throw new Exception('Category not found');
    }
    
    // Commit transaction
    $pdo->commit();
    
    flash('Category deleted successfully', 'success');
    
} catch (Exception $e) {
    // Roll back transaction
    $pdo->rollBack();
    
    // Log error
    error_log('Error deleting category: ' . $e->getMessage());
    
    flash('Error: ' . $e->getMessage(), 'danger');
}

// Redirect back to settings page
header('Location: /settings');
exit;
?>