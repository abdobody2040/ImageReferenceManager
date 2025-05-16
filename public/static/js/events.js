/**
 * JavaScript functionality for Events pages
 */

document.addEventListener('DOMContentLoaded', function() {
    // View toggle functionality (grid/list)
    const viewToggleButtons = document.querySelectorAll('.view-toggle button');
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    
    // Load stored view preference
    loadViewPreference();
    
    // Handle view toggle clicks
    viewToggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const viewType = this.getAttribute('data-view');
            toggleView(viewType);
            
            // Save preference
            localStorage.setItem('eventViewPreference', viewType);
            
            // Update active button
            viewToggleButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Clear filters button
    const clearFiltersBtn = document.getElementById('clear-filters');
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearFilters);
    }
});

// Apply filters function
function applyFilters() {
    document.querySelector('form').submit();
}

// Clear filters function
function clearFilters() {
    // Reset all form fields
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input:not([type="submit"]), select');
    
    inputs.forEach(input => {
        if (input.type === 'checkbox' || input.type === 'radio') {
            input.checked = false;
        } else {
            input.value = '';
        }
    });
    
    // Submit the form
    form.submit();
}

// Toggle between grid and list views
function toggleView(viewType) {
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    
    if (viewType === 'grid') {
        gridView.style.display = 'flex';
        listView.style.display = 'none';
    } else if (viewType === 'list') {
        gridView.style.display = 'none';
        listView.style.display = 'block';
    }
}

// Load user's preferred view
function loadViewPreference() {
    const viewPreference = localStorage.getItem('eventViewPreference');
    const viewToggleButtons = document.querySelectorAll('.view-toggle button');
    
    if (viewPreference) {
        toggleView(viewPreference);
        
        // Update active button
        viewToggleButtons.forEach(btn => {
            if (btn.getAttribute('data-view') === viewPreference) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
}