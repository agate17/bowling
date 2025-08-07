<?php
// Fixed get-screens.php - Updated path resolution
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
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

try {
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        throw new Exception('Database connection failed');
    }
    
    // Get all active screens ordered by display_order
    $stmt = $conn->prepare("
        SELECT screen_id, title, subtitle, content, screen_type, display_order, display_duration, is_active, created_at, updated_at
        FROM screens 
        WHERE is_active = 1 
        ORDER BY display_order ASC, created_at ASC
    ");
    
    $stmt->execute();
    $screens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Separate builtin and custom screens
    $builtinScreens = [];
    $customScreens = [];
    
    foreach ($screens as $screen) {
        if ($screen['screen_type'] === 'builtin') {
            $builtinScreens[] = $screen;
        } else {
            $customScreens[] = $screen;
        }
    }
    
    $response = [
        'success' => true,
        'data' => [
            'builtin' => $builtinScreens,
            'custom' => $customScreens,
            'total' => count($screens)
        ],
        'message' => 'Screens retrieved successfully',
        'debug_info' => [
            'config_path_used' => $config_path,
            'builtin_count' => count($builtinScreens),
            'custom_count' => count($customScreens)
        ]
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Failed to retrieve screens',
        'debug_info' => [
            'config_path' => $config_path,
            'file_exists' => file_exists($config_path)
        ]
    ];
    echo json_encode($response);
}
?>