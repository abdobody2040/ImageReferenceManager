
<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT e.*, u.email as creator_email FROM events e 
                         LEFT JOIN users u ON e.user_id = u.id 
                         ORDER BY e.created_at DESC");
    $events = $stmt->fetchAll();
    require 'views/events.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/events/create') {
    $name = $_POST['event_name'];
    $description = $_POST['description'];
    $requester_name = $_POST['requester_name'];
    $is_online = isset($_POST['is_online']);
    $start_datetime = $_POST['start_date'] . ' ' . $_POST['start_time'];
    $end_datetime = $_POST['end_date'] . ' ' . $_POST['end_time'];
    $deadline = $_POST['deadline_date'] . ' ' . $_POST['deadline_time'];
    $governorate = !$is_online ? $_POST['governorate'] : null;
    $user_id = $_SESSION['user_id'];
    
    // Validate dates
    $start = new DateTime($start_datetime);
    $end = new DateTime($end_datetime);
    $reg_deadline = new DateTime($deadline);
    
    if ($end < $start) {
        $_SESSION['error'] = 'End date must be after start date';
        header('Location: /events/create');
        exit();
    }
    
    if ($reg_deadline >= $start) {
        $_SESSION['error'] = 'Registration deadline must be before event start';
        header('Location: /events/create');
        exit();
    }
    
    $stmt = $pdo->prepare("INSERT INTO events (name, description, start_datetime, end_datetime, user_id) 
                          VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $description, $start_datetime, $end_datetime, $user_id]);
    
    header('Location: /events');
    exit();
}
?>
