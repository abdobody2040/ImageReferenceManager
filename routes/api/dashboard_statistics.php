<?php
// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get counts for dashboard statistics
    
    // Total events
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM event");
    $total_events = $stmt->fetch()['count'];
    
    // Upcoming events
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM event WHERE start_datetime > NOW()");
    $stmt->execute();
    $upcoming_events = $stmt->fetch()['count'];
    
    // Online events
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM event WHERE is_online = TRUE");
    $stmt->execute();
    $online_events = $stmt->fetch()['count'];
    
    // Offline/in-person events
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM event WHERE is_online = FALSE");
    $stmt->execute();
    $offline_events = $stmt->fetch()['count'];
    
    // Pending events count (for admins)
    $pending_events = 0;
    if (isAdmin()) {
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM event WHERE status = 'pending'");
        $stmt->execute();
        $pending_events = $stmt->fetch()['count'];
    }
    
    // Return JSON response
    echo json_encode([
        'total_events' => $total_events,
        'upcoming_events' => $upcoming_events,
        'online_events' => $online_events,
        'offline_events' => $offline_events,
        'pending_events' => $pending_events
    ]);
    
} catch (PDOException $e) {
    // Return error
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>