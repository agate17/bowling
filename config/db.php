<?php
// Database Configuration
class Database {
    private $host = 'localhost';
    private $db_name = 'bowling_display';
    private $username = 'root'; // Change this to your MySQL username
    private $password = '';     // Change this to your MySQL password
    private $conn;
    
    public function getConnection() {
        $this->conn = null;
        
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
        
        return $this->conn;
    }
    
    // Create database and tables if they don't exist
    public function initializeDatabase() {
        try {
            // Create database if it doesn't exist
            $tempConn = new PDO(
                "mysql:host=" . $this->host . ";charset=utf8",
                $this->username,
                $this->password
            );
            $tempConn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $tempConn->exec("CREATE DATABASE IF NOT EXISTS `{$this->db_name}` CHARACTER SET utf8 COLLATE utf8_general_ci");
            
            // Now connect to the database and create tables
            $this->conn = $this->getConnection();
            $this->createTables();
            
            return true;
        } catch(PDOException $e) {
            echo "Database initialization error: " . $e->getMessage();
            return false;
        }
    }
    
    private function createTables() {
        // Create players table
        $playersTable = "
        CREATE TABLE IF NOT EXISTS players (
            id INT AUTO_INCREMENT PRIMARY KEY,
            brunswick_id VARCHAR(50) UNIQUE,
            name VARCHAR(100) NOT NULL,
            nickname VARCHAR(50),
            total_score INT DEFAULT 0,
            average_score DECIMAL(5,2) DEFAULT 0.00,
            strikes INT DEFAULT 0,
            spares INT DEFAULT 0,
            games_played INT DEFAULT 0,
            best_score INT DEFAULT 0,
            daily_score INT DEFAULT 0,
            daily_games INT DEFAULT 0,
            daily_strikes INT DEFAULT 0,
            daily_spares INT DEFAULT 0,
            last_game_date DATE,
            last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE
        )";
        
        // Create screens table
        $screensTable = "
        CREATE TABLE IF NOT EXISTS screens (
            id INT AUTO_INCREMENT PRIMARY KEY,
            screen_id VARCHAR(50) UNIQUE NOT NULL,
            title VARCHAR(200) NOT NULL,
            subtitle VARCHAR(300),
            content TEXT,
            screen_type ENUM('announcement', 'sponsor', 'promotion', 'custom', 'builtin') DEFAULT 'custom',
            is_active BOOLEAN DEFAULT TRUE,
            display_order INT DEFAULT 0,
            display_duration INT DEFAULT 10000,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            created_by VARCHAR(100) DEFAULT 'admin'
        )";
        
        // Create teams table
        $teamsTable = "
        CREATE TABLE IF NOT EXISTS teams (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            average_score DECIMAL(5,2) DEFAULT 0.00,
            wins INT DEFAULT 0,
            losses INT DEFAULT 0,
            games_played INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_active BOOLEAN DEFAULT TRUE
        )";
        
        // Create team_members table (many-to-many relationship)
        $teamMembersTable = "
        CREATE TABLE IF NOT EXISTS team_members (
            id INT AUTO_INCREMENT PRIMARY KEY,
            team_id INT,
            player_id INT,
            joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
            FOREIGN KEY (player_id) REFERENCES players(id) ON DELETE CASCADE,
            UNIQUE KEY unique_team_player (team_id, player_id)
        )";
        
        // Create settings table
        $settingsTable = "
        CREATE TABLE IF NOT EXISTS settings (
            id INT AUTO_INCREMENT PRIMARY KEY,
            setting_key VARCHAR(100) UNIQUE NOT NULL,
            setting_value TEXT,
            description VARCHAR(300),
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        )";
        
        // Execute table creation
        $this->conn->exec($playersTable);
        $this->conn->exec($screensTable);
        $this->conn->exec($teamsTable);
        $this->conn->exec($teamMembersTable);
        $this->conn->exec($settingsTable);
        
        // Insert default settings
        $this->insertDefaultSettings();
        $this->insertSampleData();
    }
    
    private function insertDefaultSettings() {
        $defaultSettings = [
            ['screen_rotation_interval', '10000', 'Screen rotation interval in milliseconds'],
            ['auto_refresh_enabled', '1', 'Enable automatic data refresh'],
            ['refresh_interval', '300000', 'Data refresh interval in milliseconds (5 minutes)'],
            ['brunswick_api_url', '', 'Brunswick API endpoint URL'],
            ['brunswick_api_key', '', 'Brunswick API key for authentication'],
            ['display_title', '🎳 BOWLING STATS DISPLAY 🎳', 'Main display title'],
            ['max_leaderboard_entries', '8', 'Maximum entries to show in leaderboard']
        ];
        
        foreach ($defaultSettings as $setting) {
            $stmt = $this->conn->prepare("
                INSERT IGNORE INTO settings (setting_key, setting_value, description) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute($setting);
        }
    }
    
    private function insertSampleData() {
        // Insert sample players (matching your existing data)
        $samplePlayers = [
            ['brunswick_001', "Jānis 'Striker' Ozoliņš", 'Striker', 5460, 210, 85, 50, 26, 298],
            ['brunswick_002', "Anna 'Bulta' Kalniņa", 'Bulta', 5050, 202, 80, 48, 25, 285],
            ['brunswick_003', "Mārtiņš 'Precizitāte' Bērziņš", 'Precizitāte', 4752, 198, 75, 47, 24, 276],
            ['brunswick_004', "Elīna 'Spēks' Liepa", 'Spēks', 4416, 192, 70, 45, 23, 265],
            ['brunswick_005', "Rihards 'Ātrums' Siliņš", 'Ātrums', 4136, 188, 68, 44, 22, 258],
            ['brunswick_006', "Līga 'Zibens' Jansone", 'Zibens', 3885, 185, 65, 43, 21, 252],
            ['brunswick_007', "Edgars 'Bumba' Vītols", 'Bumba', 3600, 180, 62, 41, 20, 245],
            ['brunswick_008', "Ilze 'Vējš' Krūmiņa", 'Vējš', 3325, 175, 60, 40, 19, 238]
        ];
        
        foreach ($samplePlayers as $player) {
            $stmt = $this->conn->prepare("
                INSERT IGNORE INTO players 
                (brunswick_id, name, nickname, total_score, average_score, strikes, spares, games_played, best_score) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute($player);
        }
        
        // Insert the 2 built-in screens (overall and daily leaderboards)
        $builtinScreens = [
            [
                'overall-leaderboard',
                '🎳 REZULTĀTU TABULA - KOPĒJĀ',
                'Labākie boulinga spēlētāji kopumā',
                '',
                'builtin',
                1,
                10000
            ],
            [
                'daily-leaderboard', 
                '🎳 REZULTĀTU TABULA - DIENAS',
                'Labākie boulinga spēlētāji šodien',
                '',
                'builtin',
                2,
                10000
            ]
        ];
        
        foreach ($builtinScreens as $screen) {
            $stmt = $this->conn->prepare("
                INSERT IGNORE INTO screens 
                (screen_id, title, subtitle, content, screen_type, display_order, display_duration) 
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute($screen);
        }
    }
}