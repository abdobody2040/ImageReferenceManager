#!/bin/bash

# PHP Server script for PharmaEvents application
echo "Starting PHP server for PharmaEvents..."

# Set environment variables if needed (will use existing PostgreSQL settings)
export PHP_ENV=${PHP_ENV:-"development"}

# Run PHP built-in web server
php -S 0.0.0.0:5000 -t . index.php