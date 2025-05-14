<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="/dashboard">
            <img src="/static/img/logo.svg" alt="<?php echo getSetting('app_name', 'PharmaEvents'); ?> Logo" width="36" height="36" class="me-2">
            <span><?php echo getSetting('app_name', 'PharmaEvents'); ?></span>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                    <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/dashboard' ? 'active' : ''; ?>" href="/dashboard">
                        <i class="fas fa-tachometer-alt me-1"></i> Dashboard
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/events' ? 'active' : ''; ?>" href="/events">
                        <i class="fas fa-calendar-alt me-1"></i> Events
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/events/create' ? 'active' : ''; ?>" href="/events/create">
                        <i class="fas fa-plus-circle me-1"></i> Create Event
                    </a>
                </li>
                
                <?php if (isAdmin()): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $_SERVER['REQUEST_URI'] === '/settings' ? 'active' : ''; ?>" href="/settings">
                            <i class="fas fa-cog me-1"></i> Settings
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
            
            <div class="d-flex">
                <?php include 'views/components/user_menu.php'; ?>
            </div>
        </div>
    </div>
</nav>