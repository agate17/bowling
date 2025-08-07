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
        
        <!-- Loading message - will be replaced by database screens -->
        <div class="screen loading-screen" id="loading-screen">
            <div class="section-header">
                <h1>🎳 LOADING SCREENS... 🎳</h1>
                <div class="subtitle">Please wait while we load the display data</div>
            </div>
            <div style="text-align: center; margin-top: 50px; font-size: 24px;">
                ⏳ Loading...
            </div>
        </div>

        <!-- All screens will be dynamically loaded from database -->
        
    </div>
    <script src="assets/js/screen-manager.js"></script>
    <!-- Status indicator for debugging -->
    <div id="debug-info" style="position: fixed; bottom: 10px; left: 10px; background: rgba(0,0,0,0.8); color: white; padding: 10px; border-radius: 5px; font-size: 12px; max-width: 300px; display: none;">
        <div>Current Screen: <span id="current-screen-debug">Loading...</span></div>
        <div>Total Screens: <span id="total-screens-debug">0</span></div>
        <div>Custom Screens: <span id="custom-screens-debug">0</span></div>
        <div>Rotation: <span id="rotation-debug">Stopped</span></div>
    </div>

    <!-- Scripts -->
    <script src="assets/js/main.js"></script>
    
    
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