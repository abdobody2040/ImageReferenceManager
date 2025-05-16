<?php
// Update logo

// Set content type to JSON for AJAX responses
header('Content-Type: application/json');

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Check if a file was uploaded
if (!isset($_FILES['logo']) || $_FILES['logo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'No logo file uploaded or upload error']);
    exit;
}

// Validate file
$file = $_FILES['logo'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Check file extension
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'svg'];
if (!in_array($file_ext, $allowed_extensions)) {
    echo json_encode(['success' => false, 'message' => 'Invalid file extension. Allowed: ' . implode(', ', $allowed_extensions)]);
    exit;
}

// Check file size (max 2MB)
$max_size = 2 * 1024 * 1024; // 2MB
if ($file_size > $max_size) {
    echo json_encode(['success' => false, 'message' => 'File size too large. Maximum: 2MB']);
    exit;
}

try {
    // Create upload directory if it doesn't exist
    $upload_dir = 'uploads/logos/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    // Generate unique filename
    $new_file_name = uniqid() . '.' . $file_ext;
    $upload_path = $upload_dir . $new_file_name;
    
    // Move uploaded file
    if (!move_uploaded_file($file_tmp, $upload_path)) {
        throw new Exception('Failed to move uploaded file');
    }
    
    // Update logo setting in database
    $logo_url = '/' . $upload_path; // Add leading slash for URL
    
    $stmt = $pdo->prepare("
        INSERT INTO app_setting (key, value) 
        VALUES ('logo', ?) 
        ON CONFLICT (key) 
        DO UPDATE SET value = EXCLUDED.value
    ");
    
    $stmt->execute([$logo_url]);
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Logo updated successfully',
        'logo_url' => $logo_url
    ]);
    
} catch (Exception $e) {
    // Log error
    error_log('Error updating logo: ' . $e->getMessage());
    
    // Return error response
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>