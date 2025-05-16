<?php
/**
 * API endpoint for monthly events chart data
 */

// Require authentication
requireAuth();

// Initialize response arrays
$labels = [];
$values = [];

try {
    // Get monthly count for the past 12 months
    $sql = "
        SELECT 
            TO_CHAR(date_trunc('month', start_datetime), 'Mon YYYY') as month_label,
            COUNT(*) as event_count 
        FROM 
            event 
        WHERE 
            start_datetime >= NOW() - INTERVAL '12 months'
        GROUP BY 
            date_trunc('month', start_datetime)
        ORDER BY 
            date_trunc('month', start_datetime) ASC";
    
    $stmt = $pdo->query($sql);
    $monthly_data = $stmt->fetchAll();
    
    foreach ($monthly_data as $row) {
        $labels[] = $row['month_label'];
        $values[] = (int)$row['event_count'];
    }
} catch (Exception $e) {
    error_log("Error fetching monthly event data: " . $e->getMessage());
}

// Return data in format expected by Chart.js
header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?>