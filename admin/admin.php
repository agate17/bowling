<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Screen Management</title>
    <link rel="stylesheet" href="../admin_style.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🛠️ Screen Management System</h1>
            <p>Create and manage dynamic content screens</p>
        </div>

        <div class="admin-content">
            <div class="form-section">
                <h2>Create New Screen</h2>
                <form id="screenForm">
                    <div class="form-group">
                        <label for="screenId">Screen ID:</label>
                        <input type="text" id="screenId" required placeholder="e.g., sponsor-slide-1">
                    </div>

                    <div class="form-group">
                        <label for="screenTitle">Title:</label>
                        <input type="text" id="screenTitle" required placeholder="e.g., 🎯 SPECIAL ANNOUNCEMENT">
                    </div>

                    <div class="form-group">
                        <label for="screenSubtitle">Subtitle:</label>
                        <input type="text" id="screenSubtitle" placeholder="e.g., Don't miss out!">
                    </div>

                    <div class="form-group">
                        <label for="screenContent">HTML Content:</label>
                        <textarea id="screenContent" placeholder="Enter HTML content here..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="screenType">Screen Type:</label>
                        <select id="screenType">
                            <option value="announcement">Announcement</option>
                            <option value="sponsor">Sponsor Slide</option>
                            <option value="promotion">Promotion</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>

                    <button type="button" class="btn btn-success" onclick="saveScreen()">💾 Save Screen</button>
                    <button type="button" class="btn btn-danger" onclick="clearForm()">🗑️ Clear Form</button>
                </form>
            </div>

            <div class="preview-section">
                <h2>Live Preview</h2>
                <div class="screen-preview" id="screenPreview">
                    <div class="preview-header">
                        <h1 id="previewTitle">Your Title Here</h1>
                        <div class="preview-subtitle" id="previewSubtitle">Your subtitle here</div>
                    </div>
                    <div class="preview-content" id="previewContent">
                        Your HTML content will appear here...
                    </div>
                </div>
            </div>

            <div class="screen-list">
                <h2>All Screens</h2>
                
                <!-- Built-in Screens Section -->
                <div class="screen-category">
                    <h3>📺 Built-in Screens</h3>
                    <div id="builtinScreensList">
                        <!-- Built-in screens will be listed here -->
                    </div>
                </div>

                <!-- Custom Screens Section -->
                <div class="screen-category">
                    <h3>🎨 Custom Screens</h3>
                    <div id="customScreensList">
                        <!-- Custom screens will be listed here -->
                    </div>
                </div>

                <!-- Controls -->
                <div class="screen-controls">
                    <h3>🎮 Screen Controls</h3>
                    <button class="btn" onclick="openMainDisplay()">🖥️ Open Main Display</button>
                    <button class="btn" onclick="refreshScreens()">🔄 Refresh Screen List</button>
                    <button class="btn" onclick="exportScreens()">📁 Export Custom Screens</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Built-in screen definitions
        const builtinScreens = [
            {
                id: 'leaderboard',
                title: '🎳 REZULTĀTU TABULA',
                type: 'built-in',
                description: 'Bowling leaderboard with player rankings'
            },
            {
                id: 'playerOfWeek',
                title: '🏆 NEDĒĻAS SPĒLĒTĀJS',
                type: 'built-in',
                description: 'Featured player of the week'
            },
            {
                id: 'teamRankings',
                title: '👥 KOMANDU REITINGS',
                type: 'built-in',
                description: 'Team rankings and statistics'
            },
            {
                id: 'trivia',
                title: '🧠 BOULINGA VIKTORĪNA',
                type: 'built-in',
                description: 'Bowling trivia questions'
            }
        ];

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadAllScreens();
            setupPreview();
        });

        // Setup live preview
        function setupPreview() {
            const titleInput = document.getElementById('screenTitle');
            const subtitleInput = document.getElementById('screenSubtitle');
            const contentInput = document.getElementById('screenContent');

            titleInput.addEventListener('input', updatePreview);
            subtitleInput.addEventListener('input', updatePreview);
            contentInput.addEventListener('input', updatePreview);
        }

        // Update live preview
        function updatePreview() {
            const title = document.getElementById('screenTitle').value || 'Your Title Here';
            const subtitle = document.getElementById('screenSubtitle').value || 'Your subtitle here';
            const content = document.getElementById('screenContent').value || 'Your HTML content will appear here...';

            document.getElementById('previewTitle').textContent = title;
            document.getElementById('previewSubtitle').textContent = subtitle;
            document.getElementById('previewContent').innerHTML = content;
        }

        // Save screen to localStorage
        function saveScreen() {
            const screenData = {
                id: document.getElementById('screenId').value,
                title: document.getElementById('screenTitle').value,
                subtitle: document.getElementById('screenSubtitle').value,
                content: document.getElementById('screenContent').value,
                type: document.getElementById('screenType').value,
                created: new Date().toISOString()
            };

            // Validate required fields
            if (!screenData.id || !screenData.title) {
                alert('Please fill in Screen ID and Title fields!');
                return;
            }

            // Check if it conflicts with built-in screens
            if (builtinScreens.find(screen => screen.id === screenData.id)) {
                alert('Screen ID conflicts with built-in screen. Please choose a different ID.');
                return;
            }

            // Get existing screens
            let screens = JSON.parse(localStorage.getItem('customScreens') || '[]');
            
            // Check if screen ID already exists
            if (screens.find(screen => screen.id === screenData.id)) {
                if (!confirm('Screen ID already exists. Do you want to overwrite it?')) {
                    return;
                }
                screens = screens.filter(screen => screen.id !== screenData.id);
            }

            // Add new screen
            screens.push(screenData);
            localStorage.setItem('customScreens', JSON.stringify(screens));

            alert('Screen saved successfully!');
            clearForm();
            loadAllScreens();
        }

        // Clear form
        function clearForm() {
            document.getElementById('screenForm').reset();
            updatePreview();
        }

        // Load all screens (built-in + custom)
        function loadAllScreens() {
            loadBuiltinScreens();
            loadCustomScreens();
        }

        // Load built-in screens
        function loadBuiltinScreens() {
            const builtinList = document.getElementById('builtinScreensList');
            
            if (builtinScreens.length === 0) {
                builtinList.innerHTML = '<p style="color: #666;">No built-in screens found.</p>';
                return;
            }

            builtinList.innerHTML = builtinScreens.map(screen => `
                <div class="screen-item builtin-screen">
                    <div>
                        <h4>${screen.title}</h4>
                        <p>ID: ${screen.id} | Type: ${screen.type}</p>
                        <small>${screen.description}</small>
                    </div>
                    <div class="screen-actions">
                        <button class="btn btn-small" onclick="viewScreen('${screen.id}')" title="View in main display">👁️ View</button>
                        <span class="screen-status">Built-in</span>
                    </div>
                </div>
            `).join('');
        }

        // Load custom screens
        function loadCustomScreens() {
            const screens = JSON.parse(localStorage.getItem('customScreens') || '[]');
            const customList = document.getElementById('customScreensList');

            if (screens.length === 0) {
                customList.innerHTML = '<p style="color: #666;">No custom screens created yet.</p>';
                return;
            }

            customList.innerHTML = screens.map(screen => `
                <div class="screen-item custom-screen">
                    <div>
                        <h4>${screen.title}</h4>
                        <p>ID: ${screen.id} | Type: ${screen.type} | Created: ${new Date(screen.created).toLocaleDateString()}</p>
                        ${screen.subtitle ? `<small>Subtitle: ${screen.subtitle}</small>` : ''}
                    </div>
                    <div class="screen-actions">
                        <button class="btn btn-small" onclick="editScreen('${screen.id}')">✏️ Edit</button>
                        <button class="btn btn-small" onclick="viewScreen('${screen.id}')" title="View in main display">👁️ View</button>
                        <button class="btn btn-danger btn-small" onclick="deleteScreen('${screen.id}')">🗑️ Delete</button>
                    </div>
                </div>
            `).join('');
        }

        // Edit screen
        function editScreen(screenId) {
            const screens = JSON.parse(localStorage.getItem('customScreens') || '[]');
            const screen = screens.find(s => s.id === screenId);

            if (screen) {
                document.getElementById('screenId').value = screen.id;
                document.getElementById('screenTitle').value = screen.title;
                document.getElementById('screenSubtitle').value = screen.subtitle || '';
                document.getElementById('screenContent').value = screen.content || '';
                document.getElementById('screenType').value = screen.type || 'custom';
                updatePreview();
                
                // Scroll to form
                document.getElementById('screenForm').scrollIntoView({ behavior: 'smooth' });
            }
        }

        // Delete screen
        function deleteScreen(screenId) {
            if (confirm('Are you sure you want to delete this screen?')) {
                let screens = JSON.parse(localStorage.getItem('customScreens') || '[]');
                screens = screens.filter(screen => screen.id !== screenId);
                localStorage.setItem('customScreens', JSON.stringify(screens));
                loadAllScreens();
                alert('Screen deleted successfully!');
            }
        }

        // View specific screen in main display
        function viewScreen(screenId) {
            // Try to communicate with main display if it's open
            try {
                if (window.opener && !window.opener.closed) {
                    // If admin was opened from main display
                    if (window.opener.showScreen) {
                        window.opener.showScreen(screenId);
                        alert(`Switched main display to screen: ${screenId}`);
                    } else if (window.opener.screenManager) {
                        window.opener.screenManager.showScreen(screenId);
                        alert(`Switched main display to screen: ${screenId}`);
                    }
                } else {
                    // Open main display and show screen
                    const mainWindow = window.open('index.html', 'mainDisplay', 'width=1200,height=800');
                    setTimeout(() => {
                        if (mainWindow.screenManager) {
                            mainWindow.screenManager.showScreen(screenId);
                        }
                    }, 2000);
                }
            } catch (error) {
                console.error('Error communicating with main display:', error);
                alert('Could not communicate with main display. Please open main display manually.');
            }
        }

        // Open main display
        function openMainDisplay() {
            window.open('index.html', 'mainDisplay', 'width=1200,height=800');
        }

        // Refresh screen list
        function refreshScreens() {
            loadAllScreens();
            alert('Screen list refreshed!');
        }

        // Export screens data (for future database migration)
        function exportScreens() {
            const screens = JSON.parse(localStorage.getItem('customScreens') || '[]');
            const dataStr = JSON.stringify(screens, null, 2);
            const dataBlob = new Blob([dataStr], {type: 'application/json'});
            
            const link = document.createElement('a');
            link.href = URL.createObjectURL(dataBlob);
            link.download = 'custom-screens-backup.json';
            link.click();
        }

        // Add some CSS for the new elements
        const additionalCSS = `
            <style>
                .screen-category {
                    margin: 20px 0;
                    border: 1px solid #e0e0e0;
                    border-radius: 8px;
                    padding: 15px;
                }
                .screen-category h3 {
                    margin-top: 0;
                    color: #2c3e50;
                    border-bottom: 2px solid #3498db;
                    padding-bottom: 5px;
                }
                .builtin-screen {
                    background: #f8f9fa;
                    border-left: 4px solid #28a745;
                }
                .custom-screen {
                    background: #fff;
                    border-left: 4px solid #007bff;
                }
                .screen-status {
                    background: #28a745;
                    color: white;
                    padding: 2px 8px;
                    border-radius: 12px;
                    font-size: 12px;
                    font-weight: bold;
                }
                .screen-controls {
                    margin: 20px 0;
                    padding: 15px;
                    background: #f1f3f4;
                    border-radius: 8px;
                }
                .screen-controls h3 {
                    margin-top: 0;
                }
                .screen-item {
                    margin-bottom: 10px;
                }
            </style>
        `;
        document.head.insertAdjacentHTML('beforeend', additionalCSS);
    </script>
</body>
</html>