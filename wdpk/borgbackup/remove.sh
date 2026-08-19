#!/bin/sh

#$1 = Install_path

#cp -a $1/backup/BorgBackup /some/path

rm -rf /var/www/BorgBackup 
rm -f /usr/bin/borg > /dev/null
rm -f /usr/bin/borgfs > /dev/null
rm -rf $1
