<?php
/**
 * API endpoint for top requesters chart data
 */

// Require authentication
requireAuth();

// Initialize response arrays
$labels = [];
$values = [];

try {
    // Get top 5 requesters by event count
    $sql = "
        SELECT 
            requester_name,
            COUNT(*) as event_count
        FROM 
            event
        GROUP BY 
            requester_name
        ORDER BY 
            event_count DESC
        LIMIT 5";
    
    $stmt = $pdo->query($sql);
    $requesters = $stmt->fetchAll();
    
    foreach ($requesters as $row) {
        if (!empty($row['requester_name'])) {
            $labels[] = $row['requester_name'];
            $values[] = (int)$row['event_count'];
        }
    }
} catch (Exception $e) {
    error_log("Error fetching requester data: " . $e->getMessage());
}

// Return data in format expected by Chart.js
header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?>