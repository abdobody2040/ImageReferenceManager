<?php
// Destroy session data
session_unset();
session_destroy();

// Redirect to login page
flash('You have been logged out', 'success');
header('Location: /login');
exit;
?>