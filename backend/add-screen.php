<?php
// Fixed add-screen.php - Updated path resolution
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Better path resolution
$config_path = dirname(__DIR__) . '/config/db.php';

if (!file_exists($config_path)) {
    // Try alternative paths
    $alt_paths = [
        '../config/db.php',
        dirname(__FILE__) . '/../config/db.php',
        $_SERVER['DOCUMENT_ROOT'] . '/bowling/config/db.php',
        __DIR__ . '/../config/db.php'
    ];
    
    foreach ($alt_paths as $path) {
        if (file_exists($path)) {
            $config_path = $path;
            break;
        }
    }
}

if (!file_exists($config_path)) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database configuration file not found. Checked: ' . $config_path,
        'debug_info' => [
            'current_dir' => getcwd(),
            '__DIR__' => __DIR__,
            'dirname(__DIR__)' => dirname(__DIR__),
            'checked_path' => $config_path
        ]
    ]);
    exit;
}

require_once $config_path;

$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    switch ($method) {
        case 'POST':
            // Add new screen
            if (!isset($input['screen_id']) || !isset($input['title'])) {
                throw new Exception('Screen ID and Title are required');
            }
            
            // Check if screen_id already exists
            $checkStmt = $conn->prepare("SELECT id FROM screens WHERE screen_id = ?");
            $checkStmt->execute([$input['screen_id']]);
            
            if ($checkStmt->fetch()) {
                throw new Exception('Screen ID already exists');
            }
            
            // Get the next display order
            $orderStmt = $conn->prepare("SELECT MAX(display_order) as max_order FROM screens");
            $orderStmt->execute();
            $maxOrder = $orderStmt->fetch(PDO::FETCH_ASSOC)['max_order'] ?? 0;
            
            $stmt = $conn->prepare("
                INSERT INTO screens (screen_id, title, subtitle, content, screen_type, display_order, display_duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $input['screen_id'],
                $input['title'],
                $input['subtitle'] ?? '',
                $input['content'] ?? '',
                $input['screen_type'] ?? 'custom',
                $maxOrder + 1,
                $input['display_duration'] ?? 10000
            ]);
            
            $response = [
                'success' => true,
                'message' => 'Screen added successfully',
                'screen_id' => $input['screen_id']
            ];
            break;
            
        case 'PUT':
            // Update existing screen
            if (!isset($input['screen_id'])) {
                throw new Exception('Screen ID is required for update');
            }
            
            $updateFields = [];
            $updateValues = [];
            
            if (isset($input['title'])) {
                $updateFields[] = 'title = ?';
                $updateValues[] = $input['title'];
            }
            if (isset($input['subtitle'])) {
                $updateFields[] = 'subtitle = ?';
                $updateValues[] = $input['subtitle'];
            }
            if (isset($input['content'])) {
                $updateFields[] = 'content = ?';
                $updateValues[] = $input['content'];
            }
            if (isset($input['screen_type'])) {
                $updateFields[] = 'screen_type = ?';
                $updateValues[] = $input['screen_type'];
            }
            if (isset($input['display_duration'])) {
                $updateFields[] = 'display_duration = ?';
                $updateValues[] = $input['display_duration'];
            }
            if (isset($input['is_active'])) {
                $updateFields[] = 'is_active = ?';
                $updateValues[] = $input['is_active'];
            }
            
            if (empty($updateFields)) {
                throw new Exception('No fields to update');
            }
            
            $updateValues[] = $input['screen_id'];
            
            $stmt = $conn->prepare("
                UPDATE screens 
                SET " . implode(', ', $updateFields) . " 
                WHERE screen_id = ?
            ");
            
            $stmt->execute($updateValues);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Screen not found or no changes made');
            }
            
            $response = [
                'success' => true,
                'message' => 'Screen updated successfully'
            ];
            break;
            
        case 'DELETE':
            // Delete screen
            if (!isset($input['screen_id'])) {
                throw new Exception('Screen ID is required for deletion');
            }
            
            // Don't allow deletion of builtin screens
            $checkStmt = $conn->prepare("SELECT screen_type FROM screens WHERE screen_id = ?");
            $checkStmt->execute([$input['screen_id']]);
            $screen = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$screen) {
                throw new Exception('Screen not found');
            }
            
            if ($screen['screen_type'] === 'builtin') {
                throw new Exception('Cannot delete builtin screens');
            }
            
            $stmt = $conn->prepare("DELETE FROM screens WHERE screen_id = ?");
            $stmt->execute([$input['screen_id']]);
            
            $response = [
                'success' => true,
                'message' => 'Screen deleted successfully'
            ];
            break;
            
        default:
            throw new Exception('Method not allowed');
    }
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'debug_info' => [
            'method' => $method,
            'config_path' => $config_path,
            'input_received' => $input
        ]
    ];
    echo json_encode($response);
}
?>