// Settings JavaScript for PharmaEvents

document.addEventListener('DOMContentLoaded', function() {
    // Category management
    const addCategoryForm = document.getElementById('add_category_form');
    if (addCategoryForm) {
        addCategoryForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addCategory();
        });
    }
    
    // Event type management
    const addTypeForm = document.getElementById('add_type_form');
    if (addTypeForm) {
        addTypeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            addEventType();
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

// Add a new category
function addCategory() {
    const nameInput = document.getElementById('category_name');
    const name = nameInput.value.trim();
    
    if (!name) {
        showAlert('Please enter a category name', 'danger');
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('category_name', name);
    
    // Send request
    fetch('/api/categories', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.error || 'Failed to add category'); });
        }
        return response.json();
    })
    .then(data => {
        // Add new category to the list
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
        
        // Clear input
        nameInput.value = '';
        
        showAlert('Category added successfully', 'success');
        
        // Reinitialize delete buttons
        initializeDeleteButtons();
    })
    .catch(error => {
        showAlert(error.message, 'danger');
    });
}

// Add a new event type
function addEventType() {
    const nameInput = document.getElementById('type_name');
    const name = nameInput.value.trim();
    
    if (!name) {
        showAlert('Please enter an event type name', 'danger');
        return;
    }
    
    // Create form data
    const formData = new FormData();
    formData.append('type_name', name);
    
    // Send request
    fetch('/api/event-types', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => { throw new Error(err.error || 'Failed to add event type'); });
        }
        return response.json();
    })
    .then(data => {
        // Add new event type to the list
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
        
        // Clear input
        nameInput.value = '';
        
        showAlert('Event type added successfully', 'success');
        
        // Reinitialize delete buttons
        initializeDeleteButtons();
    })
    .catch(error => {
        showAlert(error.message, 'danger');
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
        
        row.innerHTML = `
            <td>${data.email}</td>
            <td><span class="badge bg-primary">${data.role.toUpperCase()}</span></td>
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

// Delete a category
function deleteCategory(id) {
    confirmAction('Are you sure you want to delete this category?', function() {
        fetch(`/api/categories/${id}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.error || 'Failed to delete category'); });
            }
            return response.json();
        })
        .then(data => {
            // Remove row from table
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                row.remove();
            }
            
            showAlert('Category deleted successfully', 'success');
        })
        .catch(error => {
            showAlert(error.message, 'danger');
        });
    });
}

// Delete an event type
function deleteEventType(id) {
    confirmAction('Are you sure you want to delete this event type?', function() {
        fetch(`/api/event-types/${id}`, {
            method: 'DELETE'
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.error || 'Failed to delete event type'); });
            }
            return response.json();
        })
        .then(data => {
            // Remove row from table
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) {
                row.remove();
            }
            
            showAlert('Event type deleted successfully', 'success');
        })
        .catch(error => {
            showAlert(error.message, 'danger');
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

// Initialize all delete buttons
function initializeDeleteButtons() {
    // Category delete buttons
    const categoryDeleteButtons = document.querySelectorAll('.btn-delete-category');
    categoryDeleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            deleteCategory(id);
        });
    });
    
    // Event type delete buttons
    const typeDeleteButtons = document.querySelectorAll('.btn-delete-type');
    typeDeleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            deleteEventType(id);
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

// Show alert message
function showAlert(message, type) {
    const alertsContainer = document.getElementById('alerts_container');
    if (!alertsContainer) return;
    
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    alertsContainer.appendChild(alert);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alert.classList.remove('show');
        setTimeout(() => {
            alert.remove();
        }, 150);
    }, 5000);
}
