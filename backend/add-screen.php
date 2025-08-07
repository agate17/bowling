<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/db.php';

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
        'error' => $e->getMessage()
    ];
    echo json_encode($response);
}
?>