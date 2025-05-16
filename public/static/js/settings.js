/**
 * JavaScript functionality for Settings page
 */

document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation for all delete buttons
    initializeDeleteButtons();
    
    // Form submission handlers
    const generalSettingsForm = document.getElementById('general-settings-form');
    if (generalSettingsForm) {
        generalSettingsForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            updateSettings(data);
        });
    }
    
    const logoForm = document.getElementById('logo-form');
    if (logoForm) {
        logoForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('/settings/update-logo', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('Logo updated successfully!', 'success');
                    // Reload the page after a short delay to show the new logo
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showAlert('Error updating logo: ' + data.message, 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred while updating the logo.', 'danger');
            });
        });
    }
});

// Update general settings via AJAX
function updateSettings(data) {
    fetch('/settings/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Settings updated successfully!', 'success');
            // If app_name was changed, update the page title and header
            if (data.app_name) {
                document.title = document.title.replace(/^.*? - /, data.app_name + ' - ');
                document.querySelectorAll('.sidebar-brand-text').forEach(el => {
                    el.textContent = data.app_name;
                });
            }
        } else {
            showAlert('Error updating settings: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while updating settings.', 'danger');
    });
}

// Add user via AJAX
function addUser() {
    const email = document.getElementById('user_email').value;
    const password = document.getElementById('user_password').value;
    const role = document.getElementById('user_role').value;
    
    if (!email || !password || !role) {
        showAlert('Please fill in all fields.', 'danger');
        return;
    }
    
    const data = {
        email: email,
        password: password,
        role: role
    };
    
    fetch('/settings/users/add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('User added successfully!', 'success');
            // Reload the page to show the new user
            window.location.reload();
        } else {
            showAlert('Error adding user: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while adding the user.', 'danger');
    });
}

// Initialize delete buttons with confirmation
function initializeDeleteButtons() {
    document.querySelectorAll('.delete-item').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const itemType = this.getAttribute('data-item-type') || 'item';
            const url = this.getAttribute('href');
            
            if (!url) return;
            
            confirmAction(`Are you sure you want to delete this ${itemType}?`, () => {
                window.location.href = url;
            });
        });
    });
}

// Delete user via AJAX
async function deleteUser(id) {
    try {
        const response = await fetch(`/settings/users/delete/${id}`, {
            method: 'POST'
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('User deleted successfully!', 'success');
            // Remove the user row from the table
            document.querySelector(`tr[data-user-id="${id}"]`).remove();
        } else {
            showAlert('Error deleting user: ' + data.message, 'danger');
        }
    } catch (error) {
        console.error('Error:', error);
        showAlert('An error occurred while deleting the user.', 'danger');
    }
}

// Show alert message
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    // Find a good place to show the alert
    const tabContent = document.querySelector('.tab-pane.active');
    if (tabContent) {
        tabContent.prepend(alertDiv);
    } else {
        document.querySelector('.card-body').prepend(alertDiv);
    }
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Confirmation dialog
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}