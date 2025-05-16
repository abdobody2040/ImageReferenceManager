<?php
/**
 * API endpoint for category chart data
 */

// Require authentication
requireAuth();

// Initialize response arrays
$labels = [];
$values = [];

try {
    // Get categories
    $categories_stmt = $pdo->query("SELECT id, name FROM event_category");
    $categories = $categories_stmt->fetchAll();

    // For each category, count associated events
    foreach ($categories as $category) {
        $count_stmt = $pdo->prepare("
            SELECT COUNT(*) as count 
            FROM event_categories 
            WHERE category_id = ?
        ");
        $count_stmt->execute([$category['id']]);
        $count = $count_stmt->fetchColumn();

        // Only include categories with events
        if ($count > 0) {
            $labels[] = $category['name'];
            $values[] = $count;
        }
    }
} catch (Exception $e) {
    error_log("Error fetching category data: " . $e->getMessage());
}

// Return data in format expected by Chart.js
header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels,
    'values' => $values
]);
?>