/**
 * Emergency fix for dashboard charts
 * This script overrides the chart initialization functions to handle different API response formats
 */

document.addEventListener('DOMContentLoaded', function() {
    // Check if we're on the dashboard page
    if (!document.getElementById('dashboard-page')) {
        return;
    }

    console.log('Applying dashboard chart fixes...');

    // Helper function to generate random colors
    function generateColors(count) {
        const colors = [];
        for (let i = 0; i < count; i++) {
            const hue = (i * 137) % 360; // Use golden angle approximation for good distribution
            colors.push(`hsl(${hue}, 70%, 60%)`); 
        }
        return colors;
    }

    // Fix for category chart
    function initCategoryChartFixed() {
        const ctx = document.getElementById('categoriesChart');
        if (!ctx) return;

        // Clear any previous chart
        ctx.innerHTML = '';
        
        // Show loading indicator
        ctx.parentNode.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading category data...</p></div>';
        
        // We'll try multiple endpoints to find working data
        const endpoints = [
            '/api/dashboard/categories',
            '/api/categories'
        ];
        
        // Try each endpoint until one works
        tryNextEndpoint(0);
        
        function tryNextEndpoint(index) {
            if (index >= endpoints.length) {
                // All endpoints failed, show error
                ctx.parentNode.innerHTML = '<div class="alert alert-warning">Could not load category data</div>';
                return;
            }
            
            fetch(endpoints[index])
                .then(response => {
                    if (!response.ok) throw new Error('API response not OK');
                    return response.json();
                })
                .then(data => {
                    // Parse data regardless of format
                    let labels = [];
                    let values = [];
                    
                    if (data && data.labels && Array.isArray(data.labels)) {
                        // Standard format
                        labels = data.labels;
                        values = data.values || data.data || [];
                    } else if (Array.isArray(data)) {
                        // Array of objects format
                        labels = data.map(item => item.name);
                        values = data.map(item => item.count || item.value || 0);
                    }
                    
                    if (labels.length === 0) {
                        ctx.parentNode.innerHTML = '<div class="text-center py-3"><em>No category data available</em></div>';
                        return;
                    }
                    
                    // Restore the canvas
                    ctx.parentNode.innerHTML = '<canvas id="categoriesChart"></canvas>';
                    const newCtx = document.getElementById('categoriesChart');
                    
                    // Create chart
                    const colors = generateColors(labels.length);
                    new Chart(newCtx, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error(`Error with endpoint ${endpoints[index]}:`, error);
                    // Try next endpoint
                    tryNextEndpoint(index + 1);
                });
        }
    }
    
    // Fix for event type chart
    function initTypeChartFixed() {
        const ctx = document.getElementById('typesChart');
        if (!ctx) return;

        // Clear any previous chart
        ctx.innerHTML = '';
        
        // Show loading indicator
        ctx.parentNode.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading event type data...</p></div>';
        
        // We'll try multiple endpoints to find working data
        const endpoints = [
            '/api/dashboard/event-types',
            '/api/event-types/stats',
            '/api/dashboard/types'
        ];
        
        // Try each endpoint until one works
        tryNextEndpoint(0);
        
        function tryNextEndpoint(index) {
            if (index >= endpoints.length) {
                // All endpoints failed, show error
                ctx.parentNode.innerHTML = '<div class="alert alert-warning">Could not load event type data</div>';
                return;
            }
            
            fetch(endpoints[index])
                .then(response => {
                    if (!response.ok) throw new Error('API response not OK');
                    return response.json();
                })
                .then(data => {
                    // Parse data regardless of format
                    let labels = [];
                    let values = [];
                    
                    if (data && data.labels && Array.isArray(data.labels)) {
                        // Standard format
                        labels = data.labels;
                        values = data.values || data.data || [];
                    } else if (Array.isArray(data)) {
                        // Array of objects format
                        labels = data.map(item => item.name);
                        values = data.map(item => item.count || item.value || 0);
                    }
                    
                    if (labels.length === 0) {
                        ctx.parentNode.innerHTML = '<div class="text-center py-3"><em>No event type data available</em></div>';
                        return;
                    }
                    
                    // Restore the canvas
                    ctx.parentNode.innerHTML = '<canvas id="typesChart"></canvas>';
                    const newCtx = document.getElementById('typesChart');
                    
                    // Create chart
                    const colors = generateColors(labels.length);
                    new Chart(newCtx, {
                        type: 'pie',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: values,
                                backgroundColor: colors,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'right',
                                }
                            }
                        }
                    });
                })
                .catch(error => {
                    console.error(`Error with endpoint ${endpoints[index]}:`, error);
                    // Try next endpoint
                    tryNextEndpoint(index + 1);
                });
        }
    }
    
    // Fix for monthly chart
    function initMonthlyChartFixed() {
        const ctx = document.getElementById('monthlyChart');
        if (!ctx) return;

        // Clear any previous chart
        ctx.innerHTML = '';
        
        // Show loading indicator
        ctx.parentNode.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading monthly data...</p></div>';
        
        fetch('/api/dashboard/monthly-events')
            .then(response => {
                if (!response.ok) throw new Error('API response not OK');
                return response.json();
            })
            .then(data => {
                // Parse data regardless of format
                let labels = [];
                let values = [];
                
                if (data && data.labels && Array.isArray(data.labels)) {
                    // Standard format
                    labels = data.labels;
                    values = data.values || data.data || [];
                } else if (Array.isArray(data)) {
                    // Array of objects format
                    labels = data.map(item => item.month || item.label || item.name);
                    values = data.map(item => item.count || item.value || 0);
                }
                
                if (labels.length === 0) {
                    ctx.parentNode.innerHTML = '<div class="text-center py-3"><em>No monthly data available</em></div>';
                    return;
                }
                
                // Restore the canvas
                ctx.parentNode.innerHTML = '<canvas id="monthlyChart"></canvas>';
                const newCtx = document.getElementById('monthlyChart');
                
                // Create chart
                new Chart(newCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Events per Month',
                            data: values,
                            backgroundColor: 'rgba(54, 162, 235, 0.6)',
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
            })
            .catch(error => {
                console.error('Error loading monthly chart:', error);
                ctx.parentNode.innerHTML = '<div class="alert alert-warning">Could not load monthly event data</div>';
            });
    }
    
    // Fix for requester chart
    function initRequesterChartFixed() {
        const ctx = document.getElementById('requesterChart');
        if (!ctx) return;

        // Clear any previous chart
        ctx.innerHTML = '';
        
        // Show loading indicator
        ctx.parentNode.innerHTML = '<div class="text-center py-3"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading requester data...</p></div>';
        
        // We'll try multiple endpoints to find working data
        const endpoints = [
            '/api/dashboard/requesters',
            '/api/dashboard/events-by-requester'
        ];
        
        // Try each endpoint until one works
        tryNextEndpoint(0);
        
        function tryNextEndpoint(index) {
            if (index >= endpoints.length) {
                // All endpoints failed, show error
                ctx.parentNode.innerHTML = '<div class="alert alert-warning">Could not load requester data</div>';
                return;
            }
            
            fetch(endpoints[index])
                .then(response => {
                    if (!response.ok) throw new Error('API response not OK');
                    return response.json();
                })
                .then(data => {
                    // Parse data regardless of format
                    let labels = [];
                    let values = [];
                    
                    if (data && data.labels && Array.isArray(data.labels)) {
                        // Standard format
                        labels = data.labels;
                        values = data.values || data.data || [];
                    } else if (Array.isArray(data)) {
                        // Array of objects format
                        labels = data.map(item => item.name || item.requester_name);
                        values = data.map(item => item.count || item.value || 0);
                    }
                    
                    if (labels.length === 0) {
                        ctx.parentNode.innerHTML = '<div class="text-center py-3"><em>No requester data available</em></div>';
                        return;
                    }
                    
                    // Restore the canvas
                    ctx.parentNode.innerHTML = '<canvas id="requesterChart"></canvas>';
                    const newCtx = document.getElementById('requesterChart');
                    
                    // Create chart with horizontal bar chart
                    new Chart(newCtx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Events per Requester',
                                data: values,
                                backgroundColor: 'rgba(75, 192, 192, 0.6)',
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
                })
                .catch(error => {
                    console.error(`Error with endpoint ${endpoints[index]}:`, error);
                    // Try next endpoint
                    tryNextEndpoint(index + 1);
                });
        }
    }

    // Initialize all charts with our fixed versions
    setTimeout(function() {
        // Add dashboard ID to easily identify the page
        document.querySelector('.dashboard-container')?.setAttribute('id', 'dashboard-page');
        
        // Initialize all our fixed charts
        initCategoryChartFixed();
        initTypeChartFixed();
        initMonthlyChartFixed();
        initRequesterChartFixed();
        
        console.log('Dashboard charts fixed!');
    }, 500);
});