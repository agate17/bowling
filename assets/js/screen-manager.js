// Updated Screen Management System - Database Integration
// Replace your screen-manager.js with this version

class ScreenManager {
    constructor() {
        this.customScreens = [];
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

    // Load screens from database instead of localStorage
    async loadScreensFromDatabase() {
        try {
            console.log('📡 Loading screens from database...');
            const response = await fetch(this.API_BASE + 'get-screens.php');
            const result = await response.json();

            if (result.success) {
                // Combine builtin and custom screens
                const allDatabaseScreens = [...result.data.builtin, ...result.data.custom];
                this.customScreens = result.data.custom;
                
                console.log(`✅ Loaded ${allDatabaseScreens.length} screens from database`);
                console.log('   - Builtin screens:', result.data.builtin.length);
                console.log('   - Custom screens:', result.data.custom.length);
                
                // Inject custom screens into DOM
                this.injectCustomScreens(result.data.custom);
                
                // Get all screen elements and setup display
                this.getAllScreenElements();
                this.setupInitialDisplay();
                
                // Start rotation if there are multiple screens
                if (this.allScreens.length > 1) {
                    this.startScreenRotation(10000); // 10 seconds
                }
            } else {
                console.error('❌ Failed to load screens from database:', result.error);
                // Fallback to existing screens
                this.getAllScreenElements();
                this.setupInitialDisplay();
            }
        } catch (error) {
            console.error('❌ Error loading screens from database:', error);
            // Fallback to existing screens
            this.getAllScreenElements();
            this.setupInitialDisplay();
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

    // Inject custom screens into the DOM
    injectCustomScreens(customScreens) {
        // Remove old custom screens first
        document.querySelectorAll('.custom-screen').forEach(screen => screen.remove());

        // Find where to inject screens (look for screen container or body)
        const container = document.querySelector('.container') || 
                         document.querySelector('main') || 
                         document.body;

        // Add new custom screens
        customScreens.forEach(screenData => {
            const screenHTML = this.createCustomScreenHTML(screenData);
            container.insertAdjacentHTML('beforeend', screenHTML);
        });

        console.log(`✅ Injected ${customScreens.length} custom screens into DOM`);
    }

    // Get all screen elements (existing + custom)
    getAllScreenElements() {
        this.allScreens = Array.from(document.querySelectorAll('.screen'));
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

    // Refresh screens (reload from database and reinject)
    async refresh() {
        console.log('🔄 Refreshing screens from database...');
        this.stopScreenRotation();
        await this.loadScreensFromDatabase();
    }

    // Get screen statistics
    getStats() {
        const existingScreens = this.allScreens.length - this.customScreens.length;
        return {
            totalScreens: this.allScreens.length,
            customScreens: this.customScreens.length,
            existingScreens: existingScreens,
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