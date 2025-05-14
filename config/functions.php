<?php
/**
 * Helper functions for the application
 */

// Add flash message to be displayed on the next page load
function flash($message, $type = 'info') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = [
        'message' => $message,
        'type' => $type
    ];
}

// Check if a user is authenticated
function isAuthenticated() {
    return isset($_SESSION['user_id']);
}

// Check if the current user is an admin
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Check if the current user is a medical rep
function isMedicalRep() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'medical_rep';
}

// Check if the current user is an event manager
function isEventManager() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'event_manager';
}

// Check if a user has permission to perform an action
function hasPermission($permission) {
    switch ($permission) {
        case 'manage_settings':
        case 'manage_users':
        case 'approve_events':
            return isAdmin();
        case 'create_event':
            return isAuthenticated(); // All authenticated users can create events
        default:
            return false;
    }
}

// Get a setting from the database
function getSetting($key, $default = null) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT value FROM app_setting WHERE key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : $default;
    } catch (PDOException $e) {
        // Log error and return default value
        error_log("Error getting setting $key: " . $e->getMessage());
        return $default;
    }
}

// Check if a filename has an allowed extension
function allowedFile($filename) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowed_extensions);
}

// Validate email format
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Format datetime for display
function formatDateTime($dt, $format = 'd M Y, H:i') {
    if (!$dt) return '';
    
    // If it's a string, convert it to a DateTime object
    if (is_string($dt)) {
        $dt = new DateTime($dt);
    }
    
    return $dt->format($format);
}

// Get appropriate badge class based on event type
function getEventBadgeClass($is_online) {
    return $is_online ? 'bg-info' : 'bg-warning';
}

// Check if the current page matches a given URL
function isCurrentPage($url) {
    $current_url = $_SERVER['REQUEST_URI'];
    
    // Remove query string if exists
    if (strpos($current_url, '?') !== false) {
        $current_url = substr($current_url, 0, strpos($current_url, '?'));
    }
    
    return $current_url === $url;
}

// Generate a secure random token
function generateToken($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

// Export events to a CSV file
function exportEventsToCSV($events) {
    // Create a temporary file handle
    $temp = fopen('php://temp', 'w');
    
    // Add CSV header
    fputcsv($temp, [
        'ID', 'Name', 'Requester', 'Type', 'Format', 'Start Date', 
        'End Date', 'Registration Deadline', 'Location', 'Status', 'Created At'
    ]);
    
    // Add event data
    foreach ($events as $event) {
        fputcsv($temp, [
            $event['id'],
            $event['name'],
            $event['requester_name'],
            $event['event_type'],
            $event['is_online'] ? 'Online' : 'In-Person',
            formatDateTime($event['start_datetime']),
            formatDateTime($event['end_datetime']),
            formatDateTime($event['registration_deadline']),
            $event['is_online'] ? 'N/A' : $event['governorate'],
            $event['status'],
            formatDateTime($event['created_at'])
        ]);
    }
    
    // Reset the file pointer to the beginning
    rewind($temp);
    
    // Return the file contents
    return stream_get_contents($temp);
}

// Get list of governorates in Egypt
function getGovernorates() {
    return [
        'Alexandria', 'Aswan', 'Asyut', 'Beheira', 'Beni Suef', 'Cairo', 
        'Dakahlia', 'Damietta', 'Faiyum', 'Gharbia', 'Giza', 'Ismailia', 
        'Kafr El Sheikh', 'Luxor', 'Matruh', 'Minya', 'Monufia', 'New Valley', 
        'North Sinai', 'Port Said', 'Qalyubia', 'Qena', 'Red Sea', 'Sharqia', 
        'Sohag', 'South Sinai', 'Suez'
    ];
}

// Create a pagination system
function paginate($query, $params = [], $page = 1, $per_page = 10) {
    global $pdo;
    
    // Calculate offset for the SQL query
    $offset = ($page - 1) * $per_page;
    
    // Get total count
    $count_query = preg_replace('/^SELECT (.*?) FROM /i', 'SELECT COUNT(*) FROM ', $query);
    $count_query = preg_replace('/ORDER BY .*/i', '', $count_query);
    
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    
    // Get paginated data
    $query .= " LIMIT $per_page OFFSET $offset";
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $items = $stmt->fetchAll();
    
    // Calculate total pages
    $total_pages = ceil($total / $per_page);
    
    return [
        'items' => $items,
        'total' => $total,
        'per_page' => $per_page,
        'current_page' => $page,
        'total_pages' => $total_pages,
        'has_next' => $page < $total_pages,
        'has_prev' => $page > 1
    ];
}

// Require authentication for a route
function requireAuth() {
    if (!isAuthenticated()) {
        flash('Please log in to access this page', 'warning');
        header('Location: /login');
        exit;
    }
}

// Require admin role for a route
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        flash('You do not have permission to access this page', 'danger');
        header('Location: /dashboard');
        exit;
    }
}

// Restrict medical rep from accessing certain routes
function notMedicalRep() {
    requireAuth();
    if (isMedicalRep()) {
        flash('You do not have permission to access this page', 'danger');
        header('Location: /dashboard');
        exit;
    }
}

?>