<?php
/**
 * API endpoint for dashboard statistics
 */

// Require authentication
requireAuth();

// Count events by type (online/offline)
$stmt = $pdo->prepare("SELECT COUNT(*) as total_events, 
                       SUM(CASE WHEN is_online = true THEN 1 ELSE 0 END) as online_events,
                       SUM(CASE WHEN is_online = false THEN 1 ELSE 0 END) as offline_events
                       FROM event");
$stmt->execute();
$stats = $stmt->fetch();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($stats);
?>