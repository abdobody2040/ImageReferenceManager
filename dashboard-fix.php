<?php
/**
 * Dashboard Fix Testing Page
 * This page can help us test that the dashboard fixes are working properly
 */

// Include database connection if needed
// require_once 'config/database.php';

// Set content type to HTML
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Fix Tester</title>
    
    <!-- Load emergency fix scripts first -->
    <script>
    // Direct inline fix for loading overlay - runs before anything else
    (function() {
        console.log("Loading overlay fix running...");
        function hideLoading() {
            var overlays = document.querySelectorAll('.loading-overlay, #loading-overlay, #loading_overlay, .spinner-container, .loading-indicator');
            overlays.forEach(function(overlay) {
                if (overlay) {
                    overlay.style.display = 'none';
                    console.log("Found and hidden overlay element");
                }
            });
            
            // Also reset any spinner counters
            if (typeof window.spinnerCounter !== 'undefined') window.spinnerCounter = 0;
            if (typeof window.loadingCounter !== 'undefined') window.loadingCounter = 0;
        }
        
        // Run immediately
        hideLoading();
        
        // Run when DOM loads
        document.addEventListener('DOMContentLoaded', hideLoading);
        
        // Run periodically
        setInterval(hideLoading, 2000);
    })();
    </script>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Chart.js for dashboard charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    <style>
        .dashboard-container { 
            padding: 20px;
        }
        .chart-container {
            height: 300px;
            margin-bottom: 20px;
            position: relative;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .loading-overlay-content {
            background: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Test loading overlay -->
    <div class="loading-overlay">
        <div class="loading-overlay-content">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3">Please wait while we process your request...</p>
        </div>
    </div>

    <div class="container dashboard-container">
        <h1 class="mb-4">Dashboard Fix Tester</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Categories Chart</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="categoriesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Event Types Chart</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="typesChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Monthly Events</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="monthlyChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5>Top Requesters</h5>
                    </div>
                    <div class="card-body">
                        <div class="chart-container">
                            <canvas id="requesterChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Load JavaScript -->
    <script src="static/js/fix-loading-overlay.js"></script>
    <script src="static/js/universal-loading-fix.js"></script>
    <script src="static/js/emergency-fix.js"></script>
    <script src="static/js/fix-dashboard-charts.js"></script>
    
    <script>
    // Test data for charts
    const categoryData = {
        labels: ['Medical Conference', 'Workshop', 'Webinar', 'Product Launch', 'Training'],
        values: [15, 12, 8, 5, 3]
    };
    
    const typeData = {
        labels: ['In-Person', 'Hybrid', 'Virtual', 'CME', 'Promotional'],
        values: [20, 15, 12, 8, 5]
    };
    
    const monthlyData = {
        labels: ['Jan 2023', 'Feb 2023', 'Mar 2023', 'Apr 2023', 'May 2023', 'Jun 2023', 
                'Jul 2023', 'Aug 2023', 'Sep 2023', 'Oct 2023', 'Nov 2023', 'Dec 2023'],
        values: [3, 5, 8, 10, 12, 15, 12, 10, 8, 6, 4, 7]
    };
    
    const requesterData = {
        labels: ['Dr. Smith', 'Dr. Johnson', 'Dr. Williams', 'Dr. Brown', 'Dr. Jones'],
        values: [8, 6, 5, 4, 3]
    };
    
    // Function to initialize charts
    function initCharts() {
        // Initialize category chart
        const categoryCtx = document.getElementById('categoriesChart');
        if (categoryCtx) {
            new Chart(categoryCtx, {
                type: 'doughnut',
                data: {
                    labels: categoryData.labels,
                    datasets: [{
                        data: categoryData.values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Initialize type chart
        const typeCtx = document.getElementById('typesChart');
        if (typeCtx) {
            new Chart(typeCtx, {
                type: 'pie',
                data: {
                    labels: typeData.labels,
                    datasets: [{
                        data: typeData.values,
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        }
        
        // Initialize monthly chart
        const monthlyCtx = document.getElementById('monthlyChart');
        if (monthlyCtx) {
            new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: monthlyData.labels,
                    datasets: [{
                        label: 'Events per Month',
                        data: monthlyData.values,
                        backgroundColor: 'rgba(54, 162, 235, 0.7)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
        
        // Initialize requester chart
        const requesterCtx = document.getElementById('requesterChart');
        if (requesterCtx) {
            new Chart(requesterCtx, {
                type: 'bar',
                data: {
                    labels: requesterData.labels,
                    datasets: [{
                        label: 'Events per Requester',
                        data: requesterData.values,
                        backgroundColor: 'rgba(75, 192, 192, 0.7)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }
    
    // Initialize charts when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        // Force hide loading overlay after a short delay
        setTimeout(function() {
            const overlay = document.querySelector('.loading-overlay');
            if (overlay) {
                overlay.style.display = 'none';
            }
        }, 1000);
        
        // Init charts
        initCharts();
    });
    </script>
</body>
</html>