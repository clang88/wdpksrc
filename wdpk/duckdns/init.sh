#!/bin/sh

# link web UI (WD convention: /var/www/apps/<module>)
WEBPATH="/var/www/apps/duckdns"
mkdir -p $WEBPATH
ln -sf $1/web/* $WEBPATH

chmod 755 $1/bin/duckdns