#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKGDIR=$(readlink -f $1)
log=/tmp/borgbackup.log

echo "INIT linking files from path: $APKGDIR" >> $log

# Needs to have the same name as package... 
# TODO: Maybe this can be a variable like APKG_MODULE?
WEBPATH="/var/www/borgbackup"

# create link to binary
ln -sf $APKGDIR/web $WEBPATH
chmod 755 $APKGDIR/bin/borg

# setup working directories
mkdir -p /shares/Volume_1/Nas_Prog/borgbackup/TMP
mkdir -p /shares/Volume_1/Nas_Prog/borgbackup/BORG

echo "export TMP=/shares/Volume_1/Nas_Prog/borgbackup/TMP" >> /etc/profile
echo "export BORG_BASE_DIR=/shares/Volume_1/Nas_Prog/borgbackup/BORG" >> /etc/profile
