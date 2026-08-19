#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKG_PATH=$1

APKG_NAME="$(basename $APKG_PATH)"

# link web UI (WD convention: /var/www/apps/<module>)
WEBPATH="/var/www/apps/duckdns"
mkdir -p $WEBPATH
ln -sf $APKG_PATH/web/* $WEBPATH

chmod 755 $APKG_PATH/bin/duckdns.sh