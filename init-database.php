<?php
// Database Initialization Script
// Run this once to set up your database

require_once 'config/db.php';

echo "🚀 Starting Database Initialization...\n\n";

try {
    $database = new Database();
    
    echo "📊 Creating database and tables...\n";
    if ($database->initializeDatabase()) {
        echo "✅ Database initialized successfully!\n\n";
        
        // Verify tables were created
        $conn = $database->getConnection();
        $tables = ['players', 'screens', 'teams', 'team_members', 'settings'];
        
        echo "🔍 Verifying tables:\n";
        foreach ($tables as $table) {
            $stmt = $conn->prepare("SELECT COUNT(*) as count FROM $table");
            $stmt->execute();
            $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
            echo "   ✅ $table: $count records\n";
        }
        
        echo "\n🎉 Database setup complete!\n\n";
        echo "📋 What's been set up:\n";
        echo "   • Players table with sample bowling data\n";
        echo "   • Screens table with 2 builtin leaderboards (overall & daily)\n";
        echo "   • Teams and team_members tables for future use\n";
        echo "   • Settings table with default configuration\n\n";
        
        echo "🔗 Next steps:\n";
        echo "   1. Update database credentials in config/db.php if needed\n";
        echo "   2. Configure Brunswick API settings in admin panel\n";
        echo "   3. Test the leaderboard displays\n";
        echo "   4. Add custom screens via admin panel\n\n";
        
        echo "🌐 Access URLs:\n";
        echo "   • Main Display: index.php\n";
        echo "   • Admin Panel: admin/admin.php\n";
        echo "   • Diagnostic Tool: diagnostic.html\n\n";
        
    } else {
        throw new Exception("Failed to initialize database");
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\n🔧 Troubleshooting:\n";
    echo "   1. Check MySQL server is running\n";
    echo "   2. Verify database credentials in config/db.php\n";
    echo "   3. Ensure MySQL user has CREATE DATABASE permissions\n";
    echo "   4. Check PHP PDO MySQL extension is enabled\n\n";
}

echo "Done.\n";
?>