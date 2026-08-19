#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path=$1
log=/tmp/persistent-ssh.log

echo "INIT linking files from path: $path" >> $log

# create persistent storage directory for authorized_keys
mkdir -p ${path}/data

# link web UI
WEBPATH="/var/www/persistent-ssh"
mkdir -p $WEBPATH
ln -sf $path/web/* $WEBPATH >> $log 2>&1

echo "Addon persistent-ssh (init.sh) done" >> $log
