// Fixed Custom Screen Management System - Handles Existing Screens
// Replace your screen-manager.js with this version

class ScreenManager {
    constructor() {
        this.customScreens = [];
        this.allScreens = [];
        this.currentScreenIndex = 0;
        this.rotationInterval = null;
        this.isRotating = false;
        this.init();
    }

    init() {
        // Wait a bit for DOM to be fully ready
        setTimeout(() => {
            this.loadCustomScreens();
            this.injectCustomScreens();
            this.getAllScreenElements();
            this.setupInitialDisplay();
            // Only start rotation if there are multiple screens
            if (this.allScreens.length > 1) {
                this.startScreenRotation(10000); // 10 seconds
            }
        }, 100);
    }

    // Load custom screens from localStorage
    loadCustomScreens() {
        try {
            const customScreens = JSON.parse(localStorage.getItem('customScreens') || '[]');
            this.customScreens = customScreens.filter(screen => screen.id && screen.title);
            console.log(`✅ Loaded ${this.customScreens.length} custom screens`);
        } catch (error) {
            console.error('❌ Error loading custom screens:', error);
            this.customScreens = [];
        }
    }

    // Create HTML for custom screen
    createCustomScreenHTML(screenData) {
        return `
            <div class="screen custom-screen" id="${screenData.id}" data-screen-type="${screenData.type}" style="display: none;">
                <div class="section-header">
                    <h1>${screenData.title}</h1>
                    ${screenData.subtitle ? `<div class="subtitle">${screenData.subtitle}</div>` : ''}
                </div>
                <div class="custom-content">
                    ${screenData.content}
                </div>
            </div>
        `;
    }

    // Inject custom screens into the DOM
    injectCustomScreens() {
        // Remove old custom screens first
        document.querySelectorAll('.custom-screen').forEach(screen => screen.remove());

        // Find where to inject screens (look for screen container or body)
        const container = document.querySelector('.screen-container') || 
                         document.querySelector('main') || 
                         document.querySelector('.container') ||
                         document.body;

        // Add new custom screens
        this.customScreens.forEach(screenData => {
            const screenHTML = this.createCustomScreenHTML(screenData);
            container.insertAdjacentHTML('beforeend', screenHTML);
        });

        console.log(`✅ Injected ${this.customScreens.length} custom screens`);
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
    }

    // Refresh screens (reload from localStorage and reinject)
    refresh() {
        console.log('🔄 Refreshing screens...');
        this.stopScreenRotation();
        this.loadCustomScreens();
        this.injectCustomScreens();
        this.getAllScreenElements();
        this.setupInitialDisplay();
        
        if (this.allScreens.length > 1) {
            this.startScreenRotation(10000);
        }
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
    
    // Listen for storage changes to auto-refresh screens
    window.addEventListener('storage', function(e) {
        if (e.key === 'customScreens') {
            console.log('📦 Custom screens updated, refreshing...');
            window.screenManager.refresh();
        }
    });
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

// Debug function - shows all screens briefly
function debugShowAllScreens() {
    if (window.screenManager) {
        console.log('🔍 Debug: Showing all screens for 2 seconds each...');
        window.screenManager.allScreens.forEach((screen, index) => {
            setTimeout(() => {
                window.screenManager.allScreens.forEach(s => s.style.display = 'none');
                screen.style.display = 'block';
                console.log(`🔍 Debug showing: ${screen.id || 'unnamed'} (${index + 1}/${window.screenManager.allScreens.length})`);
            }, index * 2000);
        });
    }
}

// Export for use in other scripts if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = ScreenManager;
}