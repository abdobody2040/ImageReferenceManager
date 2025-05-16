<?php
// Redirect to login page if not authenticated, or to dashboard if authenticated
if (isAuthenticated()) {
    header('Location: /dashboard');
} else {
    header('Location: /login');
}
exit;
?>