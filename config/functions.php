<?php
/**
 * Helper functions for the application
 */

// Authentication functions
function requireAuth() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit();
    }
}

function requireAdmin() {
    if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
        header('Location: /dashboard');
        exit();
    }
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function isMedicalRep() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'medical_rep';
}

// Flash message functions
function flash($message, $type = 'success') {
    $_SESSION['flash'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getFlash() {
    $flash = isset($_SESSION['flash']) ? $_SESSION['flash'] : null;
    unset($_SESSION['flash']);
    return $flash;
}

// Image upload functions
function allowedFile($filename) {
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    return in_array($ext, $allowed_extensions);
}

function uploadFile($file, $destination = 'static/uploads/') {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    if (!allowedFile($file['name'])) {
        return false;
    }
    
    $filename = uniqid() . '_' . basename($file['name']);
    $upload_path = $destination . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $filename;
    }
    
    return false;
}

// Date formatting functions
function formatDateTime($datetime, $format = 'd M Y, H:i') {
    $date = new DateTime($datetime);
    return $date->format($format);
}

// Application settings
function getSetting($key, $default = null) {
    global $pdo;
    
    $stmt = $pdo->prepare("SELECT value FROM app_setting WHERE key = :key");
    $stmt->execute(['key' => $key]);
    $result = $stmt->fetch();
    
    return $result ? $result['value'] : $default;
}

function updateSetting($key, $value) {
    global $pdo;
    
    // Check if setting exists
    $stmt = $pdo->prepare("SELECT id FROM app_setting WHERE key = :key");
    $stmt->execute(['key' => $key]);
    
    if ($stmt->fetch()) {
        // Update existing setting
        $stmt = $pdo->prepare("UPDATE app_setting SET value = :value WHERE key = :key");
        $stmt->execute(['key' => $key, 'value' => $value]);
    } else {
        // Insert new setting
        $stmt = $pdo->prepare("INSERT INTO app_setting (key, value) VALUES (:key, :value)");
        $stmt->execute(['key' => $key, 'value' => $value]);
    }
    
    return true;
}

// Get list of Egypt governorates
function getGovernorates() {
    return [
        'Alexandria', 'Aswan', 'Asyut', 'Beheira', 'Beni Suef', 'Cairo', 
        'Dakahlia', 'Damietta', 'Faiyum', 'Gharbia', 'Giza', 'Ismailia', 
        'Kafr El Sheikh', 'Luxor', 'Matruh', 'Minya', 'Monufia', 'New Valley', 
        'North Sinai', 'Port Said', 'Qalyubia', 'Qena', 'Red Sea', 
        'Sharqia', 'Sohag', 'South Sinai', 'Suez'
    ];
}

// Event helper functions
function getEventBadgeClass($isOnline) {
    return $isOnline ? 'bg-success' : 'bg-primary';
}

// Export events to CSV
function exportEventsToCSV($events) {
    $filename = 'events_export_' . date('Y-m-d') . '.csv';
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    
    $output = fopen('php://output', 'w');
    
    // Add CSV headers
    fputcsv($output, [
        'ID', 'Name', 'Requester', 'Type', 'Location', 'Start Date', 
        'End Date', 'Registration Deadline', 'Status', 'Created By'
    ]);
    
    // Add data rows
    foreach ($events as $event) {
        $location = $event['is_online'] ? 'Online' : $event['governorate'];
        
        fputcsv($output, [
            $event['id'],
            $event['name'],
            $event['requester_name'],
            $event['event_type_name'],
            $location,
            formatDateTime($event['start_datetime']),
            formatDateTime($event['end_datetime']),
            formatDateTime($event['registration_deadline']),
            $event['status'],
            $event['creator_email']
        ]);
    }
    
    fclose($output);
    exit;
}

// Email validation function
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}
?>