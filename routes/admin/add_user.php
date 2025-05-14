<?php
// Add user

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if the request has a JSON content type
if (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false) {
    // Get JSON data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
} else {
    // Get form data
    $data = $_POST;
}

// Validate data
$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$role = $data['role'] ?? '';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    exit;
}

if (empty($password)) {
    echo json_encode(['success' => false, 'message' => 'Password is required']);
    exit;
}

if (empty($role) || !in_array($role, ['admin', 'event_manager', 'medical_rep'])) {
    echo json_encode(['success' => false, 'message' => 'Valid role is required']);
    exit;
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

try {
    // Check if the email already exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $exists = $stmt->fetchColumn() > 0;
    
    if ($exists) {
        echo json_encode(['success' => false, 'message' => 'Email already in use']);
        exit;
    }
    
    // Hash the password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $pdo->prepare("
        INSERT INTO users (email, password_hash, role, created_at)
        VALUES (?, ?, ?, CURRENT_TIMESTAMP)
    ");
    
    $stmt->execute([$email, $password_hash, $role]);
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'User added successfully',
        'user_id' => $pdo->lastInsertId(),
        'email' => $email,
        'role' => $role
    ]);
    
} catch (PDOException $e) {
    // Log error
    error_log('Error adding user: ' . $e->getMessage());
    
    // Return error response
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>