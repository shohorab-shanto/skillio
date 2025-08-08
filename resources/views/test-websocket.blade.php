<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Connection Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div id="connection-status" style="padding: 20px; font-family: Arial;">
        <h2>WebSocket Connection Test</h2>
        <div id="status">Connecting...</div>
        <div id="logs" style="margin-top: 20px; background: #f5f5f5; padding: 10px; border-radius: 5px;">
            <h4>Connection Logs:</h4>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusDiv = document.getElementById('status');
            const logsDiv = document.getElementById('logs');
            
            function addLog(message) {
                const logEntry = document.createElement('div');
                logEntry.textContent = new Date().toLocaleTimeString() + ': ' + message;
                logsDiv.appendChild(logEntry);
            }
            
            try {
                addLog('Initializing Echo...');
                
                // Test Echo connection
                Echo.connector.pusher.connection.bind('connected', function() {
                    statusDiv.innerHTML = '<span style="color: green;">✅ WebSocket Connected Successfully!</span>';
                    addLog('WebSocket connection established');
                });
                
                Echo.connector.pusher.connection.bind('disconnected', function() {
                    statusDiv.innerHTML = '<span style="color: red;">❌ WebSocket Disconnected</span>';
                    addLog('WebSocket connection lost');
                });
                
                Echo.connector.pusher.connection.bind('error', function(error) {
                    statusDiv.innerHTML = '<span style="color: red;">❌ WebSocket Error</span>';
                    addLog('WebSocket error: ' + JSON.stringify(error));
                });
                
                addLog('Echo initialized successfully');
                
            } catch (error) {
                statusDiv.innerHTML = '<span style="color: red;">❌ Failed to Initialize Echo</span>';
                addLog('Echo initialization error: ' + error.message);
            }
        });
    </script>
</body>
</html>
