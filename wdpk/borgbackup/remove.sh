#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

path=$1
log=/tmp/borgbackup.log
APKG_NAME=$(basename $1)

# create a backup of the borg data/config
#APKG_BACKUP_DIR="/shares/Volume_1/Nas_Prog/borgbackup_backup"
#mkdir -p ${APKG_BACKUP_DIR}
#
#if [ -d "/shares/Volume_1/Nas_Prog/borgbackup/BORG" ]; then
#    cp -a /shares/Volume_1/Nas_Prog/borgbackup/BORG ${APKG_BACKUP_DIR}/ 2>> $log
#    echo "Backup borg data to ${APKG_BACKUP_DIR}" >> $log
#fi

# remove the package directory
rm -rf $path

# remove bin links
rm -f /usr/bin/borg > /dev/null
rm -f /usr/bin/borgfs > /dev/null

# remove web
rm -rf /var/www/$APKG_NAME

echo "Addon borgbackup (remove.sh) done" >> $log
