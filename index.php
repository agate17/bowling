<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bowling Stats Display</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    <a href="admin/admin.php" class="admin-btn" title="Admin Panel" style="right: 72px;">Admin</a>
    <button id="fullscreen-btn" class="fullscreen-btn" title="Toggle Fullscreen" aria-label="Toggle Fullscreen">
        <i class="bi bi-arrows-fullscreen"></i>
    </button>
    
    <div class="container">
        <div class="progress-bar" id="progressBar"></div>
        
        <!-- Overall Leaderboard Screen (Built-in) -->
        <div class="screen" id="overall-leaderboard" data-screen-type="builtin">
            <div class="leaderboard-header">
                <h1>🎳 REZULTĀTU TABULA - KOPĒJĀ 🎳</h1>
                <div class="subtitle">Labākie boulinga spēlētāji kopumā</div>
            </div>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Vieta</th>
                        <th>Spēlētājs</th>
                        <th>Punkti</th>
                        <th>Trāpījumi</th>
                        <th>Rezerves</th>
                        <th>Spēles</th>
                    </tr>
                </thead>
                <tbody id="leaderboardBody">
                    <!-- Data will be loaded by main.js -->
                </tbody>
            </table>
        </div>

        <!-- Daily Leaderboard Screen (Built-in) -->
        <div class="screen" id="daily-leaderboard" data-screen-type="builtin" style="display: none;">
            <div class="leaderboard-header">
                <h1>🎳 REZULTĀTU TABULA - DIENAS 🎳</h1>
                <div class="subtitle">Labākie boulinga spēlētāji šodien</div>
            </div>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Vieta</th>
                        <th>Spēlētājs</th>
                        <th>Punkti</th>
                        <th>Trāpījumi</th>
                        <th>Rezerves</th>
                        <th>Spēles</th>
                    </tr>
                </thead>
                <tbody id="dailyLeaderboardBody">
                    <!-- Data will be loaded by main.js -->
                </tbody>
            </table>
        </div>

        <!-- Team Rankings Screen (Built-in) -->
        <div class="screen" id="team-rankings" data-screen-type="builtin" style="display: none;">
            <div class="section-header">
                <h1>🏆 KOMANDU REITINGS 🏆</h1>
                <div class="subtitle">Labākās komandas šomēnes</div>
            </div>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Vieta</th>
                        <th>Komanda</th>
                        <th>Vidējais</th>
                        <th>Uzvaras</th>
                        <th>Dalībnieki</th>
                    </tr>
                </thead>
                <tbody id="teamLeaderboardBody">
                    <!-- Data will be loaded by main.js -->
                </tbody>
            </table>
        </div>

        <!-- Trivia Screen (Built-in) -->
        <div class="screen" id="trivia-screen" data-screen-type="builtin" style="display: none;">
            <div class="section-header">
                <h1>🧠 BOULINGA JAUTĀJUMS 🧠</h1>
            </div>
            <div class="trivia-content">
                <div class="trivia-question" id="triviaQuestion">
                    Kāds ir maksimālais punktu skaits ideālā boulinga spēlē?
                </div>
                <div class="trivia-answer" id="triviaAnswer">
                    Atbilde: 300 punkti!
                </div>
            </div>
        </div>

        <!-- Custom screens will be injected here by screen-manager.js -->
        
    </div>

    <!-- Status indicator for debugging -->
    <div id="debug-info" style="position: fixed; bottom: 10px; left: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-size: 12px; max-width: 300px; display: none;">
        <div>Current Screen: <span id="current-screen-debug">Loading...</span></div>
        <div>Total Screens: <span id="total-screens-debug">0</span></div>
        <div>Custom Screens: <span id="custom-screens-debug">0</span></div>
        <div>Rotation: <span id="rotation-debug">Stopped</span></div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    <script src="assets/js/screen-manager.js"></script>
    
    <!-- Debug toggle - remove in production -->
    <script>
        // Toggle debug info with Ctrl+D
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey && e.key === 'd') {
                e.preventDefault();
                const debugInfo = document.getElementById('debug-info');
                debugInfo.style.display = debugInfo.style.display === 'none' ? 'block' : 'none';
                
                if (debugInfo.style.display === 'block' && window.screenManager) {
                    updateDebugInfo();
                    setInterval(updateDebugInfo, 2000);
                }
            }
        });
        
        function updateDebugInfo() {
            if (window.screenManager) {
                const stats = window.screenManager.getStats();
                document.getElementById('current-screen-debug').textContent = stats.currentScreen;
                document.getElementById('total-screens-debug').textContent = stats.totalScreens;
                document.getElementById('custom-screens-debug').textContent = stats.customScreens;
                document.getElementById('rotation-debug').textContent = stats.isRotating ? 'Running' : 'Stopped';
            }
        }
    </script>
</body>
</html>