#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path=$1
log=/tmp/debug_apkg
APKG_NAME=$(basename $1)
PIDFILE="/tmp/no-ssh-pwauth.pid"
CONFIG_DIR="/shares/Volume_1/Nas_Prog/${APKG_NAME}/config"

# Kill the periodic enforcement loop immediately
if [ -f "$PIDFILE" ]; then
    LOOP_PID=$(cat "$PIDFILE")
    if kill -0 "$LOOP_PID" 2>/dev/null; then
        # Use SIGKILL to immediately terminate the process
        # (SIGTERM doesn't interrupt sleep, so it can take up to 30s)
        kill -9 "$LOOP_PID" 2>/dev/null
        # Brief delay to let the file system flush any pending writes
        sleep 0.1
        echo "Killed periodic enforcement loop (PID=$LOOP_PID)" >> $log
    fi
    rm -f "$PIDFILE"
fi

# Now safe to modify sshd_config - loop is fully stopped
# Remove config directory
rm -rf "$CONFIG_DIR"

# Remove web symlink
rm -f /var/www/apps/${APKG_NAME}

# Re-enable password authentication
sed -i 's/PasswordAuthentication yes/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords yes/PermitEmptyPasswords yes/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords no/PermitEmptyPasswords yes/g' /etc/ssh/sshd_config

# restart sshd to apply original configuration
if [ -f /var/run/sshd.pid ]; then
    kill -HUP $(cat /var/run/sshd.pid) 2>/dev/null
fi

# remove package directory
rm -rf $path

echo "Addon $APKG_NAME (remove.sh) done" >> $log
