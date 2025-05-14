<?php
// API endpoint for pending events for dashboard

// Set content type to JSON
header('Content-Type: application/json');

// Check if user is admin (only admins can see pending events)
if (!isAdmin()) {
    echo json_encode(['error' => 'Unauthorized', 'events' => []]);
    exit;
}

try {
    // Get the most recent 5 pending events
    $stmt = $pdo->prepare("
        SELECT e.id, e.name, e.requester_name, e.start_datetime
        FROM event e
        WHERE e.status = 'pending'
        ORDER BY e.created_at DESC
        LIMIT 5
    ");
    
    $stmt->execute();
    $events = $stmt->fetchAll();
    
    // Return as JSON
    echo json_encode(['events' => $events]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Error getting pending events: ' . $e->getMessage());
    
    // Return error
    echo json_encode(['error' => 'Database error', 'events' => []]);
}
?>