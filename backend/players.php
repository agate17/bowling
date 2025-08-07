<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

// With this improved version:
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
    echo json_encode(['success' => false, 'error' => 'Database config not found']);
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
        case 'GET':
            // Get leaderboard data
            $type = $_GET['type'] ?? 'overall';
            
            if ($type === 'daily') {
                // Daily leaderboard - players with games today
                $stmt = $conn->prepare("
                    SELECT 
                        name,
                        nickname,
                        daily_score as total_score,
                        CASE 
                            WHEN daily_games > 0 THEN ROUND(daily_score / daily_games, 2)
                            ELSE 0 
                        END as average_score,
                        daily_strikes as strikes,
                        daily_spares as spares,
                        daily_games as games_played,
                        best_score
                    FROM players 
                    WHERE is_active = 1 
                    AND last_game_date = CURDATE()
                    AND daily_games > 0
                    ORDER BY average_score DESC, daily_score DESC
                    LIMIT 8
                ");
            } else {
                // Overall leaderboard
                $stmt = $conn->prepare("
                    SELECT 
                        name,
                        nickname,
                        total_score,
                        average_score,
                        strikes,
                        spares,
                        games_played,
                        best_score
                    FROM players 
                    WHERE is_active = 1 
                    ORDER BY average_score DESC, total_score DESC
                    LIMIT 8
                ");
            }
            
            $stmt->execute();
            $players = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add ranking
            foreach ($players as $index => &$player) {
                $player['rank'] = $index + 1;
            }
            
            $response = [
                'success' => true,
                'data' => $players,
                'type' => $type,
                'message' => 'Leaderboard retrieved successfully'
            ];
            break;
            
        case 'POST':
            // Add/Update player data from Brunswick
            if (!isset($input['brunswick_id']) || !isset($input['name'])) {
                throw new Exception('Brunswick ID and name are required');
            }
            
            // Check if player exists
            $checkStmt = $conn->prepare("SELECT id FROM players WHERE brunswick_id = ?");
            $checkStmt->execute([$input['brunswick_id']]);
            $existingPlayer = $checkStmt->fetch();
            
            if ($existingPlayer) {
                // Update existing player
                $stmt = $conn->prepare("
                    UPDATE players 
                    SET name = ?, nickname = ?, total_score = ?, average_score = ?, 
                        strikes = ?, spares = ?, games_played = ?, best_score = ?
                    WHERE brunswick_id = ?
                ");
                
                $stmt->execute([
                    $input['name'],
                    $input['nickname'] ?? null,
                    $input['total_score'] ?? 0,
                    $input['average_score'] ?? 0,
                    $input['strikes'] ?? 0,
                    $input['spares'] ?? 0,
                    $input['games_played'] ?? 0,
                    $input['best_score'] ?? 0,
                    $input['brunswick_id']
                ]);
                
                $message = 'Player updated successfully';
            } else {
                // Insert new player
                $stmt = $conn->prepare("
                    INSERT INTO players (brunswick_id, name, nickname, total_score, average_score, strikes, spares, games_played, best_score) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");
                
                $stmt->execute([
                    $input['brunswick_id'],
                    $input['name'],
                    $input['nickname'] ?? null,
                    $input['total_score'] ?? 0,
                    $input['average_score'] ?? 0,
                    $input['strikes'] ?? 0,
                    $input['spares'] ?? 0,
                    $input['games_played'] ?? 0,
                    $input['best_score'] ?? 0
                ]);
                
                $message = 'Player added successfully';
            }
            
            $response = [
                'success' => true,
                'message' => $message,
                'brunswick_id' => $input['brunswick_id']
            ];
            break;
            
        case 'PUT':
            // Update daily stats for a player
            if (!isset($input['brunswick_id'])) {
                throw new Exception('Brunswick ID is required');
            }
            
            $updateFields = [];
            $updateValues = [];
            
            // Update daily stats
            if (isset($input['daily_score'])) {
                $updateFields[] = 'daily_score = daily_score + ?';
                $updateValues[] = $input['daily_score'];
            }
            if (isset($input['daily_games'])) {
                $updateFields[] = 'daily_games = daily_games + ?';
                $updateValues[] = $input['daily_games'];
            }
            if (isset($input['daily_strikes'])) {
                $updateFields[] = 'daily_strikes = daily_strikes + ?';
                $updateValues[] = $input['daily_strikes'];
            }
            if (isset($input['daily_spares'])) {
                $updateFields[] = 'daily_spares = daily_spares + ?';
                $updateValues[] = $input['daily_spares'];
            }
            
            // Always update the last game date
            $updateFields[] = 'last_game_date = CURDATE()';
            
            if (empty($updateFields)) {
                throw new Exception('No fields to update');
            }
            
            $updateValues[] = $input['brunswick_id'];
            
            $stmt = $conn->prepare("
                UPDATE players 
                SET " . implode(', ', $updateFields) . " 
                WHERE brunswick_id = ?
            ");
            
            $stmt->execute($updateValues);
            
            if ($stmt->rowCount() === 0) {
                throw new Exception('Player not found');
            }
            
            $response = [
                'success' => true,
                'message' => 'Daily stats updated successfully'
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