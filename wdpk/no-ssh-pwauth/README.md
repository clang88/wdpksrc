By default, password-authentication via SSH is enabled. 
Run this app after enabling ssh, to disable access via password. Requires a client-server key-pair for ssh-access.
The app simply replaces the 'PasswordAuthentication yes' line in /etc/ssh/sshd_config with 'PasswordAuthentication no'

Stop (or uninstal) the app, to reenable password-authenticated SSH-access by reverting said change.