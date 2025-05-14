
<!DOCTYPE html>
<html>
<head>
    <title>User Management - PharmaEvents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>User Management</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?php echo $user['id']; ?></td>
                    <td><?php echo $user['email']; ?></td>
                    <td><?php echo $user['role']; ?></td>
                    <td>
                        <button class="btn btn-danger btn-sm" onclick="deleteUser(<?php echo $user['id']; ?>)">Delete</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function deleteUser(id) {
            if (confirm('Are you sure you want to delete this user?')) {
                $.post('/users/delete', {id: id}, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
