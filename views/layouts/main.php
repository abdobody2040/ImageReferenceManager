<!DOCTYPE html>
<html lang="en" data-bs-theme="<?php echo getSetting('theme', 'light'); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : getSetting('app_name', 'PharmaEvents'); ?> - Secure & Compliant Event Management</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Flatpickr for date/time pickers -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Select2 for enhanced select boxes -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@1.5.2/dist/select2-bootstrap4.min.css" rel="stylesheet">
    
    <!-- Chart.js for dashboard charts -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/static/css/styles.css">
    
    <!-- Dark Mode Adjustments -->
    <style>
    [data-bs-theme="dark"] {
        --bs-body-bg: #212529;
        --bs-body-color: #dee2e6;
    }
    
    [data-bs-theme="dark"] .bg-white {
        background-color: #2c3034 !important;
    }
    
    [data-bs-theme="dark"] .bg-light {
        background-color: #343a40 !important;
    }
    
    [data-bs-theme="dark"] .card {
        background-color: #2c3034;
        border-color: #495057;
    }
    
    [data-bs-theme="dark"] .settings-header {
        border-bottom-color: rgba(255, 255, 255, 0.1);
    }
    
    [data-bs-theme="dark"] .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    </style>
    
    <!-- Page-specific CSS -->
    <?php if (isset($pageStyles)) echo $pageStyles; ?>
</head>
<body>
    <!-- Loading Overlay -->
    <div id="loading_overlay" class="loading-overlay">
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="loading-message">Please wait while we process your request...</div>
        </div>
    </div>
    
    <?php if (isset($_SESSION['user_id'])): ?>
        <?php include 'views/components/nav.php'; ?>
    <?php endif; ?>
    
    <div class="container-fluid py-4">
        <!-- Flash Messages -->
        <?php 
        $flash = getFlash();
        if ($flash): 
        ?>
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-<?php echo $flash['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $flash['message']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <!-- Main Content -->
        <?php if (isset($content)) echo $content; ?>
    </div>
    
    <!-- jQuery (needed for Select2) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Flatpickr for date/time pickers -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    
    <!-- Select2 for enhanced select boxes -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <!-- Main JavaScript -->
    <script src="/static/js/main.js"></script>
    
    <!-- Page-specific JavaScript -->
    <?php if (isset($pageScripts)) echo $pageScripts; ?>
</body>
</html>