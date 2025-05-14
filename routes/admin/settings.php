<?php
// Settings page for admins

// Get event types
$stmt = $pdo->query("SELECT id, name FROM event_type ORDER BY name");
$event_types = $stmt->fetchAll();

// Get categories
$stmt = $pdo->query("SELECT id, name FROM event_category ORDER BY name");
$categories = $stmt->fetchAll();

// Get users
$stmt = $pdo->query("SELECT id, email, role, created_at FROM users ORDER BY id");
$users = $stmt->fetchAll();

// Include the view
include 'views/admin/settings.php';
?>