<?php
/**
 * Database connection configuration
 */

// Get database connection details from environment variables
// Support both PostgreSQL and MySQL environments
if (getenv('DATABASE_URL')) {
    // Parse connection details from URL format
    $db_url = parse_url(getenv('DATABASE_URL'));
    $db_host = $db_url['host'] ?? 'localhost';
    $db_port = $db_url['port'] ?? '5432';
    $db_name = ltrim($db_url['path'] ?? '', '/');
    $db_user = $db_url['user'] ?? '';
    $db_pass = $db_url['pass'] ?? '';

    // Determine if PostgreSQL or MySQL
    $db_type = (strpos(getenv('DATABASE_URL'), 'postgres') !== false) ? 'pgsql' : 'mysql';
} else {
    // Fallback to individual environment variables
    $db_host = getenv('PGHOST') ?: getenv('DB_HOST') ?: 'localhost';
    $db_port = getenv('PGPORT') ?: getenv('DB_PORT') ?: '5432';
    $db_name = getenv('PGDATABASE') ?: getenv('DB_NAME') ?: 'pharmaevents';
    $db_user = getenv('PGUSER') ?: getenv('DB_USER') ?: 'postgres';
    $db_pass = getenv('PGPASSWORD') ?: getenv('DB_PASSWORD') ?: '';
    $db_type = getenv('DB_TYPE') ?: 'pgsql';
}

// Construct DSN based on database type
$dsn = "$db_type:host=$db_host;port=$db_port;dbname=$db_name";

// Add SSL option based on database type
$ssl_options = '';
if ($db_type === 'pgsql') {
    // Make SSL mode optional
    $ssl_options = getenv('DB_SSL_REQUIRED') ? 'sslmode=require' : 'sslmode=prefer';
}

// Set connection attempts
$max_attempts = 3;
$attempt = 1;
$connected = false;

while ($attempt <= $max_attempts && !$connected) {
    try {
        // Create PDO instance with dynamic DSN
        $pdo = new PDO(
            "$db_type:host=$db_host;port=$db_port;dbname=$db_name" . ($ssl_options ? ";$ssl_options" : ""), 
            $db_user, 
            $db_pass, 
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_TIMEOUT => 5 // 5 seconds timeout
            ]
        );

        // Set application-specific options
        if ($db_type === 'pgsql') {
            $pdo->exec("SET TIME ZONE 'UTC'");
        }
        
        $connected = true;
        
    } catch (PDOException $e) {
        // Log error
        error_log("Database connection attempt $attempt failed: " . $e->getMessage());
        
        if ($attempt >= $max_attempts) {
            // Display friendly error message after all attempts fail
            die('Database connection failed after multiple attempts. Please try again later or contact an administrator.');
        }
        
        // Wait before trying again (exponential backoff)
        $wait_time = pow(2, $attempt - 1); // 1, 2, 4 seconds
        sleep($wait_time);
        $attempt++;
    }
}
?>