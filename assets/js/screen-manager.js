// Updated Screen Management System - Database Integration Only
// This version loads ALL screens from database (builtin + custom)

class ScreenManager {
    constructor() {
        this.allScreens = [];
        this.currentScreenIndex = 0;
        this.rotationInterval = null;
        this.isRotating = false;
        this.API_BASE = 'backend/';
        this.init();
    }

    init() {
        console.log('🚀 Initializing Screen Manager...');
        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
            this.loadScreensFromDatabase();
        }, 100);
    }

    // Load screens from database and create ALL screens dynamically
    async loadScreensFromDatabase() {
        try {
            console.log('📡 Loading screens from database...');
            console.log('API URL:', this.API_BASE + 'get-screens.php');
            
            const response = await fetch(this.API_BASE + 'get-screens.php');
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            console.log('API Response:', result);

            if (result.success) {
                // Combine builtin and custom screens
                const allDatabaseScreens = [...result.data.builtin, ...result.data.custom];
                
                console.log(`✅ Loaded ${allDatabaseScreens.length} screens from database`);
                console.log('   - Builtin screens:', result.data.builtin.length, result.data.builtin);
                console.log('   - Custom screens:', result.data.custom.length, result.data.custom);
                
                if (allDatabaseScreens.length === 0) {
                    console.warn('⚠️ No screens found in database!');
                    this.showNoScreensMessage();
                    return;
                }
                
                // Clear existing screens and create new ones from database
                this.clearAllScreens();
                this.createScreensFromDatabase(allDatabaseScreens);
                
                // Setup display
                this.getAllScreenElements();
                this.setupInitialDisplay();
                
                // Start rotation if there are multiple screens
                if (this.allScreens.length > 1) {
                    this.startScreenRotation(10000); // 10 seconds
                } else if (this.allScreens.length === 0) {
                    this.showErrorScreen();
                }
            } else {
                console.error('❌ Failed to load screens from database:', result.error);
                console.error('Debug info:', result.debug_info);
                this.showErrorScreen(`Database Error: ${result.error}`);
            }
        } catch (error) {
            console.error('❌ Error loading screens from database:', error);
            this.showErrorScreen(`Network Error: ${error.message}`);
        }
    }

    // Clear all existing screens from DOM
    clearAllScreens() {
        const container = document.querySelector('.container');
        if (container) {
            // Remove all screens except progress bar and loading screen
            const screens = container.querySelectorAll('.screen:not(.loading-screen)');
            screens.forEach(screen => screen.remove());
            
            // Hide loading screen
            const loadingScreen = container.querySelector('.loading-screen');
            if (loadingScreen) {
                loadingScreen.style.display = 'none';
            }
        }
    }

    // Show error screen when database fails
    showErrorScreen(errorMessage = 'Cannot connect to database') {
        const container = document.querySelector('.container');
        const errorHTML = `
            <div class="screen error-screen" id="error-screen">
                <div class="section-header">
                    <h1>❌ ERROR LOADING SCREENS ❌</h1>
                    <div class="subtitle">${errorMessage}</div>
                </div>
                <div style="text-align: center; margin-top: 50px; font-size: 18px; color: #ff6b6b;">
                    <p>Unable to load display screens from database.</p>
                    <p>Please check your database connection and try again.</p>
                    <p style="font-size: 14px; color: #666; margin-top: 20px;">
                        Debug: Open browser console for more details
                    </p>
                    <button onclick="window.location.reload()" style="margin-top: 20px; padding: 10px 20px; font-size: 16px; background: #4ECDC4; color: white; border: none; border-radius: 5px; cursor: pointer;">
                        🔄 Retry
                    </button>
                    <br><br>
                    <a href="debug-screens.php" target="_blank" style="background: #ff9500; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block;">
                        🔍 Debug Database
                    </a>
                </div>
            </div>
        `;
        
        if (container) {
            // Remove loading screen
            const loadingScreen = container.querySelector('.loading-screen');
            if (loadingScreen) {
                loadingScreen.remove();
            }
            
            container.insertAdjacentHTML('beforeend', errorHTML);
            this.getAllScreenElements();
            this.setupInitialDisplay();
        }
    }

    // Show message when no screens are found
    showNoScreensMessage() {
        const container = document.querySelector('.container');
        const noScreensHTML = `
            <div class="screen no-screens" id="no-screens">
                <div class="section-header">
                    <h1>📺 NO SCREENS CONFIGURED</h1>
                    <div class="subtitle">No screens found in database</div>
                </div>
                <div style="text-align: center; margin-top: 50px; font-size: 18px; color: #ffa500;">
                    <p>The database connection is working, but no screens are configured.</p>
                    <p>Please add some screens through the admin panel.</p>
                    <br>
                    <a href="admin/admin.php" style="background: #4ECDC4; color: white; padding: 15px 30px; text-decoration: none; border-radius: 5px; display: inline-block; font-size: 18px;">
                        🛠️ Go to Admin Panel
                    </a>
                </div>
            </div>
        `;
        
        if (container) {
            // Remove loading screen
            const loadingScreen = container.querySelector('.loading-screen');
            if (loadingScreen) {
                loadingScreen.remove();
            }
            
            container.insertAdjacentHTML('beforeend', noScreensHTML);
            this.getAllScreenElements();
            this.setupInitialDisplay();
        }
    }

    // Create screens from database data
    // Create screens from database data
    createScreensFromDatabase(screensData) {
        const container = document.querySelector('.container') || document.body;

        screensData.forEach(screenData => {
            let screenHTML;

            // Debug: show incoming screen data
            console.log('📄 Processing screenData:', screenData);

            if (screenData.screen_type === 'builtin') {
                screenHTML = this.createBuiltinScreenHTML(screenData);
            } else {
                screenHTML = this.createCustomScreenHTML(screenData);
            }

            // If HTML is empty or undefined, log a warning
            if (!screenHTML || screenHTML.trim() === '') {
                console.warn(`❌ screenHTML is empty or undefined for screen: ${screenData.screen_id}`);
                return;
            }

            // Try inserting into DOM
            try {
                container.insertAdjacentHTML('beforeend', screenHTML);
                console.log(`✅ Inserted screen: ${screenData.screen_id}`);
            } catch (e) {
                console.error(`🚫 Error inserting screen ${screenData.screen_id}:`, e);
            }
        });

        // After all are processed
        console.log(`✅ Created ${screensData.length} screens in DOM`);
    }


    // Create HTML for built-in screens (leaderboards, etc.)
    createBuiltinScreenHTML(screenData) {
        switch (screenData.screen_id) {
            case 'overall-leaderboard':
                return `
                    <div class="screen" id="${screenData.screen_id}" data-screen-type="builtin" style="display: none;">
                        <div class="leaderboard-header">
                            <h1>${screenData.title}</h1>
                            ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
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
                        <div class="progress-bar" id="progressBar-${screenData.screen_id}"></div>
                    </div>
                `;

            case 'daily-leaderboard':
                return `
                    <div class="screen" id="${screenData.screen_id}" data-screen-type="builtin" style="display: none;">
                        <div class="leaderboard-header">
                            <h1>${screenData.title}</h1>
                            ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
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
                        <div class="progress-bar" id="progressBar-${screenData.screen_id}"></div>
                    </div>
                `;

            case 'team-rankings':
                return `
                    <div class="screen" id="${screenData.screen_id}" data-screen-type="builtin" style="display: none;">
                        <div class="section-header">
                            <h1>${screenData.title}</h1>
                            ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
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
                        <div class="progress-bar" id="progressBar-${screenData.screen_id}"></div>
                    </div>
                `;

            case 'trivia-screen':
                return `
                    <div class="screen" id="${screenData.screen_id}" data-screen-type="builtin" style="display: none;">
                        <div class="section-header">
                            <h1>${screenData.title}</h1>
                            ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
                        </div>
                        <div class="trivia-content">
                            <div class="trivia-question" id="triviaQuestion">
                                Kāds ir maksimālais punktu skaits ideālā boulinga spēlē?
                            </div>
                            <div class="trivia-answer" id="triviaAnswer">
                                Atbilde: 300 punkti!
                            </div>
                        </div>
                        <div class="progress-bar" id="progressBar-${screenData.screen_id}"></div>
                    </div>
                `;

            default:
                // Generic built-in screen
                return `
                    <div class="screen" id="${screenData.screen_id}" data-screen-type="builtin" style="display: none;">
                        <div class="section-header">
                            <h1>${screenData.title}</h1>
                            ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
                        </div>
                        <div class="builtin-content">
                            ${screenData.content || '<p>Built-in screen content would go here.</p>'}
                        </div>
                        <div class="progress-bar" id="progressBar-${screenData.screen_id}"></div>
                    </div>
                `;
        }
    }

    // Create HTML for custom screen
    createCustomScreenHTML(screenData) {
        return `
            <div class="screen custom-screen" id="${screenData.screen_id}" data-screen-type="${screenData.screen_type}" style="display: none;">
                <div class="section-header">
                    <h1>${screenData.title}</h1>
                    ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
                </div>
                <div class="custom-content">
                    ${screenData.content || ''}
                </div>
                <div class="progress-bar" id="progressBar-${screenData.screen_id}"></div>
            </div>
        `;
    }

    // Get all screen elements
    getAllScreenElements() {
        this.allScreens = Array.from(document.querySelectorAll('.screen:not(.loading-screen):not(.error-screen)'));
        console.log(`✅ Found ${this.allScreens.length} total screens:`, 
                   this.allScreens.map(s => s.id || 'unnamed'));
    }

    // Setup initial display - show first screen, hide others
    setupInitialDisplay() {
        if (this.allScreens.length === 0) {
            console.warn('⚠️ No screens found!');
            return;
        }

        // Hide all screens first
        this.allScreens.forEach((screen, index) => {
            screen.style.display = 'none';
        });

        // Show the first screen
        this.currentScreenIndex = 0;
        this.allScreens[0].style.display = 'block';
        console.log(`✅ Showing initial screen: ${this.allScreens[0].id || 'unnamed'}`);

        // Start progress bar for current screen
        this.updateProgressBar(10000);

        // If only one screen, make sure it's visible
        if (this.allScreens.length === 1) {
            console.log('ℹ️ Only one screen found, no rotation needed');
        }
    }

    // Start automatic screen rotation
    startScreenRotation(interval = 10000) {
        if (this.allScreens.length <= 1) {
            console.log('ℹ️ Not enough screens for rotation');
            return;
        }

        if (this.isRotating) {
            console.log('ℹ️ Rotation already running');
            return;
        }

        this.isRotating = true;
        console.log(`🔄 Starting screen rotation (${interval/1000}s interval)`);

        this.rotationInterval = setInterval(() => {
            this.nextScreen();
        }, interval);
    }

    // Stop screen rotation
    stopScreenRotation() {
        if (this.rotationInterval) {
            clearInterval(this.rotationInterval);
            this.rotationInterval = null;
            this.isRotating = false;
            console.log('⏹️ Screen rotation stopped');
        }
    }

    // Move to next screen
    nextScreen() {
        if (this.allScreens.length <= 1) return;

        // Hide current screen
        this.allScreens[this.currentScreenIndex].style.display = 'none';
        
        // Move to next screen
        this.currentScreenIndex = (this.currentScreenIndex + 1) % this.allScreens.length;
        
        // Show next screen
        this.allScreens[this.currentScreenIndex].style.display = 'block';
        
        const currentScreen = this.allScreens[this.currentScreenIndex];
        console.log(`🔄 Switched to screen: ${currentScreen.id || 'unnamed'}`);
        
        // Restart progress bar for new screen
        this.updateProgressBar(10000);
    }

    // Move to previous screen
    previousScreen() {
        if (this.allScreens.length <= 1) return;

        // Hide current screen
        this.allScreens[this.currentScreenIndex].style.display = 'none';
        
        // Move to previous screen
        this.currentScreenIndex = (this.currentScreenIndex - 1 + this.allScreens.length) % this.allScreens.length;
        
        // Show previous screen
        this.allScreens[this.currentScreenIndex].style.display = 'block';
        
        const currentScreen = this.allScreens[this.currentScreenIndex];
        console.log(`🔄 Switched to screen: ${currentScreen.id || 'unnamed'}`);
        
        // Restart progress bar for new screen
        this.updateProgressBar(10000);
    }

    // Progress bar function
    updateProgressBar(duration = 10000) {
        // Try to find progress bar in current screen first
        const currentScreen = this.allScreens[this.currentScreenIndex];
        let progressBar = currentScreen ? currentScreen.querySelector('.progress-bar') : null;
        
        // Fallback to global progress bar
        if (!progressBar) {
            progressBar = document.getElementById('progressBar');
        }
        
        if (!progressBar) return;
        
        // Reset progress bar
        progressBar.style.width = '0%';
        
        const startTime = Date.now();
        
        const animate = () => {
            const elapsed = Date.now() - startTime;
            const progress = Math.min((elapsed / duration) * 100, 100);
            
            progressBar.style.width = progress + '%';
            
            if (progress < 100) {
                requestAnimationFrame(animate);
            }
        };
        
        animate();
    }

    // Refresh screens (reload from database and recreate)
    async refresh() {
        console.log('🔄 Refreshing screens from database...');
        this.stopScreenRotation();
        await this.loadScreensFromDatabase();
    }

    // Get screen statistics
    getStats() {
        const customScreens = this.allScreens.filter(s => s.classList.contains('custom-screen')).length;
        const builtinScreens = this.allScreens.length - customScreens;
        
        return {
            totalScreens: this.allScreens.length,
            customScreens: customScreens,
            builtinScreens: builtinScreens,
            currentScreen: this.allScreens[this.currentScreenIndex]?.id || 'none',
            isRotating: this.isRotating
        };
    }

    // Show specific screen by ID
    showScreen(screenId) {
        const screenIndex = this.allScreens.findIndex(screen => screen.id === screenId);
        if (screenIndex !== -1) {
            // Hide current screen
            this.allScreens[this.currentScreenIndex].style.display = 'none';
            // Show target screen
            this.currentScreenIndex = screenIndex;
            this.allScreens[this.currentScreenIndex].style.display = 'block';
            // Restart progress bar
            this.updateProgressBar(10000);
            console.log(`🎯 Manually switched to screen: ${screenId}`);
        } else {
            console.warn(`⚠️ Screen with ID '${screenId}' not found`);
        }
    }
}

// Initialize the screen manager when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initializing Screen Manager...');
    window.screenManager = new ScreenManager();
});

// Global utility functions
function nextScreen() {
    if (window.screenManager) {
        window.screenManager.nextScreen();
    }
}

function previousScreen() {
    if (window.screenManager) {
        window.screenManager.previousScreen();
    }
}

function showScreen(screenId) {
    if (window.screenManager) {
        window.screenManager.showScreen(screenId);
    }
}

function toggleRotation() {
    if (window.screenManager) {
        if (window.screenManager.isRotating) {
            window.screenManager.stopScreenRotation();
        } else {
            window.screenManager.startScreenRotation(10000);
        }
    }
}

function getScreenStats() {
    if (window.screenManager) {
        console.table(window.screenManager.getStats());
        return window.screenManager.getStats();
    }
}

function refreshScreens() {
    if (window.screenManager) {
        window.screenManager.refresh();
    }
}

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ScreenManager;
}