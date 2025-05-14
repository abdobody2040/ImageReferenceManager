
<!DOCTYPE html>
<html>
<head>
    <title>Events - PharmaEvents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Events</h2>
            <a href="/events/create" class="btn btn-primary">Create Event</a>
        </div>
        
        <div class="row">
            <?php foreach ($events as $event): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($event['name']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars($event['description']); ?></p>
                        <p class="text-muted">
                            Start: <?php echo date('Y-m-d H:i', strtotime($event['start_datetime'])); ?><br>
                            End: <?php echo date('Y-m-d H:i', strtotime($event['end_datetime'])); ?>
                        </p>
                        <div class="d-flex justify-content-between">
                            <a href="/events/<?php echo $event['id']; ?>" class="btn btn-sm btn-primary">View Details</a>
                            <?php if ($_SESSION['user_id'] == $event['user_id']): ?>
                            <button class="btn btn-sm btn-danger" onclick="deleteEvent(<?php echo $event['id']; ?>)">Delete</button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function deleteEvent(id) {
            if (confirm('Are you sure you want to delete this event?')) {
                $.post('/events/delete', {id: id}, function(response) {
                    if (response.success) {
                        location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
