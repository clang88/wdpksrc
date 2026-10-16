#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKG_NAME=$(basename $1)
log=/tmp/debug_apkg
PIDFILE="/tmp/no-ssh-pwauth.pid"

# Kill the periodic enforcement loop immediately
if [ -f "$PIDFILE" ]; then
    LOOP_PID=$(cat "$PIDFILE")
    if kill -0 "$LOOP_PID" 2>/dev/null; then
        # Use SIGKILL to immediately terminate the process
        # (SIGTERM doesn't interrupt sleep, so it can take up to 30s)
        kill -9 "$LOOP_PID" 2>/dev/null
        # Brief delay to let the file system flush any pending writes
        # to sshd_config before we modify it ourselves
        sleep 0.1
        echo "Killed periodic enforcement loop (PID=$LOOP_PID)" >> $log
    fi
    rm -f "$PIDFILE"
fi

# Now safe to modify sshd_config - loop is fully stopped
# Re-enable password authentication but keep empty passwords disabled
sed -i 's/PasswordAuthentication yes/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords yes/PermitEmptyPasswords no/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords no/PermitEmptyPasswords no/g' /etc/ssh/sshd_config

# reload sshd
if [ -f /var/run/sshd.pid ]; then
    kill -HUP $(cat /var/run/sshd.pid) 2>/dev/null
fi

echo "Addon $APKG_NAME (stop.sh) done" >> $log