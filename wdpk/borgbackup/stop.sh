#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APPDIR=$1

# remove binary links
rm -f /usr/bin/borg
rm -f /usr/bin/borgfs

echo "Addon borgbackup (stop.sh) done" >> /tmp/debug_apkg
