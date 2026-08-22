#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

log=/tmp/persistent-ssh.log

# Remove web symlink
rm -rf /var/www/persistent-ssh

echo "Addon persistent-ssh (clean.sh) done" >> $log