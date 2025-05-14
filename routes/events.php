
<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT e.*, u.email as creator_email FROM events e 
                         LEFT JOIN users u ON e.user_id = u.id 
                         ORDER BY e.created_at DESC");
    $events = $stmt->fetchAll();
    require 'views/events.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $start_datetime = $_POST['start_datetime'];
    $end_datetime = $_POST['end_datetime'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $pdo->prepare("INSERT INTO events (name, description, start_datetime, end_datetime, user_id) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $start_datetime, $end_datetime, $user_id]);
    
    header('Location: /events');
    exit();
}
?>
