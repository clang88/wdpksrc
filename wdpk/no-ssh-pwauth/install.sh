#!/bin/sh
# Example: module name is (MNAME) = utelnetd
# default install path: (INST_PATH) = /mnt/HD/HD_a2/Nas_Prog == $2
# default upload path: (UPLOAD_PATH) = /mnt/HD/HD_a2/Nas_Prog/_install/$MNAME == $1

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path_src=$1
path_dst=$2
log=/tmp/debug_apkg

APKG_NAME=$(basename $1)
APKG_PATH="${path_dst}/${APKG_NAME}"

# install all package scripts to the proper location
cp -rf $path_src $path_dst

echo "Addon ${APKG_NAME} (install.sh) done" >> $log