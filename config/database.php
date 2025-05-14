<?php
/**
 * Database connection configuration
 */

// Get database connection details from environment variables
$db_host = getenv('PGHOST');
$db_port = getenv('PGPORT');
$db_name = getenv('PGDATABASE');
$db_user = getenv('PGUSER');
$db_pass = getenv('PGPASSWORD');

// Construct DSN
$dsn = "pgsql:host=$db_host;port=$db_port;dbname=$db_name";

try {
    // Create PDO instance
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    // Set application-specific options
    $pdo->exec("SET TIME ZONE 'UTC'");
    
} catch (PDOException $e) {
    // Log error and display generic message
    error_log('Database connection error: ' . $e->getMessage());
    die('Database connection failed. Please check the logs or contact an administrator.');
}
?>