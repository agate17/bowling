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
        
        <!-- Leaderboard Screen -->
        <div class="screen active" id="leaderboard">
            <div class="leaderboard-header">
                <h1>🎳 REZULTĀTU TABULA 🎳</h1>
                <div class="subtitle">Labākie boulinga spēlētāji šomēnes</div>
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
                </tbody>
            </table>
        </div>

        <!-- Player of Week Screen -->
        <div class="screen" id="playerOfWeek" style="display: none;">
            <div class="section-header">
                <h1>🏆 NEDĒĻAS SPĒLĒTĀJS 🏆</h1>
                <div class="subtitle">Šīs nedēļas uzvarētājs</div>
            </div>
            <div class="player-spotlight">
                <div class="player-info">
                    <h2>Jānis 'Striker' Ozoliņš</h2>
                    <div class="stats">
                        <div class="stat">
                            <span class="stat-value">234</span>
                            <span class="stat-label">Vidējie punkti</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">12</span>
                            <span class="stat-label">Trāpījumi</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value">5</span>
                            <span class="stat-label">Spēles</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Rankings Screen -->
        <div class="screen" id="teamRankings" style="display: none;">
            <div class="section-header">
                <h1>👥 KOMANDU REITINGS 👥</h1>
                <div class="subtitle">Labākās komandas šomēnes</div>
            </div>
            <table class="leaderboard-table">
                <thead>
                    <tr>
                        <th>Vieta</th>
                        <th>Komanda</th>
                        <th>Vidējie</th>
                        <th>Rezultāts</th>
                        <th>Dalībnieki</th>
                    </tr>
                </thead>
                <tbody id="teamLeaderboardBody">
                </tbody>
            </table>
        </div>

        <!-- Trivia Screen -->
        <div class="screen" id="trivia" style="display: none;">
            <div class="section-header">
                <h1>🧠 BOULINGA VIKTORĪNA 🧠</h1>
                <div class="subtitle">Pārbaudi savas zināšanas</div>
            </div>
            <div class="trivia-screen">
                <div class="trivia-question" id="triviaQuestion">Kāds ir maksimālais punktu skaits ideālā boulinga spēlē?</div>
                <div class="trivia-answer" id="triviaAnswer">Answer: 300 punkti!</div>
            </div>
        </div>
    </div>

    <script src="assets/js/main.js"></script>
    <script src="assets/js/screen-manager.js"></script>
</body>
</html>