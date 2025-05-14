<?php
// Add category

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('Invalid request method', 'danger');
    header('Location: /settings');
    exit;
}

// Get and validate the category name
$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    flash('Category name cannot be empty', 'danger');
    header('Location: /settings');
    exit;
}

try {
    // Check if the category already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_category WHERE name = ?");
    $stmt->execute([$name]);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        flash('A category with this name already exists', 'danger');
        header('Location: /settings');
        exit;
    }
    
    // Insert new category
    $stmt = $pdo->prepare("INSERT INTO event_category (name) VALUES (?)");
    $stmt->execute([$name]);
    
    flash('Category added successfully', 'success');
    
} catch (PDOException $e) {
    // Log error
    error_log('Error adding category: ' . $e->getMessage());
    
    flash('Database error: ' . $e->getMessage(), 'danger');
}

// Redirect back to settings page
header('Location: /settings');
exit;
?>