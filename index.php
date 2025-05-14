<?php
session_start();
require_once 'config/database.php';
require_once 'config/functions.php';

// Parse requested URL
$request_uri = $_SERVER['REQUEST_URI'];
$base_path = '/';

// Remove query string if present
$request_uri = strtok($request_uri, '?');

// Remove trailing slash if present
$request_uri = rtrim($request_uri, '/');

// Add trailing slash to base path for proper routing
if ($request_uri === '') {
    $request_uri = '/';
}

// Router based on the request URI
switch ($request_uri) {
    case $base_path:
        // Home page redirects to dashboard if logged in, otherwise to login
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        } else {
            header('Location: /login');
            exit;
        }
        break;
        
    case $base_path . 'login':
        // Login page
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/auth/login_post.php';
        } else {
            require 'routes/auth/login.php';
        }
        break;
        
    case $base_path . 'logout':
        // Logout action
        require 'routes/auth/logout.php';
        break;
        
    case $base_path . 'forgot-password':
        // Forgot password page
        require 'routes/auth/forgot_password.php';
        break;
        
    case $base_path . 'dashboard':
        // Dashboard page
        requireAuth();
        require 'routes/dashboard.php';
        break;
        
    case $base_path . 'events':
        // Events listing page
        requireAuth();
        require 'routes/events/index.php';
        break;
        
    case $base_path . 'events/create':
        // Create event page
        requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/events/create_post.php';
        } else {
            require 'routes/events/create.php';
        }
        break;
        
    case $base_path . 'events/export':
        // Export events to CSV
        requireAuth();
        require 'routes/events/export.php';
        break;
        
    case $base_path . 'settings':
        // Settings page
        requireAuth();
        requireAdmin();
        require 'routes/settings/index.php';
        break;
        
    case $base_path . 'api/settings':
        // API endpoint for updating settings
        requireAuth();
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/api/settings.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case $base_path . 'api/settings/logo':
        // API endpoint for updating logo
        requireAuth();
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/api/settings_logo.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case $base_path . 'api/categories':
        // API endpoint for categories
        requireAuth();
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/api/categories_post.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case $base_path . 'api/event-types':
        // API endpoint for event types
        requireAuth();
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/api/event_types_post.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case $base_path . 'api/users':
        // API endpoint for users
        requireAuth();
        requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require 'routes/api/users_post.php';
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['error' => 'Method not allowed']);
        }
        break;
        
    case $base_path . 'api/dashboard/statistics':
        // API endpoint for dashboard statistics
        requireAuth();
        require 'routes/api/dashboard_statistics.php';
        break;
        
    default:
        // Check for variable paths like /events/1, /api/categories/5, etc.
        if (preg_match('#^' . $base_path . 'events/(\d+)$#', $request_uri, $matches)) {
            // Event details page
            requireAuth();
            $event_id = $matches[1];
            require 'routes/events/show.php';
            break;
        }
        
        if (preg_match('#^' . $base_path . 'events/(\d+)/edit$#', $request_uri, $matches)) {
            // Edit event page
            requireAuth();
            $event_id = $matches[1];
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                require 'routes/events/edit_post.php';
            } else {
                require 'routes/events/edit.php';
            }
            break;
        }
        
        if (preg_match('#^' . $base_path . 'events/(\d+)/delete$#', $request_uri, $matches)) {
            // Delete event
            requireAuth();
            $event_id = $matches[1];
            require 'routes/events/delete.php';
            break;
        }
        
        if (preg_match('#^' . $base_path . 'events/(\d+)/approve$#', $request_uri, $matches)) {
            // Approve event
            requireAuth();
            requireAdmin();
            $event_id = $matches[1];
            require 'routes/events/approve.php';
            break;
        }
        
        if (preg_match('#^' . $base_path . 'events/(\d+)/reject$#', $request_uri, $matches)) {
            // Reject event
            requireAuth();
            requireAdmin();
            $event_id = $matches[1];
            require 'routes/events/reject.php';
            break;
        }
        
        if (preg_match('#^' . $base_path . 'api/categories/(\d+)$#', $request_uri, $matches)) {
            // Delete category
            requireAuth();
            requireAdmin();
            $category_id = $matches[1];
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                require 'routes/api/categories_delete.php';
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        }
        
        if (preg_match('#^' . $base_path . 'api/event-types/(\d+)$#', $request_uri, $matches)) {
            // Delete event type
            requireAuth();
            requireAdmin();
            $type_id = $matches[1];
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                require 'routes/api/event_types_delete.php';
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        }
        
        if (preg_match('#^' . $base_path . 'api/users/(\d+)$#', $request_uri, $matches)) {
            // Delete user
            requireAuth();
            requireAdmin();
            $user_id = $matches[1];
            if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
                require 'routes/api/users_delete.php';
            } else {
                http_response_code(405); // Method Not Allowed
                echo json_encode(['error' => 'Method not allowed']);
            }
            break;
        }
        
        // If no route matches, return 404
        http_response_code(404);
        require 'views/error/404.php';
        break;
}
?>