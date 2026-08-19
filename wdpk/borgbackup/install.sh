#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path_src=$1
path_dst=$2

log=/tmp/debug_apkg

APKG_MODULE="borgbackup"
APKG_PATH="${path_dst}/${APKG_MODULE}"

# install all package scripts to the proper location
cp -rf $path_src $path_dst

echo "Addon ${APKG_MODULE} (install.sh) done" >> $log

