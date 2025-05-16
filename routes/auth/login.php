<?php
// Check if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: /dashboard');
    exit;
}

// Include login view
include 'views/login.php';
?>