<?php
// Add event type

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('Invalid request method', 'danger');
    header('Location: /settings');
    exit;
}

// Get and validate the event type name
$name = trim($_POST['name'] ?? '');
if (empty($name)) {
    flash('Event type name cannot be empty', 'danger');
    header('Location: /settings');
    exit;
}

try {
    // Check if the event type already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event_type WHERE name = ?");
    $stmt->execute([$name]);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        flash('An event type with this name already exists', 'danger');
        header('Location: /settings');
        exit;
    }
    
    // Insert new event type
    $stmt = $pdo->prepare("INSERT INTO event_type (name) VALUES (?)");
    $stmt->execute([$name]);
    
    flash('Event type added successfully', 'success');
    
} catch (PDOException $e) {
    // Log error
    error_log('Error adding event type: ' . $e->getMessage());
    
    flash('Database error: ' . $e->getMessage(), 'danger');
}

// Redirect back to settings page
header('Location: /settings');
exit;
?>