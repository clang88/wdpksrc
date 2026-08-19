#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path_src=$1
NASPROG=$2

log=/tmp/debug_apkg

APKG_MODULE="persistent-ssh"
APKG_PATH="${NASPROG}/${APKG_MODULE}"

# install all package scripts to the proper location
cp -rf $path_src $NASPROG

# create persistent storage directory for authorized_keys
mkdir -p "${APKG_PATH}/data"

echo "Addon ${APKG_MODULE} (install.sh) done" >> $log