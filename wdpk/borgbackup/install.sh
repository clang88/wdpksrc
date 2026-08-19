#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path_src=$1
NASPROG=$2

log=/tmp/debug_apkg

APKG_MODULE="borgbackup"
APKG_PATH="${NASPROG}/${APKG_MODULE}"

# install all package scripts to the proper location
cp -rf $path_src $NASPROG

echo "Addon ${APKG_MODULE} (install.sh) done" >> $log

