#!/bin/sh
# Example: module name is (MNAME) = utelnetd
# default install path: (INST_PATH) = /mnt/HD/HD_a2/Nas_Prog/$MNAME == $2
# default upload path: (UPLOAD_PATH) = /mnt/HD/HD_a2/Nas_Prog/_install == $1

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path_src=$1
path_dst=$2

log=/tmp/debug_apkg

APKG_NAME="$(basename $path_src)"
APKG_PATH="${path_dst}/${APKG_NAME}"

mv $path_src $path_dst
mkdir -p $path_dst/$APKG_NAME/logs
mkdir -p $path_dst/$APKG_NAME/config

echo "Addon ${APKG_NAME} (install.sh) done" >> $log