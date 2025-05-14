
<!DOCTYPE html>
<html>
<head>
    <title>Create Event - PharmaEvents</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Event</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" action="/events/create" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="event_name" class="form-label">Event Name</label>
                        <input type="text" class="form-control" id="event_name" name="event_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="requester_name" class="form-label">Requester Name</label>
                        <input type="text" class="form-control" id="requester_name" name="requester_name" required>
                    </div>
                    
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_online" name="is_online">
                        <label class="form-check-label" for="is_online">Online Event</label>
                    </div>
                </div>
                
                <div class="col-md-6" id="venue_fields">
                    <div class="mb-3">
                        <label for="governorate" class="form-label">Governorate</label>
                        <select class="form-select" id="governorate" name="governorate">
                            <option value="">Select Governorate</option>
                            <option value="Cairo">Cairo</option>
                            <option value="Alexandria">Alexandria</option>
                            <option value="Giza">Giza</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label>Start Date/Time</label>
                    <div class="row">
                        <div class="col">
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col">
                            <input type="time" class="form-control" name="start_time" required>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <label>End Date/Time</label>
                    <div class="row">
                        <div class="col">
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="col">
                            <input type="time" class="form-control" name="end_time" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label>Registration Deadline</label>
                    <div class="row">
                        <div class="col">
                            <input type="date" class="form-control" name="deadline_date" required>
                        </div>
                        <div class="col">
                            <input type="time" class="form-control" name="deadline_time" required>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">Create Event</button>
        </form>
    </div>
    
    <script>
        document.getElementById('is_online').addEventListener('change', function() {
            const venueFields = document.getElementById('venue_fields');
            venueFields.style.display = this.checked ? 'none' : 'block';
        });
    </script>
</body>
</html>
