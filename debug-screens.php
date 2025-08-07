<?php
// Debug script - save as debug-screens.php in your root directory
header('Content-Type: text/html; charset=utf-8');

echo "<h1>🔍 Screen Database Debug</h1>";

// Test database connection
try {
    require_once 'config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if (!$conn) {
        echo "<p style='color: red;'>❌ Database connection failed!</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Check if screens table exists
    $stmt = $conn->prepare("SHOW TABLES LIKE 'screens'");
    $stmt->execute();
    $tableExists = $stmt->fetch();
    
    if (!$tableExists) {
        echo "<p style='color: red;'>❌ Screens table does not exist!</p>";
        echo "<p>Run the database initialization script first.</p>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Screens table exists</p>";
    
    // Get all screens from database
    $stmt = $conn->prepare("SELECT * FROM screens ORDER BY display_order ASC, created_at ASC");
    $stmt->execute();
    $allScreens = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<h2>📊 Total Screens in Database: " . count($allScreens) . "</h2>";
    
    if (count($allScreens) == 0) {
        echo "<p style='color: red;'>❌ No screens found in database!</p>";
        echo "<p>You need to run the database initialization to create default screens.</p>";
        
        // Try to initialize
        echo "<h3>🔧 Initializing database...</h3>";
        $database->initializeDatabase();
        echo "<p>Database initialized. <a href='debug-screens.php'>Refresh this page</a></p>";
        exit;
    }
    
    echo "<table border='1' cellpadding='10' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
    echo "<tr style='background-color: #f0f0f0;'>";
    echo "<th>ID</th><th>Screen ID</th><th>Title</th><th>Type</th><th>Active</th><th>Order</th><th>Duration</th>";
    echo "</tr>";
    
    foreach ($allScreens as $screen) {
        $bgColor = $screen['screen_type'] === 'builtin' ? '#e8f5e8' : '#f0f8ff';
        $activeColor = $screen['is_active'] ? 'green' : 'red';
        
        echo "<tr style='background-color: {$bgColor};'>";
        echo "<td>{$screen['id']}</td>";
        echo "<td><strong>{$screen['screen_id']}</strong></td>";
        echo "<td>{$screen['title']}</td>";
        echo "<td>{$screen['screen_type']}</td>";
        echo "<td style='color: {$activeColor};'>" . ($screen['is_active'] ? 'YES' : 'NO') . "</td>";
        echo "<td>{$screen['display_order']}</td>";
        echo "<td>{$screen['display_duration']}ms</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test the get-screens.php API
    echo "<h2>🔗 Testing get-screens.php API</h2>";
    
    $apiUrl = 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/backend/get-screens.php';
    echo "<p>API URL: <code>{$apiUrl}</code></p>";
    
    $apiResponse = @file_get_contents($apiUrl);
    
    if ($apiResponse === false) {
        echo "<p style='color: red;'>❌ Failed to call get-screens.php API</p>";
        echo "<p>Check that backend/get-screens.php exists and is accessible</p>";
    } else {
        $apiData = json_decode($apiResponse, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p style='color: red;'>❌ API returned invalid JSON</p>";
            echo "<pre>" . htmlspecialchars($apiResponse) . "</pre>";
        } else {
            echo "<p style='color: green;'>✅ API Response successful</p>";
            echo "<pre>" . json_encode($apiData, JSON_PRETTY_PRINT) . "</pre>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>