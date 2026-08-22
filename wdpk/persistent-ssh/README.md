For some reason Western Digital devices remove the home directory on every boot, so if the user has
configured a `.ssh` directory correctly (so no password is necessary), such configuration is removed
on a reboot.

This simple app backups the `.ssh` folder, and then restores it. It also allows you to add new allowed_keys via the WebUI.
