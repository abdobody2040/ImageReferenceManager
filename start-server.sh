
#!/bin/bash

# Cleanup: Kill any existing PHP servers
echo "Cleaning up any existing PHP processes..."
pkill -f "php -S" || echo "No PHP servers running"

# Wait a moment for processes to terminate
sleep 1

# Prepare upload directory
echo "Setting up upload directories..."
mkdir -p public/static/uploads/events
chmod -R 755 public/static/uploads

# Start the PHP server
echo "Starting PHP server on port 5000..."
php -S 0.0.0.0:5000 index.php
