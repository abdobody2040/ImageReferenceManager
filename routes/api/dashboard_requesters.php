<?php
// API endpoint for top requesters chart

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get top requesters (limit to top 5)
    $stmt = $pdo->prepare("
        SELECT requester_name, COUNT(*) as count
        FROM event
        WHERE status = 'approved' OR user_id = ? OR ? = TRUE
        GROUP BY requester_name
        ORDER BY count DESC
        LIMIT 5
    ");
    
    $stmt->execute([$_SESSION['user_id'], isAdmin()]);
    $requesters = $stmt->fetchAll();
    
    // Format data for Chart.js
    $labels = [];
    $values = [];
    
    foreach ($requesters as $requester) {
        $labels[] = $requester['requester_name'];
        $values[] = (int)$requester['count'];
    }
    
    // Return as JSON
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Error getting requester data: ' . $e->getMessage());
    
    // Return error
    echo json_encode([
        'error' => 'Database error',
        'labels' => [],
        'values' => []
    ]);
}
?>