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