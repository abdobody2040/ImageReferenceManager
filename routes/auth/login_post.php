<?php
// Process login form submission
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$remember = isset($_POST['remember']);

// Validate input
if (empty($email) || empty($password)) {
    flash('Email and password are required', 'danger');
    header('Location: /login');
    exit;
}

if (!validateEmail($email)) {
    flash('Please enter a valid email address', 'danger');
    header('Location: /login');
    exit;
}

// Check if user exists
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user) {
    // User not found
    flash('Invalid email or password', 'danger');
    header('Location: /login');
    exit;
}

// Verify password
if (!password_verify($password, $user['password_hash'])) {
    // Invalid password
    flash('Invalid email or password', 'danger');
    header('Location: /login');
    exit;
}

// Set session variables
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

// Implement remember me functionality if needed
if ($remember) {
    // Set a longer session timeout or a persistent cookie
    ini_set('session.cookie_lifetime', 30 * 24 * 60 * 60); // 30 days
}

// Redirect to dashboard
flash('Logged in successfully!', 'success');
header('Location: /dashboard');
exit;
?>