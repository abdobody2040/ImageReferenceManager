
<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT id, email, role FROM users");
    $users = $stmt->fetchAll();
    require 'views/users.php';
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $id = $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true]);
}
?>
