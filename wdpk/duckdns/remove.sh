#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg
#$1 = Install_path

APKG_PATH=$1

APKG_NAME="$(basename $APKG_PATH)"

rm -rf /var/www/apps/duckdns 
rm -f /usr/bin/duckdns > /dev/null
rm -rf $APKG_PATH
