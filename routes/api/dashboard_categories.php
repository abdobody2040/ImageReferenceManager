<?php
// API endpoint for events by category chart

// Set content type to JSON
header('Content-Type: application/json');

try {
    // Get events by category
    $stmt = $pdo->prepare("
        SELECT ec.name, COUNT(ecj.event_id) as count
        FROM event_category ec
        LEFT JOIN event_category_junction ecj ON ec.id = ecj.category_id
        LEFT JOIN event e ON ecj.event_id = e.id
        WHERE e.id IS NULL OR (
            e.id IS NOT NULL
            AND (e.status = 'approved' OR e.user_id = ? OR ? = TRUE)
        )
        GROUP BY ec.name
        ORDER BY count DESC
    ");
    
    $stmt->execute([$_SESSION['user_id'], isAdmin()]);
    $categories = $stmt->fetchAll();
    
    // Format data for Chart.js
    $labels = [];
    $values = [];
    
    foreach ($categories as $category) {
        $labels[] = $category['name'];
        $values[] = (int)$category['count'];
    }
    
    // Return as JSON
    echo json_encode([
        'labels' => $labels,
        'values' => $values
    ]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Error getting category data: ' . $e->getMessage());
    
    // Return error
    echo json_encode([
        'error' => 'Database error',
        'labels' => [],
        'values' => []
    ]);
}
?>