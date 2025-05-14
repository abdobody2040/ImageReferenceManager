document.addEventListener('DOMContentLoaded', function() {
    // Show loading spinner while dashboard initializes
    showSpinner('Loading dashboard data...');
    setupSpinnerTimeout(20000); // Set maximum wait time to 20 seconds
    
    // Track loading completion for all dashboard components
    const loadingTasks = {
        stats: false,
        categoryChart: false,
        typeChart: false,
        monthlyChart: false,
        requesterChart: false,
        pendingEvents: document.getElementById('pending-events-container') ? false : true
    };
    
    // Function to check if all loading is complete
    function checkAllLoaded() {
        const allLoaded = Object.values(loadingTasks).every(item => item === true);
        if (allLoaded) {
            hideSpinner();
        }
    }
    
    // Load dashboard statistics
    loadDashboardStats(loadingTasks, checkAllLoaded);
    
    // Initialize charts
    initCategoryChart(loadingTasks, checkAllLoaded);
    initTypeChart(loadingTasks, checkAllLoaded);
    initMonthlyChart(loadingTasks, checkAllLoaded);
    initRequesterChart(loadingTasks, checkAllLoaded);
    
    // Load pending events for admins
    if (document.getElementById('pending-events-container')) {
        loadPendingEvents(loadingTasks, checkAllLoaded);
    }
});

// Load dashboard statistics with robust error handling
function loadDashboardStats(loadingTasks, callback) {
    fetch('/api/dashboard/statistics')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            // Update statistics cards
            document.getElementById('total-events').textContent = data.total_events || '0';
            document.getElementById('upcoming-events').textContent = data.upcoming_events || '0';
            document.getElementById('online-events').textContent = data.online_events || '0';
            document.getElementById('offline-events').textContent = data.offline_events || '0';
            
            // Mark task as complete
            loadingTasks.stats = true;
            callback();
        })
        .catch(error => {
            console.error('Error loading dashboard statistics:', error);
            
            // Provide fallback values on error
            document.getElementById('total-events').textContent = '0';
            document.getElementById('upcoming-events').textContent = '0';
            document.getElementById('online-events').textContent = '0';
            document.getElementById('offline-events').textContent = '0';
            
            // Display error message
            const statsCards = document.querySelector('.row:first-of-type');
            if (statsCards) {
                const errorAlert = document.createElement('div');
                errorAlert.className = 'col-12 mt-2';
                errorAlert.innerHTML = `
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Could not load statistics data. Please try refreshing the page.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                `;
                statsCards.insertAdjacentElement('afterend', errorAlert);
            }
            
            // Mark task as complete even on error
            loadingTasks.stats = true;
            callback();
        });
}

// Load pending events for admin dashboard
function loadPendingEvents(loadingTasks, callback) {
    fetch('/api/dashboard/pending-events')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('pending-events-container');
            
            // Clear loading indicator
            container.innerHTML = '';
            
            if (!data.events || data.events.length === 0) {
                container.innerHTML = '<p class="text-center py-4">No pending events found.</p>';
                
                // Mark task as complete
                if (loadingTasks) {
                    loadingTasks.pendingEvents = true;
                    callback();
                }
                
                return;
            }
            
            // Create table for pending events
            const table = document.createElement('div');
            table.className = 'table-responsive';
            table.innerHTML = `
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Event Name</th>
                            <th>Requester</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="pending-events-body"></tbody>
                </table>
            `;
            
            container.appendChild(table);
            
            const tbody = document.getElementById('pending-events-body');
            
            // Add events to table
            data.events.forEach(event => {
                const row = document.createElement('tr');
                
                row.innerHTML = `
                    <td><a href="/events/${event.id}">${event.name || 'Unnamed Event'}</a></td>
                    <td>${event.requester_name || 'Unknown'}</td>
                    <td>${formatDateTime(event.start_datetime) || 'No date'}</td>
                    <td>
                        <a href="/events/${event.id}" class="btn btn-sm btn-info">View</a>
                        <a href="/events/${event.id}/approve" class="btn btn-sm btn-success">Approve</a>
                        <a href="/events/${event.id}/reject" class="btn btn-sm btn-danger">Reject</a>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
            
            // Mark task as complete
            if (loadingTasks) {
                loadingTasks.pendingEvents = true;
                callback();
            }
        })
        .catch(error => {
            console.error('Error loading pending events:', error);
            const container = document.getElementById('pending-events-container');
            container.innerHTML = `
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Failed to load pending events. Please try refreshing the page.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;
            
            // Mark task as complete even on error
            if (loadingTasks) {
                loadingTasks.pendingEvents = true;
                callback();
            }
        });
}

// Initialize category chart
function initCategoryChart(loadingTasks, callback) {
    fetch('/api/dashboard/categories')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            const ctx = document.getElementById('categoriesChart');
            if (!ctx) {
                if (loadingTasks) {
                    loadingTasks.categoryChart = true;
                    callback();
                }
                return;
            }
            
            // Handle empty data
            if (!data.labels || !data.values || data.labels.length === 0) {
                ctx.parentNode.innerHTML = '<div class="text-center py-5"><em>No category data available</em></div>';
                
                if (loadingTasks) {
                    loadingTasks.categoryChart = true;
                    callback();
                }
                return;
            }
            
            const colors = generateColors(data.labels.length);
            
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: colors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
            
            // Mark task as complete
            if (loadingTasks) {
                loadingTasks.categoryChart = true;
                callback();
            }
        })
        .catch(error => {
            console.error('Error loading category chart:', error);
            
            // Display error in chart container
            const ctx = document.getElementById('categoriesChart');
            if (ctx && ctx.parentNode) {
                ctx.parentNode.innerHTML = `
                    <div class="alert alert-warning m-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load category data.
                    </div>
                `;
            }
            
            // Mark task as complete even on error
            if (loadingTasks) {
                loadingTasks.categoryChart = true;
                callback();
            }
        });
}

// Initialize event type chart
function initTypeChart(loadingTasks, callback) {
    fetch('/api/dashboard/types')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            const ctx = document.getElementById('typesChart');
            if (!ctx) {
                if (loadingTasks) {
                    loadingTasks.typeChart = true;
                    callback();
                }
                return;
            }
            
            // Handle empty data
            if (!data.labels || !data.values || data.labels.length === 0) {
                ctx.parentNode.innerHTML = '<div class="text-center py-5"><em>No event type data available</em></div>';
                
                if (loadingTasks) {
                    loadingTasks.typeChart = true;
                    callback();
                }
                return;
            }
            
            const colors = generateColors(data.labels.length);
            
            new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: colors,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
            
            // Mark task as complete
            if (loadingTasks) {
                loadingTasks.typeChart = true;
                callback();
            }
        })
        .catch(error => {
            console.error('Error loading type chart:', error);
            
            // Display error in chart container
            const ctx = document.getElementById('typesChart');
            if (ctx && ctx.parentNode) {
                ctx.parentNode.innerHTML = `
                    <div class="alert alert-warning m-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load event type data.
                    </div>
                `;
            }
            
            // Mark task as complete even on error
            if (loadingTasks) {
                loadingTasks.typeChart = true;
                callback();
            }
        });
}

// Initialize monthly events chart
function initMonthlyChart(loadingTasks, callback) {
    fetch('/api/dashboard/monthly')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            const ctx = document.getElementById('monthlyChart');
            if (!ctx) {
                if (loadingTasks) {
                    loadingTasks.monthlyChart = true;
                    callback();
                }
                return;
            }
            
            // Handle empty data
            if (!data.labels || !data.values || data.labels.length === 0) {
                ctx.parentNode.innerHTML = '<div class="text-center py-5"><em>No monthly event data available</em></div>';
                
                if (loadingTasks) {
                    loadingTasks.monthlyChart = true;
                    callback();
                }
                return;
            }
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Number of Events',
                        data: data.values,
                        backgroundColor: 'rgba(78, 115, 223, 0.7)',
                        borderColor: 'rgba(78, 115, 223, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });
            
            // Mark task as complete
            if (loadingTasks) {
                loadingTasks.monthlyChart = true;
                callback();
            }
        })
        .catch(error => {
            console.error('Error loading monthly chart:', error);
            
            // Display error in chart container
            const ctx = document.getElementById('monthlyChart');
            if (ctx && ctx.parentNode) {
                ctx.parentNode.innerHTML = `
                    <div class="alert alert-warning m-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load monthly event data.
                    </div>
                `;
            }
            
            // Mark task as complete even on error
            if (loadingTasks) {
                loadingTasks.monthlyChart = true;
                callback();
            }
        });
}

// Initialize requester chart
function initRequesterChart(loadingTasks, callback) {
    fetch('/api/dashboard/requesters')
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server returned ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            const ctx = document.getElementById('requesterChart');
            if (!ctx) {
                if (loadingTasks) {
                    loadingTasks.requesterChart = true;
                    callback();
                }
                return;
            }
            
            // Handle empty data
            if (!data.labels || !data.values || data.labels.length === 0) {
                ctx.parentNode.innerHTML = '<div class="text-center py-5"><em>No requester data available</em></div>';
                
                if (loadingTasks) {
                    loadingTasks.requesterChart = true;
                    callback();
                }
                return;
            }
            
            const colors = generateColors(data.labels.length);
            
            new Chart(ctx, {
                type: 'polarArea',
                data: {
                    labels: data.labels,
                    datasets: [{
                        data: data.values,
                        backgroundColor: colors
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                        }
                    }
                }
            });
            
            // Mark task as complete
            if (loadingTasks) {
                loadingTasks.requesterChart = true;
                callback();
            }
        })
        .catch(error => {
            console.error('Error loading requester chart:', error);
            
            // Display error in chart container
            const ctx = document.getElementById('requesterChart');
            if (ctx && ctx.parentNode) {
                ctx.parentNode.innerHTML = `
                    <div class="alert alert-warning m-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Failed to load requester data.
                    </div>
                `;
            }
            
            // Mark task as complete even on error
            if (loadingTasks) {
                loadingTasks.requesterChart = true;
                callback();
            }
        });
}

// Generate colors for charts
function generateColors(count) {
    const colorPalette = [
        'rgba(78, 115, 223, 0.7)',
        'rgba(28, 200, 138, 0.7)',
        'rgba(54, 185, 204, 0.7)',
        'rgba(246, 194, 62, 0.7)',
        'rgba(231, 74, 59, 0.7)',
        'rgba(133, 135, 150, 0.7)',
        'rgba(105, 105, 105, 0.7)',
        'rgba(255, 159, 64, 0.7)'
    ];
    
    // If we need more colors than in the palette, generate them
    if (count > colorPalette.length) {
        const additionalColors = [];
        for (let i = 0; i < count - colorPalette.length; i++) {
            const r = Math.floor(Math.random() * 255);
            const g = Math.floor(Math.random() * 255);
            const b = Math.floor(Math.random() * 255);
            additionalColors.push(`rgba(${r}, ${g}, ${b}, 0.7)`);
        }
        return [...colorPalette, ...additionalColors];
    }
    
    return colorPalette.slice(0, count);
}

// Format date and time
function formatDateTime(dateTimeStr) {
    if (!dateTimeStr) return '';
    
    const date = new Date(dateTimeStr);
    return date.toLocaleString('en-US', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}