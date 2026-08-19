#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKG_PATH=$1

APKG_NAME="$(basename $APKG_PATH)"

kill $(cat $APKG_PATH/duckdns.pid)
rm -f /usr/bin/duckdns.sh
