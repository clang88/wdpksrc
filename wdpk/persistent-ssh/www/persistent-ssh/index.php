<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Persistent SSH - Manage Authorized Keys</title>
    <link rel="stylesheet" href="/persistent-ssh/style.css">
</head>
<body>
    <div class="container">
        <h1>Persistent SSH</h1>
        <p class="description">Manage your SSH authorized keys. Changes are saved persistently and restored after reboot.</p>
        
        <div class="form-group">
            <label for="authorized_keys">SSH Authorized Keys:</label>
            <textarea id="authorized_keys" rows="12" placeholder="Enter your SSH public keys (one per line)&#10;&#10;Example: ssh-rsa AAAAB3NzaC1yc2EAAA... user@host"></textarea>
        </div>
        
        <div class="actions">
            <button id="apply-btn" onclick="saveKeys()">Apply Changes</button>
            <button id="clear-btn" onclick="clearKeys()" class="secondary">Clear All</button>
        </div>
        
        <div id="status" class="status hidden"></div>
    </div>
    
    <script>
        // Load current keys on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadKeys();
        });
        
        function loadKeys() {
            fetch('/persistent-ssh/authorized_keys.php?action=read')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('authorized_keys').value = data.keys || '';
                    } else {
                        showStatus('Error loading keys: ' + data.message, 'error');
                    }
                })
                .catch(error => {
                    showStatus('Error loading keys: ' + error, 'error');
                });
        }
        
        function saveKeys() {
            const keys = document.getElementById('authorized_keys').value;
            
            fetch('/persistent-ssh/authorized_keys.php?action=write', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ keys: keys })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showStatus('Keys saved and restored successfully!', 'success');
                } else {
                    showStatus('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showStatus('Error saving keys: ' + error, 'error');
            });
        }
        
        function clearKeys() {
            if (confirm('Are you sure you want to remove all SSH keys?')) {
                document.getElementById('authorized_keys').value = '';
                saveKeys();
            }
        }
        
        function showStatus(message, type) {
            const statusEl = document.getElementById('status');
            statusEl.textContent = message;
            statusEl.className = 'status ' + type;
            
            // Auto-hide success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    statusEl.className = 'status hidden';
                }, 5000);
            }
        }
    </script>
</body>
</html>
