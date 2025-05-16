<?php
/**
 * API endpoint for event types chart data
 */

// Require authentication
requireAuth();

// Initialize response arrays
$labels = [];
$values = [];

try {
    // Get event types
    $types_stmt = $pdo->query("SELECT id, name FROM event_type");
    $types = $types_stmt->fetchAll();

    // For each type, count associated events
    foreach ($types as $type) {
        $count_stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM event 
            WHERE event_type_id = ?
        ");
        $count_stmt->execute([$type['id']]);
        $count = $count_stmt->fetchColumn();

        // Only include types with events
        if ($count > 0) {
            $labels[] = $type['name'];
            $values[] = $count;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching event type data: " . $e->getMessage());
}

// Return data in format expected by Chart.js
header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?>