<?php
// API endpoint for events by type chart

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get events by type
    $stmt = $pdo->prepare("
        SELECT et.name, COUNT(e.id) as count
        FROM event_type et
        LEFT JOIN event e ON et.id = e.event_type_id
        WHERE e.id IS NULL OR (
            e.id IS NOT NULL
            AND (e.status = 'approved' OR e.user_id = ? OR ? = TRUE)
        )
        GROUP BY et.name
        ORDER BY count DESC
    ");
    
    $stmt->execute([$_SESSION['user_id'], isAdmin()]);
    $types = $stmt->fetchAll();
    
    // Format data for Chart.js
    $labels = [];
    $values = [];
    
    foreach ($types as $type) {
        $labels[] = $type['name'];
        $values[] = (int)$type['count'];
    }
    
    // Return as JSON
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Error getting type data: ' . $e->getMessage());
    
    // Return error
    echo json_encode([
        'error' => 'Database error',
        'labels' => [],
        'values' => []
    ]);
}
?>