#!/bin/sh

APKG_MODULE=$1
# Define the PID file
PIDFILE="${APKG_MODULE}/duckdns.pid"

# Configuration file with the DuckDNS update URL (domain + token).
# Created and maintained by the web UI (web/config.php).
CONFIG_FILE="${APKG_MODULE}/config/duckdns.conf"

# Check if the PID file exists and if the process is still running
if [ -e $PIDFILE ] && kill -0 $(cat $PIDFILE) 2>/dev/null; then
    echo "Script is already running with PID $(cat $PIDFILE)."
    exit 1
else
    # Save the current PID to the PID file
    echo $$ > $PIDFILE
fi
# Function to clean up the PID file on exit
cleanup() {
	echo "SIGINT or SIGTERM received. Stopping duckdns requests and removing .pid file..."
	rm -f $PIDFILE
	kill -SIGINT $(jobs -p) #Kill all child processes https://stackoverflow.com/a/32049811
	exit 0
}

# Trap signals and call cleanup
trap cleanup SIGINT SIGTERM

# Main loop to send the curl request every 5 minutes.
# The URL is re-read from the config file on every iteration, so
# changes made in the web UI take effect without a restart.
while true; do
    if [ -f "$CONFIG_FILE" ]; then
        URL=$(grep '^url=' "$CONFIG_FILE" | tail -n 1 | cut -d= -f2-)
    else
        URL=""
    fi

    if [ -n "$URL" ]; then
        echo "url=\"$URL\"" | curl -k -o ${APKG_MODULE}/logs/duck.log -K -
    else
        echo "No DuckDNS URL configured. Set it in the web UI (Apps -> DuckDNS)." >> ${APKG_MODULE}/logs/duck.log
    fi

    sleep 300 &
	wait
done

# Clean up the PID file when the script ends
cleanup

