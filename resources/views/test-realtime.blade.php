<!DOCTYPE html>
<html>
<head>
    <title>Real-time Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div style="padding: 20px; font-family: Arial;">
        <h2>Real-time Messaging Test</h2>
        <div id="status">Initializing...</div>
        <div id="messages" style="margin-top: 20px; background: #f5f5f5; padding: 10px; border-radius: 5px; min-height: 100px;">
            <h4>Messages:</h4>
        </div>
        <div style="margin-top: 20px;">
            <input type="text" id="test-message" placeholder="Type test message..." style="padding: 8px; width: 300px;">
            <button onclick="sendTestMessage()" style="padding: 8px 16px; margin-left: 10px;">Send Test</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusDiv = document.getElementById('status');
            const messagesDiv = document.getElementById('messages');
            
            function addMessage(text) {
                const messageEl = document.createElement('div');
                messageEl.textContent = new Date().toLocaleTimeString() + ': ' + text;
                messagesDiv.appendChild(messageEl);
            }
            
            if (typeof window.Echo != 'undefined') {
                statusDiv.textContent = 'Echo loaded successfully!';
                addMessage('Echo initialized');
                
                // Test Echo connection
                window.Echo.connector.pusher.connection.bind('connected', function() {
                    statusDiv.textContent = 'WebSocket Connected!';
                    addMessage('WebSocket connection established');
                });
                
                window.Echo.connector.pusher.connection.bind('error', function(error) {
                    statusDiv.textContent = 'WebSocket Error: ' + JSON.stringify(error);
                    addMessage('WebSocket error: ' + JSON.stringify(error));
                });
                
            } else {
                statusDiv.textContent = 'Echo not available!';
                addMessage('Echo not loaded');
            }
        });
        
        function sendTestMessage() {
            const input = document.getElementById('test-message');
            const message = input.value.trim();
            if (message) {
                fetch('/test-broadcast', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ message: message })
                }).then(response => {
                    if (response.ok) {
                        input.value = '';
                        document.getElementById('messages').lastElementChild.textContent += ' (sent)';
                    }
                });
            }
        }
    </script>
</body>
</html>
