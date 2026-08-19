#!/bin/sh

#$1 = Install_path

rm -rf /var/www/duckdns 
rm -rf /var/www/apps/duckdns 
rm -f /usr/bin/duckdns > /dev/null
rm -rf $1
