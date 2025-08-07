<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Screen Management</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', sans-serif; background: #f5f7fa; color: #2c3e50; }
        
        .admin-container { max-width: 1400px; margin: 0 auto; padding: 20px; }
        .admin-header { text-align: center; margin-bottom: 30px; background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .admin-header h1 { color: #2c3e50; font-size: 2.5em; margin-bottom: 10px; }
        
        .admin-content { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        
        .form-section, .preview-section { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .form-section h2, .preview-section h2 { margin-bottom: 20px; color: #2980b9; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #34495e; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 12px; border: 2px solid #e0e6ed; border-radius: 8px; font-size: 14px; transition: border-color 0.3s; }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus { outline: none; border-color: #3498db; }
        .form-group textarea { resize: vertical; min-height: 120px; font-family: 'Courier New', monospace; }
        
        .btn { background: #3498db; color: white; border: none; padding: 12px 20px; border-radius: 8px; cursor: pointer; font-size: 14px; font-weight: 600; margin: 5px; transition: all 0.3s; }
        .btn:hover { background: #2980b9; transform: translateY(-2px); }
        .btn-success { background: #27ae60; }
        .btn-success:hover { background: #229954; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .btn-small { padding: 8px 12px; font-size: 12px; }
        
        .screen-preview { border: 2px solid #e0e6ed; border-radius: 12px; padding: 20px; min-height: 300px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
        .preview-header { text-align: center; margin-bottom: 20px; }
        .preview-header h1 { font-size: 2em; margin-bottom: 10px; }
        .preview-subtitle { font-size: 1.2em; opacity: 0.9; }
        .preview-content { background: rgba(255,255,255,0.1); padding: 15px; border-radius: 8px; min-height: 100px; }
        
        .screen-list { background: white; padding: 25px; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        .screen-category { margin: 20px 0; border: 1px solid #e0e6ed; border-radius: 12px; padding: 20px; }
        .screen-category h3 { margin-top: 0; color: #2c3e50; border-bottom: 2px solid #3498db; padding-bottom: 8px; }
        
        .screen-item { display: flex; justify-content: space-between; align-items: center; padding: 15px; margin: 10px 0; border: 1px solid #e0e6ed; border-radius: 10px; transition: all 0.3s; }
        .screen-item:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
        .screen-item h4 { margin: 0 0 5px 0; color: #2c3e50; }
        .screen-item p { margin: 0; color: #7f8c8d; font-size: 13px; }
        .screen-item small { color: #95a5a6; }
        
        .builtin-screen { border-left: 4px solid #27ae60; background: #f8fff9; }
        .custom-screen { border-left: 4px solid #3498db; background: #f8fbff; }
        
        .screen-actions { display: flex; gap: 5px; }
        .screen-status { background: #27ae60; color: white; padding: 4px 12px; border-radius: 15px; font-size: 11px; font-weight: bold; }
        
        .screen-controls { margin: 20px 0; padding: 20px; background: #f8f9fa; border-radius: 12px; }
        .screen-controls h3 { margin-top: 0; color: #2c3e50; }
        
        .status-message { padding: 15px; border-radius: 8px; margin: 15px 0; font-weight: 600; }
        .status-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
        
        .loading { opacity: 0.6; pointer-events: none; }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1>🛠️ Screen Management System</h1>
            <p>Create and manage dynamic content screens with database storage</p>
        </div>

        <div id="statusMessage"></div>

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

                    <div class="form-group">
                        <label for="displayDuration">Display Duration (ms):</label>
                        <input type="number" id="displayDuration" value="10000" min="1000" max="60000" step="1000">
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
        </div>

        <div class="screen-list">
            <h2>All Screens</h2>
            
            <!-- Built-in Screens Section -->
            <div class="screen-category">
                <h3>📺 Built-in Screens</h3>
                <div id="builtinScreensList">
                    <p>Loading built-in screens...</p>
                </div>
            </div>

            <!-- Custom Screens Section -->
            <div class="screen-category">
                <h3>🎨 Custom Screens</h3>
                <div id="customScreensList">
                    <p>Loading custom screens...</p>
                </div>
            </div>

            <!-- Controls -->
            <div class="screen-controls">
                <h3>🎮 Screen Controls</h3>
                <button class="btn" onclick="openMainDisplay()">🖥️ Open Main Display</button>
                <button class="btn" onclick="refreshScreens()">🔄 Refresh Screen List</button>
                <button class="btn" onclick="testDatabaseConnection()">🔗 Test Database</button>
                <button class="btn" onclick="exportScreens()">📁 Export Custom Screens</button>
            </div>
        </div>
    </div>

    <script>
        // API Base URL - adjust if needed
        const API_BASE = '../backend/';
        
        let isEditingScreen = false;
        let editingScreenId = null;

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

        // Show status message
        function showStatus(message, type = 'success') {
            const statusDiv = document.getElementById('statusMessage');
            statusDiv.className = `status-message status-${type}`;
            statusDiv.textContent = message;
            statusDiv.style.display = 'block';
            
            setTimeout(() => {
                statusDiv.style.display = 'none';
            }, 5000);
        }

        // Save screen to database
        async function saveScreen() {
            const screenData = {
                screen_id: document.getElementById('screenId').value,
                title: document.getElementById('screenTitle').value,
                subtitle: document.getElementById('screenSubtitle').value,
                content: document.getElementById('screenContent').value,
                screen_type: document.getElementById('screenType').value,
                display_duration: parseInt(document.getElementById('displayDuration').value) || 10000
            };

            // Validate required fields
            if (!screenData.screen_id || !screenData.title) {
                showStatus('Please fill in Screen ID and Title fields!', 'error');
                return;
            }

            try {
                const method = isEditingScreen ? 'PUT' : 'POST';
                const response = await fetch(API_BASE + 'add-screen.php', {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(screenData)
                });

                const result = await response.json();

                if (result.success) {
                    showStatus(result.message, 'success');
                    clearForm();
                    loadAllScreens();
                } else {
                    showStatus(result.error || 'Failed to save screen', 'error');
                }
            } catch (error) {
                showStatus('Network error: ' + error.message, 'error');
            }
        }

        // Clear form
        function clearForm() {
            document.getElementById('screenForm').reset();
            document.getElementById('displayDuration').value = '10000';
            isEditingScreen = false;
            editingScreenId = null;
            updatePreview();
        }

        // Load all screens from database
        async function loadAllScreens() {
            try {
                const response = await fetch(API_BASE + 'get-screens.php');
                const result = await response.json();

                if (result.success) {
                    displayBuiltinScreens(result.data.builtin);
                    displayCustomScreens(result.data.custom);
                } else {
                    showStatus('Failed to load screens: ' + result.error, 'error');
                }
            } catch (error) {
                showStatus('Network error loading screens: ' + error.message, 'error');
            }
        }

        // Display built-in screens
        function displayBuiltinScreens(screens) {
            const builtinList = document.getElementById('builtinScreensList');
            
            if (screens.length === 0) {
                builtinList.innerHTML = '<p style="color: #666;">No built-in screens found.</p>';
                return;
            }

            builtinList.innerHTML = screens.map(screen => `
                <div class="screen-item builtin-screen">
                    <div>
                        <h4>${screen.title}</h4>
                        <p>ID: ${screen.screen_id} | Type: ${screen.screen_type} | Duration: ${screen.display_duration}ms</p>
                        ${screen.subtitle ? `<small>Subtitle: ${screen.subtitle}</small>` : ''}
                    </div>
                    <div class="screen-actions">
                        <button class="btn btn-small" onclick="viewScreen('${screen.screen_id}')" title="View in main display">👁️ View</button>
                        <span class="screen-status">Built-in</span>
                    </div>
                </div>
            `).join('');
        }

        // Display custom screens
        function displayCustomScreens(screens) {
            const customList = document.getElementById('customScreensList');

            if (screens.length === 0) {
                customList.innerHTML = '<p style="color: #666;">No custom screens created yet.</p>';
                return;
            }

            customList.innerHTML = screens.map(screen => `
                <div class="screen-item custom-screen">
                    <div>
                        <h4>${screen.title}</h4>
                        <p>ID: ${screen.screen_id} | Type: ${screen.screen_type} | Duration: ${screen.display_duration}ms</p>
                        <p>Created: ${new Date(screen.created_at).toLocaleDateString()}</p>
                        ${screen.subtitle ? `<small>Subtitle: ${screen.subtitle}</small>` : ''}
                    </div>
                    <div class="screen-actions">
                        <button class="btn btn-small" onclick="editScreen('${screen.screen_id}')">✏️ Edit</button>
                        <button class="btn btn-small" onclick="viewScreen('${screen.screen_id}')" title="View in main display">👁️ View</button>
                        <button class="btn btn-danger btn-small" onclick="deleteScreen('${screen.screen_id}')">🗑️ Delete</button>
                    </div>
                </div>
            `).join('');
        }

        // Edit screen
        async function editScreen(screenId) {
            try {
                const response = await fetch(API_BASE + 'get-screens.php');
                const result = await response.json();

                if (result.success) {
                    const allScreens = [...result.data.builtin, ...result.data.custom];
                    const screen = allScreens.find(s => s.screen_id === screenId);

                    if (screen) {
                        document.getElementById('screenId').value = screen.screen_id;
                        document.getElementById('screenTitle').value = screen.title;
                        document.getElementById('screenSubtitle').value = screen.subtitle || '';
                        document.getElementById('screenContent').value = screen.content || '';
                        document.getElementById('screenType').value = screen.screen_type || 'custom';
                        document.getElementById('displayDuration').value = screen.display_duration || 10000;
                        
                        isEditingScreen = true;
                        editingScreenId = screenId;
                        updatePreview();
                        
                        // Scroll to form
                        document.getElementById('screenForm').scrollIntoView({ behavior: 'smooth' });
                        showStatus('Screen loaded for editing', 'success');
                    }
                }
            } catch (error) {
                showStatus('Error loading screen for edit: ' + error.message, 'error');
            }
        }

        // Delete screen
        async function deleteScreen(screenId) {
            if (!confirm('Are you sure you want to delete this screen?')) {
                return;
            }

            try {
                const response = await fetch(API_BASE + 'add-screen.php', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ screen_id: screenId })
                });

                const result = await response.json();

                if (result.success) {
                    showStatus('Screen deleted successfully!', 'success');
                    loadAllScreens();
                } else {
                    showStatus(result.error || 'Failed to delete screen', 'error');
                }
            } catch (error) {
                showStatus('Network error deleting screen: ' + error.message, 'error');
            }
        }

        // View specific screen in main display
        function viewScreen(screenId) {
            try {
                if (window.opener && !window.opener.closed) {
                    if (window.opener.showScreen) {
                        window.opener.showScreen(screenId);
                        showStatus(`Switched main display to screen: ${screenId}`, 'success');
                    } else if (window.opener.screenManager) {
                        window.opener.screenManager.showScreen(screenId);
                        showStatus(`Switched main display to screen: ${screenId}`, 'success');
                    }
                } else {
                    const mainWindow = window.open('../index.php', 'mainDisplay', 'width=1200,height=800');
                    setTimeout(() => {
                        if (mainWindow.screenManager) {
                            mainWindow.screenManager.showScreen(screenId);
                        }
                    }, 2000);
                }
            } catch (error) {
                showStatus('Could not communicate with main display. Please open main display manually.', 'warning');
            }
        }

        // Open main display
        function openMainDisplay() {
            window.open('../index.php', 'mainDisplay', 'width=1200,height=800');
        }

        // Refresh screen list
        function refreshScreens() {
            loadAllScreens();
            showStatus('Screen list refreshed!', 'success');
        }

        // Test database connection
        async function testDatabaseConnection() {
            try {
                const response = await fetch(API_BASE + 'get-screens.php');
                const result = await response.json();

                if (result.success) {
                    showStatus('✅ Database connection successful!', 'success');
                } else {
                    showStatus('❌ Database connection failed: ' + result.error, 'error');
                }
            } catch (error) {
                showStatus('❌ Network error: ' + error.message, 'error');
            }
        }

        // Export screens data
        async function exportScreens() {
            try {
                const response = await fetch(API_BASE + 'get-screens.php');
                const result = await response.json();

                if (result.success) {
                    const dataStr = JSON.stringify(result.data.custom, null, 2);
                    const dataBlob = new Blob([dataStr], {type: 'application/json'});
                    
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(dataBlob);
                    link.download = `custom-screens-backup-${new Date().toISOString().split('T')[0]}.json`;
                    link.click();
                    
                    showStatus('Screens exported successfully!', 'success');
                } else {
                    showStatus('Failed to export screens', 'error');
                }
            } catch (error) {
                showStatus('Export error: ' + error.message, 'error');
            }
        }
    </script>
</body>
</html>