document.addEventListener('DOMContentLoaded', function() {
    // Load dashboard statistics
    loadDashboardStats();
    
    // Initialize charts
    initCategoryChart();
    initTypeChart();
    initMonthlyChart();
    initRequesterChart();
    
    // Load pending events for admins
    if (document.getElementById('pending-events-container')) {
        loadPendingEvents();
    }
});

// Load dashboard statistics
function loadDashboardStats() {
    fetch('/api/dashboard/statistics')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Update statistics cards
            document.getElementById('total-events').textContent = data.total_events;
            document.getElementById('upcoming-events').textContent = data.upcoming_events;
            document.getElementById('online-events').textContent = data.online_events;
            document.getElementById('offline-events').textContent = data.offline_events;
        })
        .catch(error => {
            console.error('Error loading dashboard statistics:', error);
        });
}

// Load pending events for admin dashboard
function loadPendingEvents() {
    fetch('/api/dashboard/pending-events')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            const container = document.getElementById('pending-events-container');
            
            // Clear loading indicator
            container.innerHTML = '';
            
            if (data.events.length === 0) {
                container.innerHTML = '<p class="text-center py-4">No pending events found.</p>';
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
                    <td><a href="/events/${event.id}">${event.name}</a></td>
                    <td>${event.requester_name}</td>
                    <td>${formatDateTime(event.start_datetime)}</td>
                    <td>
                        <a href="/events/${event.id}" class="btn btn-sm btn-info">View</a>
                        <a href="/events/${event.id}/approve" class="btn btn-sm btn-success">Approve</a>
                        <a href="/events/${event.id}/reject" class="btn btn-sm btn-danger">Reject</a>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading pending events:', error);
            const container = document.getElementById('pending-events-container');
            container.innerHTML = '<p class="text-center py-4 text-danger">Failed to load pending events. Please try again later.</p>';
        });
}

// Initialize category chart
function initCategoryChart() {
    fetch('/api/dashboard/categories')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('categoriesChart');
            if (!ctx) return;
            
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
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Initialize event type chart
function initTypeChart() {
    fetch('/api/dashboard/types')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('typesChart');
            if (!ctx) return;
            
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
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Initialize monthly events chart
function initMonthlyChart() {
    fetch('/api/dashboard/monthly')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('monthlyChart');
            if (!ctx) return;
            
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
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

// Initialize requester chart
function initRequesterChart() {
    fetch('/api/dashboard/requesters')
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('requesterChart');
            if (!ctx) return;
            
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
        })
        .catch(error => {
            console.error('Error:', error);
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