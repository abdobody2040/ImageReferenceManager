<?php
// In a production app, this would send a password reset email
// For now, we'll just display a message and redirect to login
$app_name = getSetting('app_name', 'PharmaEvents');

flash('Password reset functionality is not implemented yet. Please contact an administrator.', 'info');
header('Location: /login');
exit;
?>