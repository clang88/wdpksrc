#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APPDIR=$1
APKG_NAME=$(basename $1)

# remove binary links
rm -f /usr/bin/borg
rm -f /usr/bin/borgfs

echo "Addon $APKG_NAME (stop.sh) done" >> /tmp/debug_apkg
