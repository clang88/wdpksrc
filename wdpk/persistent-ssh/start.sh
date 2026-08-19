#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APPDIR=$1
log=/tmp/persistent-ssh.log

# Ensure .ssh directory exists for root user
mkdir -p /home/root/.ssh
chmod 700 /home/root/.ssh

# Restore authorized_keys from persistent storage if it exists
if [ -f "${APPDIR}/data/authorized_keys" ]; then
    cp ${APPDIR}/data/authorized_keys /home/root/.ssh/authorized_keys
    chmod 600 /home/root/.ssh/authorized_keys
    echo "Restored authorized_keys from persistent storage" >> $log
else
    # Create empty authorized_keys if none exists yet, copy existing authorized_keys to persistent storage and make sure permissions are correct
    touch /home/root/.ssh/authorized_keys
    cat /home/root/.ssh/authorized_keys > ${APPDIR}/data/authorized_keys
    chmod 600 /home/root/.ssh/authorized_keys
    echo "Created empty authorized_keys (no persistent keys found)" >> $log
fi

# Create symlink for web UI
ln -sf ${APPDIR}/web/* /var/www/persistent-ssh

echo "Addon persistent-ssh (start.sh) done" >> $log