// Settings page JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Handle application name update
    const nameInput = document.getElementById('app_name');
    const updateNameBtn = document.getElementById('update_name');
    if (nameInput && updateNameBtn) {
        updateNameBtn.addEventListener('click', function() {
            const name = nameInput.value;
            updateSettings({ name: name });
        });
    }

    // Handle theme toggle
    const themeToggles = document.querySelectorAll('input[name="theme"]');
    // Set initial theme based on current setting
    themeToggles.forEach(toggle => {
        if (document.documentElement.getAttribute('data-bs-theme') === toggle.value) {
            toggle.checked = true;
        }
        
        toggle.addEventListener('change', function() {
            const theme = this.value;
            // Update the HTML element theme attribute
            document.documentElement.setAttribute('data-bs-theme', theme);
            // Save the setting to server
            updateSettings({ theme: theme });
        });
    });

    // Handle logo upload
    const logoInput = document.getElementById('app_logo');
    const uploadLogoBtn = document.getElementById('upload_logo');
    if (logoInput && uploadLogoBtn) {
        uploadLogoBtn.addEventListener('click', function() {
            const file = logoInput.files[0];
            if (file) {
                const formData = new FormData();
                formData.append('logo', file);

                fetch('/api/settings/logo', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showAlert('Logo updated successfully', 'success');
                    } else {
                        showAlert('Error updating logo', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error updating logo', 'danger');
                });
            }
        });
    }

    // Category management
    const addCategoryForm = document.getElementById('add_category_form');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const categoryName = document.getElementById('category_name').value;

            const formData = new FormData();
            formData.append('category_name', categoryName);

            fetch('/api/categories', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    const categoryList = document.getElementById('category_list');
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', data.id);
                    row.innerHTML = `
                        <td>${data.name}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-danger btn-delete-category" data-id="${data.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    categoryList.appendChild(row);
                    document.getElementById('category_name').value = '';
                    showAlert('Category added successfully', 'success');
                    initializeDeleteButtons();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error adding category', 'danger');
            });
        });
    }

    // Event type management
    const addTypeForm = document.getElementById('add_type_form');
    if (addTypeForm) {
        addTypeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const typeName = document.getElementById('type_name').value;

            const formData = new FormData();
            formData.append('type_name', typeName);

            fetch('/api/event-types', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.id) {
                    const typeList = document.getElementById('type_list');
                    const row = document.createElement('tr');
                    row.setAttribute('data-id', data.id);
                    row.innerHTML = `
                        <td>${data.name}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-danger btn-delete-type" data-id="${data.id}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </td>
                    `;
                    typeList.appendChild(row);
                    document.getElementById('type_name').value = '';
                    showAlert('Event type added successfully', 'success');
                    initializeDeleteButtons();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('Error adding event type', 'danger');
            });
        });
    }

        // User management
    const addUserForm = document.getElementById('add_user_form');
    if (addUserForm) {
        addUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addUser();
        });
    }

    // Initialize delete buttons
    initializeDeleteButtons();
});

function updateSettings(settings) {
    fetch('/api/settings', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Settings updated successfully', 'success');
            if (settings.name) {
                document.title = settings.name;
            }
        } else {
            showAlert('Error updating settings', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('Error updating settings', 'danger');
    });
}

// Add a new user
function addUser() {
    const emailInput = document.getElementById('user_email');
    const passwordInput = document.getElementById('user_password');
    const roleSelect = document.getElementById('user_role');
    
    const email = emailInput.value.trim();
    const password = passwordInput.value;
    const role = roleSelect.value;
    
    if (!email || !password || !role) {
        showAlert('Please fill all fields', 'danger');
        return;
    }
    
    // Validate email format
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    if (!emailPattern.test(email)) {
        showAlert('Please enter a valid email address', 'danger');
        return;
    }
    
    // Validate password length
    if (password.length < 6) {
        showAlert('Password must be at least 6 characters long', 'danger');
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('email', email);
    formData.append('password', password);
    formData.append('role', role);
    
    // Send request
    fetch('/api/users', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.error || 'Failed to add user'); });
        }
        return response.json();
    })
    .then(data => {
        // Add new user to the list
        const userList = document.getElementById('user_list');
        const row = document.createElement('tr');
        row.setAttribute('data-id', data.id);
        
        // Determine badge color based on role
        let badgeClass = 'bg-secondary';
        let roleDisplay = data.role.toUpperCase();
        
        if (data.role === 'admin') {
            badgeClass = 'bg-primary';
        } else if (data.role === 'medical_rep') {
            badgeClass = 'bg-info';
            roleDisplay = 'MEDICAL REP';
        }
        
        row.innerHTML = `
            <td>${data.email}</td>
            <td><span class="badge ${badgeClass}">${roleDisplay}</span></td>
            <td class="text-end">
                <button class="btn btn-sm btn-danger btn-delete-user" data-id="${data.id}">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </td>
        `;
        
        userList.appendChild(row);
        
        // Clear inputs
        emailInput.value = '';
        passwordInput.value = '';
        roleSelect.value = 'event_manager';
        
        showAlert('User added successfully', 'success');
        
        // Reinitialize delete buttons
        initializeDeleteButtons();
    })
    .catch(error => {
        showAlert(error.message, 'danger');
    });
}


function initializeDeleteButtons() {
    // Category delete buttons
    const categoryDeleteButtons = document.querySelectorAll('.btn-delete-category');
    categoryDeleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this category?')) {
                fetch(`/api/categories/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                        showAlert('Category deleted successfully', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting category', 'danger');
                });
            }
        });
    });

    // Event type delete buttons
    const typeDeleteButtons = document.querySelectorAll('.btn-delete-type');
    typeDeleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            if (confirm('Are you sure you want to delete this event type?')) {
                fetch(`/api/event-types/${id}`, {
                    method: 'DELETE'
                })
                .then(response => response.json())
                .then(data => {
                    const row = document.querySelector(`tr[data-id="${id}"]`);
                    if (row) {
                        row.remove();
                        showAlert('Event type deleted successfully', 'success');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error deleting event type', 'danger');
                });
            }
        });
    });

        // User delete buttons
    const userDeleteButtons = document.querySelectorAll('.btn-delete-user');
    userDeleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            deleteUser(id);
        });
    });
}

// Delete a user
function deleteUser(id) {
    confirmAction('Are you sure you want to delete this user?', function() {
        fetch(`/api/users/${id}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.error || 'Failed to delete user'); });
            }
            return response.json();
        })
        .then(data => {
            // Remove row from table
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                row.remove();
            }
            
            showAlert('User deleted successfully', 'success');
        })
        .catch(error => {
            showAlert(error.message, 'danger');
        });
    });
}

// Show alert message
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.role = 'alert';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;

    const container = document.querySelector('.container');
    container.insertBefore(alertDiv, container.firstChild);

    // Auto dismiss after 3 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 3000);
}

// Confirm action before delete
function confirmAction(message, callback) {
    if (window.confirm(message)) {
        callback();
    }
}