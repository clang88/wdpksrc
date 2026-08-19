#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path=$1
log=/tmp/borgbackup.log

echo "INIT linking files from path: $path" >> $log

# create link to binary
ln -sf $1/www/BorgBackup /var/www/
chmod 755 $1/bin/borg

# setup working directories
mkdir -p /shares/Volume_1/Nas_Prog/borgbackup/TMP
mkdir -p /shares/Volume_1/Nas_Prog/borgbackup/BORG

echo "export TMP=/shares/Volume_1/Nas_Prog/borgbackup/TMP" >> /etc/profile
echo "export BORG_BASE_DIR=/shares/Volume_1/Nas_Prog/borgbackup/BORG" >> /etc/profile
