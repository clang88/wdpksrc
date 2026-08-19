#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

# remove environment variables from profile
sed -i "\;export TMP=/shares/Volume_1/Nas_Prog/borgbackup/TMP;d" /etc/profile
sed -i "\;export BORG_BASE_DIR=/shares/Volume_1/Nas_Prog/borgbackup/BORG;d" /etc/profile

# remove web 
# TODO: also should be a variable in the future...
rm -rf /var/www/borgbackup

echo "Addon borgbackup (clean.sh) done" >> /tmp/debug_apkg