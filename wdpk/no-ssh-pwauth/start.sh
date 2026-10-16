#!/bin/sh

[ -f /tmp/debug_apkg ] && echo "APKG_DEBUG: $0 $@" >> /tmp/debug_apkg

APKGDIR=$(readlink -f $1)
APKG_NAME=$(basename $1)
log=/tmp/debug_apkg
PIDFILE="/tmp/no-ssh-pwauth.pid"
CONFIG_FILE="/shares/Volume_1/Nas_Prog/${APKG_NAME}/config/no-ssh-pwauth.conf"

function log_msg {
    TIME=$(date '+%Y-%m-%d %H:%M:%S')
    echo "$TIME [no-ssh-pwauth] [$(basename $0)] $1" >> $log
}

# Read a config value from the config file
# Usage: cfg_get <key> [default]
cfg_get() {
    local key="$1"
    local default="$2"
    local val=""
    if [ -f "$CONFIG_FILE" ]; then
        val=$(grep "^${key}=" "$CONFIG_FILE" 2>/dev/null | cut -d= -f2 | tr -d '[:space:]')
    fi
    if [ -z "$val" ]; then
        val="$default"
    fi
    echo "$val"
}

# Read current config values from file (for periodic enforcement)
cfg_read_current() {
    local key="$1"
    local default="$2"
    local val=""
    if [ -f "$CONFIG_FILE" ]; then
        val=$(grep "^${key}=" "$CONFIG_FILE" 2>/dev/null | cut -d= -f2 | tr -d '[:space:]')
    fi
    if [ -z "$val" ]; then
        val="$default"
    fi
    echo "$val"
}

# Apply sshd settings based on config
apply_sshd_settings() {
    local password_auth="$1"
    local permit_empty_pw="$2"

    # Function to set a directive in sshd_config
    # Handles: uncommented existing line, commented line, or missing line
    set_sshd_directive() {
        local directive="$1"
        local value="$2"
        local config_file="/etc/ssh/sshd_config"

        if grep -q "^${directive} " "$config_file" 2>/dev/null; then
            # Already set uncommented - update value
            sed -i "s/^${directive} .*/${directive} ${value}/" "$config_file"
        elif grep -q "^# *${directive} " "$config_file" 2>/dev/null; then
            # Commented out - uncomment and set value
            sed -i "s/^# *${directive} .*/${directive} ${value}/" "$config_file"
        else
            # Not found - append
            echo "${directive} ${value}" >> "$config_file"
        fi
    }

    if [ "$password_auth" = "1" ]; then
        set_sshd_directive "PasswordAuthentication" "yes"
    else
        set_sshd_directive "PasswordAuthentication" "no"
    fi

    if [ "$permit_empty_pw" = "1" ]; then
        set_sshd_directive "PermitEmptyPasswords" "yes"
    else
        set_sshd_directive "PermitEmptyPasswords" "no"
    fi

    # Reload sshd if running
    if [ -f /var/run/sshd.pid ]; then
        kill -HUP $(cat /var/run/sshd.pid) 2>/dev/null
    fi

    log_msg "Applied: PasswordAuthentication=${password_auth}, PermitEmptyPasswords=${permit_empty_pw}"
}

# Read initial configuration
PASSWORD_AUTH=$(cfg_get "password_auth" "0")
PERMIT_EMPTY_PW=$(cfg_get "permit_empty_pw" "0")
CHECK_INTERVAL=$(cfg_get "check_interval" "30")

# Validate check_interval is a positive integer
case "$CHECK_INTERVAL" in
    ''|*[!0-9]*) CHECK_INTERVAL=30 ;;
esac
if [ "$CHECK_INTERVAL" -lt 1 ] 2>/dev/null; then
    CHECK_INTERVAL=30
fi

log_msg "Config: password_auth=${PASSWORD_AUTH}, permit_empty_pw=${PERMIT_EMPTY_PW}, check_interval=${CHECK_INTERVAL}"

# Apply settings immediately
apply_sshd_settings "$PASSWORD_AUTH" "$PERMIT_EMPTY_PW"

# Start periodic enforcement loop
cleanup() {
    log_msg "Stopping periodic enforcement loop"
    rm -f "$PIDFILE"
    # Restore settings before exiting
    apply_sshd_settings "1" "1"
    exit 0
}

trap cleanup SIGTERM SIGINT

# Write our PID file
echo $$ > "$PIDFILE"
log_msg "Periodic enforcement started (PID=$$, interval=${CHECK_INTERVAL}s)"

while true; do
    sleep "$CHECK_INTERVAL"
    # Re-read config each iteration to pick up UI changes
    PASSWORD_AUTH=$(cfg_read_current "password_auth" "0")
    PERMIT_EMPTY_PW=$(cfg_read_current "permit_empty_pw" "0")
    CHECK_INTERVAL=$(cfg_read_current "check_interval" "30")
    # Validate check_interval
    case "$CHECK_INTERVAL" in
        ''|*[!0-9]*) CHECK_INTERVAL=30 ;;
    esac
    if [ "$CHECK_INTERVAL" -lt 1 ] 2>/dev/null; then
        CHECK_INTERVAL=30
    fi
    apply_sshd_settings "$PASSWORD_AUTH" "$PERMIT_EMPTY_PW"
done