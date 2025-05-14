<?php
$pageTitle = '404 - Page Not Found - ' . getSetting('app_name', 'PharmaEvents');
$app_name = getSetting('app_name', 'PharmaEvents');

ob_start();
?>

<div class="container-fluid">
    <div class="text-center mt-5">
        <div class="error mx-auto" data-text="404">404</div>
        <p class="lead text-gray-800 mb-4">Page Not Found</p>
        <p class="text-gray-500 mb-0">It looks like you found a glitch in the matrix...</p>
        <?php if (isAuthenticated()): ?>
            <a href="/dashboard">&larr; Back to Dashboard</a>
        <?php else: ?>
            <a href="/login">&larr; Back to Login</a>
        <?php endif; ?>
    </div>
</div>

<style>
    .error {
        color: #5a5c69;
        font-size: 7rem;
        position: relative;
        line-height: 1;
        width: 12.5rem;
        margin: 0 auto;
    }
    .error:before {
        content: attr(data-text);
        position: absolute;
        left: -2px;
        top: 0;
        width: 100%;
        height: 100%;
        color: #e74a3b;
        opacity: 0.1;
    }
</style>

<?php
$content = ob_get_clean();
include 'views/layouts/main.php';
?>