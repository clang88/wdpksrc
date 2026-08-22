#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path_src=$1
path_dst=$2

log=/tmp/debug_apkg

APKG_NAME="$(basename $path_src)"
APKG_PATH="${path_dst}/${APKG_NAME}"

# install all package scripts to the proper location
cp -rf $path_src $path_dst

# create persistent storage directory for authorized_keys
mkdir -p "${APKG_PATH}/data"

echo "Addon ${APKG_NAME} (install.sh) done" >> $log