#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

sed -i 's/PasswordAuthentication no/PasswordAuthentication yes/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords no/PermitEmptyPasswords yes/g' /etc/ssh/sshd_config

kill -HUP `cat /var/run/sshd.pid`

APKG_NAME=$(basename $1)
log=/tmp/debug_apkg

# remove web link
rm -rf /var/www/$APKG_NAME

echo "Addon $APKG_NAME (clean.sh) done" >> $log