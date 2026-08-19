#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APPDIR=$1

# create link to binary
ln -sf $APPDIR/bin/borg /usr/bin/borg
ln -sf $APPDIR/bin/borg /usr/bin/borgfs

echo "Addon borgbackup (start.sh) done" >> /tmp/debug_apkg