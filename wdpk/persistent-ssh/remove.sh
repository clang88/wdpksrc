#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path=$1
log=/tmp/persistent-ssh.log

# Backup authorized_keys before removal - I don't like that so I disable this logic...
#APKG_BACKUP_DIR="${path}_backup"
#mkdir -p ${APKG_BACKUP_DIR}
#
#if [ -f "${path}/data/authorized_keys" ]; then
#    cp ${path}/data/authorized_keys ${APKG_BACKUP_DIR}/ 2>> $log
#    echo "Backup authorized_keys to ${APKG_BACKUP_DIR}" >> $log
#fi

# Remove the package directory
rm -rf $path

# Remove web symlink
rm -f /var/www/persistent-ssh

echo "Addon persistent-ssh (remove.sh) done" >> $log
