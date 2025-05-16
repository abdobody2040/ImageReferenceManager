<?php
// User profile route

try {
    // Get user details
    $stmt = $pdo->prepare("SELECT id, email, role, created_at FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    if (!$user) {
        flash('User not found', 'danger');
        header('Location: /logout');
        exit;
    }
    
    // Get user's events count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM event WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $events_count = $stmt->fetchColumn();
    
    // Get upcoming events count
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM event 
        WHERE user_id = ? AND start_datetime > NOW()
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $upcoming_events_count = $stmt->fetchColumn();
    
    // Get events created this month
    $start_of_month = date('Y-m-01 00:00:00');
    $end_of_month = date('Y-m-t 23:59:59');
    
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM event 
        WHERE user_id = ? 
        AND created_at BETWEEN ? AND ?
    ");
    $stmt->execute([$_SESSION['user_id'], $start_of_month, $end_of_month]);
    $events_this_month_count = $stmt->fetchColumn();
    
    // Include view
    include 'views/profile.php';
    
} catch (PDOException $e) {
    // Log error
    error_log('Error retrieving user profile: ' . $e->getMessage());
    
    // Show error message
    flash('Database error: ' . $e->getMessage(), 'danger');
    header('Location: /dashboard');
    exit;
}
?>