<?php
// Create event page

// Handle POST request (form submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required_fields = [
        'name', 'requester_name', 'event_type_id',
        'start_date', 'start_time', 'end_date', 'end_time',
        'registration_deadline_date', 'registration_deadline_time'
    ];
    
    $errors = [];
    
    // Check required fields
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = "Field '$field' is required.";
        }
    }
    
    // Check if this is an online event
    $is_online = isset($_POST['is_online']) ? 1 : 0;
    
    // If it's not an online event, check venue fields
    if (!$is_online) {
        if (empty($_POST['governorate'])) {
            $errors[] = "Governorate is required for in-person events.";
        }
        
        if (empty($_POST['venue_id'])) {
            $errors[] = "Venue is required for in-person events.";
        }
    }
    
    // Validate and process dates
    try {
        $start_datetime = new DateTime($_POST['start_date'] . ' ' . $_POST['start_time']);
        $end_datetime = new DateTime($_POST['end_date'] . ' ' . $_POST['end_time']);
        $registration_deadline = new DateTime($_POST['registration_deadline_date'] . ' ' . $_POST['registration_deadline_time']);
        
        // Check that end date is after start date
        if ($end_datetime <= $start_datetime) {
            $errors[] = "End date/time must be after start date/time.";
        }
        
        // Check that registration deadline is before start date
        if ($registration_deadline >= $start_datetime) {
            $errors[] = "Registration deadline must be before the event start date/time.";
        }
    } catch (Exception $e) {
        $errors[] = "Invalid date/time format: " . $e->getMessage();
    }
    
    // Process image
    $image_url = null;
    $image_file = null;
    
    if ($_POST['image_source'] === 'url' && !empty($_POST['image_url'])) {
        $image_url = filter_var($_POST['image_url'], FILTER_VALIDATE_URL);
        if (!$image_url) {
            $errors[] = "Invalid image URL.";
        }
    } elseif ($_POST['image_source'] === 'file' && isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['image_file']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error uploading image: " . $_FILES['image_file']['error'];
        } else {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($_FILES['image_file']['type'], $allowed_types)) {
                $errors[] = "Invalid image type. Allowed types: JPG, PNG, GIF, WEBP.";
            } else {
                // Create upload directory if it doesn't exist
                $upload_dir = 'uploads/events/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                // Generate unique filename
                $filename = uniqid() . '_' . basename($_FILES['image_file']['name']);
                $upload_path = $upload_dir . $filename;
                
                // Move uploaded file
                if (move_uploaded_file($_FILES['image_file']['tmp_name'], $upload_path)) {
                    $image_file = '/' . $upload_path;
                } else {
                    $errors[] = "Failed to move uploaded file.";
                }
            }
        }
    }
    
    // If no errors, insert the event into the database
    if (empty($errors)) {
        try {
            // Begin transaction
            $pdo->beginTransaction();
            
            // Insert event
            $stmt = $pdo->prepare("
                INSERT INTO event (
                    name, requester_name, is_online, image_url, image_file,
                    start_datetime, end_datetime, registration_deadline,
                    governorate, venue_id, service_request_id, employee_code_id,
                    event_type_id, description, user_id, status
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
                )
            ");
            
            $stmt->execute([
                $_POST['name'],
                $_POST['requester_name'],
                $is_online,
                $image_url,
                $image_file,
                $start_datetime->format('Y-m-d H:i:s'),
                $end_datetime->format('Y-m-d H:i:s'),
                $registration_deadline->format('Y-m-d H:i:s'),
                $is_online ? null : $_POST['governorate'],
                $is_online ? null : $_POST['venue_id'],
                empty($_POST['service_request_id']) ? null : $_POST['service_request_id'],
                empty($_POST['employee_code_id']) ? null : $_POST['employee_code_id'],
                $_POST['event_type_id'],
                $_POST['description'],
                $_SESSION['user_id'],
                // If user is medical rep, event needs approval
                isMedicalRep() ? 'pending' : 'approved'
            ]);
            
            $event_id = $pdo->lastInsertId();
            
            // Insert event categories
            if (!empty($_POST['categories']) && is_array($_POST['categories'])) {
                $category_stmt = $pdo->prepare("
                    INSERT INTO event_category_junction (event_id, category_id)
                    VALUES (?, ?)
                ");
                
                foreach ($_POST['categories'] as $category_id) {
                    $category_stmt->execute([$event_id, $category_id]);
                }
            }
            
            // Commit transaction
            $pdo->commit();
            
            // Set success message and redirect
            flash('Event created successfully!', 'success');
            header('Location: /events/' . $event_id);
            exit;
            
        } catch (PDOException $e) {
            // Roll back transaction
            $pdo->rollBack();
            
            // Log error and add to errors array
            error_log('Error creating event: ' . $e->getMessage());
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
    
    // If we got here, there were errors
    // Convert errors array to flash messages
    foreach ($errors as $error) {
        flash($error, 'danger');
    }
}

// Get data for form dropdowns
$stmt = $pdo->query("SELECT id, name FROM event_type ORDER BY name");
$event_types = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM event_category ORDER BY name");
$categories = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name, governorate FROM venue ORDER BY governorate, name");
$venues = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, name FROM service_request ORDER BY name");
$service_requests = $stmt->fetchAll();

$stmt = $pdo->query("SELECT id, code, name FROM employee_code ORDER BY code");
$employee_codes = $stmt->fetchAll();

// Get list of governorates
$governorates = getGovernorates();

// Include the view
include 'views/events/create.php';
?>