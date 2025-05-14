<?php
// Change user password route

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    flash('Invalid request method', 'danger');
    header('Location: /profile');
    exit;
}

// Get form data
$current_password = $_POST['current_password'] ?? '';
$new_password = $_POST['new_password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

// Validate input
if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
    flash('All fields are required', 'danger');
    header('Location: /profile');
    exit;
}

if ($new_password !== $confirm_password) {
    flash('New passwords do not match', 'danger');
    header('Location: /profile');
    exit;
}

// Password complexity check
if (strlen($new_password) < 6) {
    flash('New password must be at least 6 characters long', 'danger');
    header('Location: /profile');
    exit;
}

try {
    // Get user's current password hash
    $stmt = $pdo->prepare("SELECT password_hash FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        flash('User not found', 'danger');
        header('Location: /logout');
        exit;
    }
    
    // Verify current password
    if (!password_verify($current_password, $user['password_hash'])) {
        flash('Current password is incorrect', 'danger');
        header('Location: /profile');
        exit;
    }
    
    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
    $stmt->execute([$password_hash, $_SESSION['user_id']]);
    
    // Success message
    flash('Password changed successfully', 'success');
    
} catch (PDOException $e) {
    // Log error
    error_log('Error changing password: ' . $e->getMessage());
    
    // Show error message
    flash('Database error: ' . $e->getMessage(), 'danger');
}

// Redirect back to profile page
header('Location: /profile');
exit;
?>