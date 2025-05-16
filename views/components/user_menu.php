<div class="dropdown">
    <a class="btn btn-outline-light dropdown-toggle" href="#" role="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fas fa-user-circle me-1"></i>
        <?php echo $_SESSION['user_email'] ?? 'User'; ?>
        <?php if (isAdmin()): ?>
            <span class="badge bg-primary ms-1">Admin</span>
        <?php elseif (isMedicalRep()): ?>
            <span class="badge bg-info ms-1">Med Rep</span>
        <?php else: ?>
            <span class="badge bg-secondary ms-1">Manager</span>
        <?php endif; ?>
    </a>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
        <li><a class="dropdown-item" href="/settings"><i class="fas fa-cog me-2"></i>Settings</a></li>
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item" href="/logout"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
    </ul>
</div>