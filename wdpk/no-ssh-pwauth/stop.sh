#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKG_NAME=$(basename $1)
log=/tmp/debug_apkg

# re-enable password authentication
sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords no/PermitEmptyPasswords yes/g' /etc/ssh/sshd_config

# reload sshd
if [ -f /var/run/sshd.pid ]; then
    kill -HUP $(cat /var/run/sshd.pid)
fi

echo "Addon $APKG_NAME (stop.sh) done" >> $log