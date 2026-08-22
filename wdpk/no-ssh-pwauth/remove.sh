#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path=$1
log=/tmp/debug_apkg
APKG_NAME=$(basename $1)

# restore original sshd_config
if [ -f $1/backup/sshd_config ]; then
    cp -a $1/backup/sshd_config /etc/ssh/sshd_config
fi

# remove web link
rm -rf /var/www/$APKG_NAME

# remove package directory
rm -rf $path

# restart sshd to apply original configuration
if [ -f /var/run/sshd.pid ]; then
    kill -HUP $(cat /var/run/sshd.pid)
fi

echo "Addon $APKG_NAME (remove.sh) done" >> $log
