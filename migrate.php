<?php
/**
 * Database Migration Script for PharmaEvents
 * This script handles database initialization and migrations
 */

// Include database configuration
require_once 'config/database.php';

echo "Starting database migration...\n";

// Read SQL from the database.sql file
$sql = file_get_contents('database.sql');

// Split SQL by semicolons
$queries = preg_split('/;\s*$/m', $sql);

// Execute each query
$success = true;
$pdo->beginTransaction();

try {
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;
        
        echo "Executing: " . substr($query, 0, 50) . "...\n";
        $result = $pdo->exec($query);
        if ($result === false) {
            echo "Error executing query: " . $query . "\n";
            $success = false;
            break;
        }
    }
    
    if ($success) {
        $pdo->commit();
        echo "Database migration completed successfully!\n";
    } else {
        $pdo->rollBack();
        echo "Database migration failed. Rolling back changes.\n";
    }
} catch (PDOException $e) {
    $pdo->rollBack();
    echo "Database migration error: " . $e->getMessage() . "\n";
}

// Check if tables were created
echo "\nVerifying database tables:\n";
$tables = ['users', 'event', 'event_category', 'event_type', 'venue', 'service_request', 'employee_code', 'app_setting'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
        $count = $stmt->fetchColumn();
        echo "- Table '$table': OK ($count records)\n";
    } catch (PDOException $e) {
        echo "- Table '$table': MISSING\n";
    }
}

echo "\nDone!\n";
?>