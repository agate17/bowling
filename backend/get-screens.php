<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

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
        'message' => 'Screens retrieved successfully'
    ];
    
    echo json_encode($response);
    
} catch (Exception $e) {
    http_response_code(500);
    $response = [
        'success' => false,
        'error' => $e->getMessage(),
        'message' => 'Failed to retrieve screens'
    ];
    echo json_encode($response);
}
?>