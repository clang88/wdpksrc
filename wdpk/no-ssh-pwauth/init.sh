#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKGDIR=$(readlink -f $1)
APKG_NAME=$(basename $1)
log=/tmp/debug_apkg

echo "INIT linking files from path: $APKGDIR" >> $log

# create link to web
WEBPATH="/var/www/$APKG_NAME"
ln -sf $APKGDIR/web $WEBPATH

# backup original sshd_config
cp -a /etc/ssh/sshd_config $APKGDIR/backup

# disable password authentication
sed -i 's/PasswordAuthentication yes/PasswordAuthentication no/g' /etc/ssh/sshd_config
sed -i 's/PermitEmptyPasswords yes/PermitEmptyPasswords no/g' /etc/ssh/sshd_config

# reload sshd
if [ -f /var/run/sshd.pid ]; then
    kill -HUP $(cat /var/run/sshd.pid)
fi

echo "Addon $APKG_NAME (init.sh) done" >> $log
