
<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

$request = $_SERVER['REQUEST_URI'];
$base_path = '/';

switch ($request) {
    case $base_path:
        require 'routes/home.php';
        break;
    case $base_path . 'login':
        require 'routes/auth/login.php';
        break;
    case $base_path . 'logout':
        require 'routes/auth/logout.php';
        break;
    case $base_path . 'dashboard':
        requireAuth();
        require 'routes/dashboard.php';
        break;
    case $base_path . 'events':
        requireAuth();
        require 'routes/events.php';
        break;
    case $base_path . 'settings':
        requireAuth();
        requireAdmin();
        require 'routes/settings.php';
        break;
    case $base_path . 'users':
        requireAuth();
        requireAdmin();
        require 'routes/users.php';
        break;
    default:
        http_response_code(404);
        require 'views/404.php';
        break;
}
?>
