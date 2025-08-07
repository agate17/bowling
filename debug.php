<?php
// Debug Script - Run this to check your setup
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔧 Bowling Display Debug Tool</h1>\n";
echo "<pre>\n";

// Check PHP version
echo "📊 PHP Version: " . PHP_VERSION . "\n";

// Check required extensions
$required_extensions = ['pdo', 'pdo_mysql', 'json'];
echo "\n📋 PHP Extensions:\n";
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? '✅' : '❌';
    echo "   {$status} {$ext}\n";
}

// Check file structure
echo "\n📁 File Structure:\n";
$files_to_check = [
    'config/db.php',
    'backend/add-screen.php',
    'backend/get-screens.php',
    'backend/players.php',
    'backend/update-settings.php',
    'init-database.php'
];

foreach ($files_to_check as $file) {
    $status = file_exists($file) ? '✅' : '❌';
    echo "   {$status} {$file}\n";
}

// Test database connection
echo "\n🔗 Database Connection Test:\n";
try {
    require_once 'config/db.php';
    $database = new Database();
    $conn = $database->getConnection();
    
    if ($conn) {
        echo "   ✅ Database connection successful\n";
        
        // Check if tables exist
        $tables = ['players', 'screens', 'settings'];
        echo "\n📊 Database Tables:\n";
        
        foreach ($tables as $table) {
            try {
                $stmt = $conn->prepare("SELECT COUNT(*) as count FROM {$table}");
                $stmt->execute();
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                echo "   ✅ {$table}: {$count} records\n";
            } catch (Exception $e) {
                echo "   ❌ {$table}: " . $e->getMessage() . "\n";
            }
        }
        
    } else {
        echo "   ❌ Database connection failed\n";
    }
    
} catch (Exception $e) {
    echo "   ❌ Database error: " . $e->getMessage() . "\n";
    echo "   💡 Check your database credentials in config/db.php\n";
}

// Test API endpoints
echo "\n🔌 API Endpoint Test:\n";
if (file_exists('backend/add-screen.php')) {
    echo "   ✅ add-screen.php exists\n";
    
    // Test with a simple GET request
    ob_start();
    $_SERVER['REQUEST_METHOD'] = 'GET';
    try {
        include 'backend/add-screen.php';
        $output = ob_get_contents();
        ob_end_clean();
        
        if (strpos($output, 'Method not allowed') !== false) {
            echo "   ✅ add-screen.php responding (Method not allowed is expected for GET)\n";
        } else {
            echo "   ⚠️ add-screen.php output: " . substr($output, 0, 100) . "...\n";
        }
    } catch (Exception $e) {
        ob_end_clean();
        echo "   ❌ add-screen.php error: " . $e->getMessage() . "\n";
    }
} else {
    echo "   ❌ add-screen.php not found\n";
}

// Check localStorage data format
echo "\n💾 LocalStorage Test:\n";
echo "   📝 Open browser console and run:\n";
echo "   console.log(JSON.parse(localStorage.getItem('customScreens') || '[]'));\n";

// Show current working directory
echo "\n📂 Current Directory: " . getcwd() . "\n";

echo "\n🔧 If you see errors above:\n";
echo "   1. Make sure MySQL server is running\n";
echo "   2. Check database credentials in config/db.php\n";
echo "   3. Run init-database.php to create tables\n";
echo "   4. Ensure web server has write permissions\n";
echo "   5. Check PHP error logs\n";

echo "</pre>\n";
?>