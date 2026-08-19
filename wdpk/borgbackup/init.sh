#!/bin/sh

ln -s $1/www/BorgBackup /var/www/
chmod 755 $1/bin/borg
mount -o remount,exec /tmp
echo "export TMP=/mnt/HD/HD_a2/christian/_myapps/TMP" >> /etc/profile
echo "export BORG_BASE_DIR=/mnt/HD/HD_a2/christian/_myapps/BORG" >> /etc/profile
