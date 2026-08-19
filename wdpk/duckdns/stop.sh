#!/bin/sh

kill $(cat ~/duckdns.pid)
rm -f /usr/bin/duckdns
