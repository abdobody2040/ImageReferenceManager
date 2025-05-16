<?php
/**
 * Error handling functions for the application
 */

// Set error reporting level
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors directly to the user

// Set a custom error handler
set_error_handler('customErrorHandler');

// Set a custom exception handler
set_exception_handler('customExceptionHandler');

// Register a shutdown function to catch fatal errors
register_shutdown_function('fatalErrorHandler');

/**
 * Custom error handler
 * 
 * @param int $errno Error number
 * @param string $errstr Error message
 * @param string $errfile File where the error occurred
 * @param int $errline Line number where the error occurred
 * @return bool
 */
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Don't handle errors that are suppressed with @
    if (error_reporting() === 0) {
        return false;
    }
    
    $error_type = getErrorType($errno);
    
    // Log the error
    error_log("[$error_type] $errstr in $errfile on line $errline");
    
    // For minor errors, just log them and continue
    if (in_array($errno, [E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED, E_STRICT])) {
        return true;
    }
    
    // For serious errors, display an error page
    displayErrorPage("Application Error", "$error_type: $errstr", [
        'file' => $errfile,
        'line' => $errline
    ]);
    
    return true;
}

/**
 * Custom exception handler
 * 
 * @param Throwable $exception The exception object
 * @return void
 */
function customExceptionHandler($exception) {
    // Log the exception
    error_log("Uncaught Exception: " . $exception->getMessage() . " in " . $exception->getFile() . " on line " . $exception->getLine());
    
    // Display an error page
    displayErrorPage("Application Exception", $exception->getMessage(), [
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
}

/**
 * Fatal error handler for catching fatal errors
 * 
 * @return void
 */
function fatalErrorHandler() {
    $error = error_get_last();
    
    // Check if a fatal error occurred
    if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        $error_type = getErrorType($error['type']);
        
        // Log the error
        error_log("[$error_type] {$error['message']} in {$error['file']} on line {$error['line']}");
        
        // Display an error page
        displayErrorPage("Fatal Error", "{$error['message']}", [
            'file' => $error['file'],
            'line' => $error['line']
        ]);
    }
}

/**
 * Get the error type as a string
 * 
 * @param int $errno Error number
 * @return string
 */
function getErrorType($errno) {
    switch ($errno) {
        case E_ERROR:
            return 'Fatal Error';
        case E_WARNING:
            return 'Warning';
        case E_PARSE:
            return 'Parse Error';
        case E_NOTICE:
            return 'Notice';
        case E_CORE_ERROR:
            return 'Core Error';
        case E_CORE_WARNING:
            return 'Core Warning';
        case E_COMPILE_ERROR:
            return 'Compile Error';
        case E_COMPILE_WARNING:
            return 'Compile Warning';
        case E_USER_ERROR:
            return 'User Error';
        case E_USER_WARNING:
            return 'User Warning';
        case E_USER_NOTICE:
            return 'User Notice';
        case E_STRICT:
            return 'Strict';
        case E_RECOVERABLE_ERROR:
            return 'Recoverable Error';
        case E_DEPRECATED:
            return 'Deprecated';
        case E_USER_DEPRECATED:
            return 'User Deprecated';
        default:
            return 'Unknown Error';
    }
}

/**
 * Display an error page
 * 
 * @param string $title Error title
 * @param string $message Error message
 * @param array $details Additional details
 * @return void
 */
function displayErrorPage($title, $message, $details = []) {
    // Clear any output that has already been generated
    ob_clean();
    
    // Set the HTTP response code
    http_response_code(500);
    
    // In development, show detailed error information
    $show_details = getenv('APP_ENV') !== 'production';
    
    // Start the output buffer
    ob_start();
    
    // Include the error template
    include 'views/errors/500.php';
    
    // Flush the output buffer and end script execution
    ob_end_flush();
    exit;
}

/**
 * Log a database error and display an appropriate message
 * 
 * @param PDOException $e The PDO exception
 * @param string $context Context where the error occurred
 * @param bool $redirect Whether to redirect to another page
 * @param string $redirect_url URL to redirect to
 * @return void
 */
function handleDatabaseError($e, $context = '', $redirect = true, $redirect_url = '/') {
    // Log the error with context
    error_log("Database error in $context: " . $e->getMessage());
    
    // Add a flash message
    flash('A database error occurred. Please try again later.', 'danger');
    
    // Redirect if requested
    if ($redirect) {
        header("Location: $redirect_url");
        exit;
    }
}
?>