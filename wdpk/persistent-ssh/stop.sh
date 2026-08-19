#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APPDIR=$1
log=/tmp/persistent-ssh.log

# Remove web symlink
rm -f /var/www/persistent-ssh

echo "Addon persistent-ssh (stop.sh) done" >> $log