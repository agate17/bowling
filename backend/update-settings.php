<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
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
        case 'GET':
            // Get all settings
            $stmt = $conn->prepare("SELECT setting_key, setting_value, description FROM settings ORDER BY setting_key");
            $stmt->execute();
            $settings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Convert to key-value format
            $settingsFormatted = [];
            foreach ($settings as $setting) {
                $settingsFormatted[$setting['setting_key']] = [
                    'value' => $setting['setting_value'],
                    'description' => $setting['description']
                ];
            }
            
            $response = [
                'success' => true,
                'data' => $settingsFormatted,
                'message' => 'Settings retrieved successfully'
            ];
            break;
            
        case 'POST':
            // Update settings
            if (empty($input) || !is_array($input)) {
                throw new Exception('Invalid settings data');
            }
            
            $conn->beginTransaction();
            
            try {
                foreach ($input as $key => $value) {
                    $stmt = $conn->prepare("
                        INSERT INTO settings (setting_key, setting_value) 
                        VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                    ");
                    $stmt->execute([$key, $value]);
                }
                
                $conn->commit();
                
                $response = [
                    'success' => true,
                    'message' => 'Settings updated successfully'
                ];
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
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