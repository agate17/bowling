<?php
// Simple test endpoint to debug the issue
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Log the request
    $log_data = [
        'timestamp' => date('Y-m-d H:i:s'),
        'method' => $method,
        'input' => file_get_contents('php://input'),
        'headers' => getallheaders()
    ];
    
    // Try to include database config
    if (file_exists('../config/db.php')) {
        require_once '../config/db.php';
        $database = new Database();
        $conn = $database->getConnection();
        
        if ($conn) {
            $db_status = 'Connected successfully';
        } else {
            $db_status = 'Connection failed';
        }
    } else {
        $db_status = 'Database config file not found';
    }
    
    $response = [
        'success' => true,
        'message' => 'Test endpoint working',
        'method' => $method,
        'database_status' => $db_status,
        'php_version' => PHP_VERSION,
        'current_dir' => getcwd(),
        'request_log' => $log_data
    ];
    
    if ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        $response['received_data'] = $input;
        
        if ($input) {
            $response['message'] = 'POST data received successfully';
        }
    }
    
    echo json_encode($response, JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
        'trace' => $e->getTraceAsString()
    ];
    echo json_encode($response, JSON_PRETTY_PRINT);
}
?>