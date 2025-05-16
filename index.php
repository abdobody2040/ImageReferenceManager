<?php
/**
 * PharmaEvents - Event Management Application
 * Main Router
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Set secure session parameters
    $session_options = [
        'cookie_httponly' => true,     // Prevent JavaScript access to session cookie
        'cookie_secure' => isset($_SERVER['HTTPS']), // Secure flag when using HTTPS
        'cookie_samesite' => 'Lax',    // Prevent CSRF attacks
        'use_strict_mode' => true,     // Help mitigate session fixation
        'gc_maxlifetime' => 3600       // Session timeout (1 hour)
    ];
    session_start($session_options);
}

// Include configuration files
require_once 'config/database.php';
require_once 'config/functions.php';

// Get the request path
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_path = trim($request_path, '/');

// Route mapping
$routes = [
    '' => 'routes/home.php',
    'login' => 'routes/auth/login.php',
    'dashboard' => 'routes/dashboard.php',
    'events' => 'routes/events/index.php',
    'profile' => 'routes/profile.php',
    '/logout' => 'routes/auth/logout.php',
    '/forgot-password' => 'routes/auth/forgot_password.php',
    '/events/create' => 'routes/events/create.php',
    '/events/export' => 'routes/events/export.php',
    '/profile/change-password' => 'routes/profile/change_password.php',
    '/settings' => 'routes/admin/settings.php',
    '/settings/update' => 'routes/admin/update_settings.php',
    '/settings/update-logo' => 'routes/admin/update_logo.php',
    '/settings/event-types/add' => 'routes/admin/add_event_type.php',
    '/settings/event-types/delete' => 'routes/admin/delete_event_type.php',
    '/settings/categories/add' => 'routes/admin/add_category.php',
    '/settings/categories/delete' => 'routes/admin/delete_category.php',
    '/settings/users/add' => 'routes/admin/add_user.php',
    '/settings/users/delete' => 'routes/admin/delete_user.php',
    '/api/dashboard/statistics' => 'routes/api/dashboard_statistics.php',
    '/api/dashboard/pending-events' => 'routes/api/dashboard_pending_events.php',
    '/api/dashboard/categories' => 'routes/api/dashboard_categories.php',
    '/api/dashboard/types' => 'routes/api/dashboard_types.php',
    '/api/dashboard/monthly' => 'routes/api/dashboard_monthly.php',
    '/api/dashboard/requesters' => 'routes/api/dashboard_requesters.php',
    '/api/events' => 'routes/api/events.php'
];

// Route for POST requests to login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['REQUEST_URI'] === '/login') {
    require_once 'routes/auth/login_post.php';
    exit;
}

// Handle dynamic routes (those with parameters)
if (preg_match('#^/events/(\d+)$#', $request_path, $matches)) {
    $event_id = $matches[1];
    $_GET['id'] = $event_id;
    require_once 'routes/events/show.php';
    exit;
}

if (preg_match('#^/events/(\d+)/edit$#', $request_path, $matches)) {
    $event_id = $matches[1];
    $_GET['id'] = $event_id;
    require_once 'routes/events/edit.php';
    exit;
}

if (preg_match('#^/events/(\d+)/delete$#', $request_path, $matches)) {
    $event_id = $matches[1];
    $_GET['id'] = $event_id;
    require_once 'routes/events/delete.php';
    exit;
}

if (preg_match('#^/events/(\d+)/approve$#', $request_path, $matches)) {
    $event_id = $matches[1];
    $_GET['id'] = $event_id;
    require_once 'routes/events/approve.php';
    exit;
}

if (preg_match('#^/events/(\d+)/reject$#', $request_path, $matches)) {
    $event_id = $matches[1];
    $_GET['id'] = $event_id;
    require_once 'routes/events/reject.php';
    exit;
}

require_once 'config/error_handler.php';

// Check if route exists
if (isset($routes[$request_path])) {
    // Define authentication requirements for routes
    $auth_required = [
        '/dashboard',
        '/events',
        '/events/create',
        '/events/export',
        '/profile',
        '/profile/change-password',
    ];
    
    $admin_required = [
        '/settings',
        '/settings/update',
        '/settings/update-logo',
        '/settings/event-types/add',
        '/settings/event-types/delete',
        '/settings/categories/add',
        '/settings/categories/delete',
        '/settings/users/add',
        '/settings/users/delete',
    ];
    
    // Check authentication requirements
    if (in_array($request_path, $auth_required)) {
        requireAuth();
    }
    
    if (in_array($request_path, $admin_required)) {
        requireAdmin();
    }
    
    // Include the appropriate route file
    require_once $routes[$request_path];
} else {
    // Route not found - show 404 page
    http_response_code(404);
    include 'views/errors/404.php';
}
?>