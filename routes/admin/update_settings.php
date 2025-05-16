<?php
// Update general settings

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
if (empty($data)) {
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit;
}

try {
    // Begin transaction
    $pdo->beginTransaction();
    
    // Process settings
    foreach ($data as $key => $value) {
        // Sanitize key name to prevent SQL injection
        $key = preg_replace('/[^a-zA-Z0-9_]/', '', $key);
        
        // Update or insert the setting
        $stmt = $pdo->prepare("
            INSERT INTO app_setting (key, value) 
            VALUES (?, ?) 
            ON CONFLICT (key) 
            DO UPDATE SET value = EXCLUDED.value
        ");
        
        $stmt->execute([$key, $value]);
    }
    
    // Commit transaction
    $pdo->commit();
    
    // Return success response
    echo json_encode([
        'success' => true, 
        'message' => 'Settings updated successfully',
        'app_name' => $data['app_name'] ?? null
    ]);
    
} catch (PDOException $e) {
    // Rollback transaction
    $pdo->rollBack();
    
    // Log error
    error_log('Error updating settings: ' . $e->getMessage());
    
    // Return error response
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>