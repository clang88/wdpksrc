#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKGDIR=$(readlink -f $1)
APKG_NAME=$(basename $1)
log=/tmp/debug_apkg

echo "INIT deploying files from path: $APKGDIR" >> $log

# Deploy web UI (WD convention for CenterType=1: /var/www/apps/<module>)
WEBPATH="/var/www/apps/${APKG_NAME}"
mkdir -p "$WEBPATH"
ln -sf "$APKGDIR/web"/* "$WEBPATH" >> $log 2>&1

# Create persistent storage directory for config
CONFIG_DIR="/shares/Volume_1/Nas_Prog/${APKG_NAME}/config"
mkdir -p "$CONFIG_DIR"

# Copy default config if not present
if [ ! -f "$CONFIG_DIR/no-ssh-pwauth.conf" ]; then
    cp -a "$APKGDIR/config/no-ssh-pwauth.conf" "$CONFIG_DIR/no-ssh-pwauth.conf"
    echo "Copied default config to $CONFIG_DIR/no-ssh-pwauth.conf" >> $log
fi

echo "Addon $APKG_NAME (init.sh) done" >> $log
