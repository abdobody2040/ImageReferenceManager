<?php
// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Process login form submission
try {
    // Sanitize inputs
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) ?? '';
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Store email in session for form repopulation on error
    $_SESSION['form_email'] = $email;
    
    // Validate input
    if (empty($email) || empty($password)) {
        flash('Email and password are required', 'danger');
        header('Location: /login');
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Please enter a valid email address', 'danger');
        header('Location: /login');
        exit;
    }
    
    // Check database connection
    if (!isset($pdo) || !($pdo instanceof PDO)) {
        error_log('Database connection not available in login_post.php');
        flash('System error. Please try again later.', 'danger');
        header('Location: /login');
        exit;
    }
    
    // Check if user exists - use a transaction for security
    $pdo->beginTransaction();
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        $pdo->commit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        error_log('Database query error in login: ' . $e->getMessage());
        flash('Login system temporarily unavailable. Please try again later.', 'danger');
        header('Location: /login');
        exit;
    }
    
    if (!$user) {
        // User not found - use same message as password failure for security
        flash('Invalid email or password', 'danger');
        header('Location: /login');
        exit;
    }
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        // Add a small delay to prevent timing attacks
        usleep(random_int(100000, 500000)); // 0.1 to 0.5 seconds
        
        flash('Invalid email or password', 'danger');
        header('Location: /login');
        exit;
    }
    
    // Clear any previous session data for security
    $_SESSION = array();
    
    // Generate new session ID to prevent session fixation
    session_regenerate_id(true);
    
    // Set session variables with user data
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['last_activity'] = time();
    $_SESSION['login_time'] = time();
    
    // Implement remember me functionality
    if ($remember) {
        // Set a longer session timeout
        ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 days
        $_SESSION['remember_me'] = true;
    }
    
    // Clear form email from session
    unset($_SESSION['form_email']);
    
    // Log successful login
    error_log("User {$user['email']} logged in successfully");
    
    // Redirect to dashboard
    flash('Logged in successfully!', 'success');
    header('Location: /dashboard');
    exit;
    
} catch (Exception $e) {
    // Catch any unexpected errors
    error_log('Unexpected error in login process: ' . $e->getMessage());
    flash('An unexpected error occurred. Please try again.', 'danger');
    header('Location: /login');
    exit;
}
?>