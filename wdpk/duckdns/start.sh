#!/bin/sh
[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKG_PATH=$1

log=/tmp/debug_apkg

ln -s $APKG_PATH/bin/duckdns.sh /usr/bin/duckdns.sh
chmod +x /usr/bin/duckdns.sh


duckdns.sh $APKG_PATH

echo "DuckDNS service started." >> $log