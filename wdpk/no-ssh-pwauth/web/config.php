<?php
/*
 * no-ssh-pwauth - Configuration Backend
 *
 * Reads/writes the SSH password auth settings stored in
 * <package>/config/no-ssh-pwauth.conf.
 *
 * SPDX-FileCopyrightText: 2026 clang88
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
session_start();

$r = new stdClass();
$r->success = false;

include ($_SERVER['DOCUMENT_ROOT']."/web/lib/login_checker.php");

if (login_check() != 1)
{
	$r->message = "Authentication required";
	echo json_encode($r);
	exit;
}

$action = $_POST['action'];
if ($action == "")	$action = $_GET['action'];

// Persistent storage path
$APKG_PATH = "/shares/Volume_1/Nas_Prog/no-ssh-pwauth";
$CONFIG_FILE = $APKG_PATH . "/config/no-ssh-pwauth.conf";

// Default values
$DEFAULTS = array(
	'password_auth'    => '0',
	'permit_empty_pw'  => '0',
	'check_interval'   => '30'
);

/**
 * Read a config value from the file.
 * Returns the value if found, or the default.
 */
function cfg_read($key)
{
	global $CONFIG_FILE;
	if (!file_exists($CONFIG_FILE)) {
		return $DEFAULTS[$key];
	}
	$contents = file_get_contents($CONFIG_FILE);
	$lines = explode("\n", $contents);
	foreach ($lines as $line) {
		$line = trim($line);
		if (strpos($line, '#') === 0) continue; // skip comments
		if (strpos($line, $key.'=') === 0) {
			return substr($line, strlen($key) + 1);
		}
	}
	return $DEFAULTS[$key];
}

/**
 * Write a config value to the file.
 * Updates existing key or appends new key.
 */
function cfg_write($key, $value)
{
	global $CONFIG_FILE;
	$contents = file_get_contents($CONFIG_FILE);
	$lines = explode("\n", $contents);
	$new_lines = array();
	$key_found = false;
	foreach ($lines as $line) {
		$trimmed = trim($line);
		if (strpos($trimmed, $key.'=') === 0) {
			$new_lines[] = $key.'='.$value;
			$key_found = true;
		} else {
			$new_lines[] = $line;
		}
	}
	if (!$key_found) {
		$new_lines[] = $key.'='.$value;
	}
	file_put_contents($CONFIG_FILE, implode("\n", $new_lines));
}

/**
 * Set a directive in sshd_config.
 * Handles: uncommented existing line, commented line, or missing line.
 */
function set_sshd_directive($config_file, $directive, $value)
{
	$found = false;
	$contents = file_get_contents($config_file);
	$lines = explode("\n", $contents);
	$new_lines = array();
	foreach ($lines as $line) {
		$trimmed = trim($line);
		if (preg_match('/^' . preg_quote($directive, '/') . '\s/', $trimmed)) {
			// Already set uncommented - replace value
			$new_lines[] = $directive . ' ' . $value;
			$found = true;
		} elseif (preg_match('/^#\s*' . preg_quote($directive, '/') . '\s/', $trimmed)) {
			// Commented out - uncomment and set value
			$new_lines[] = $directive . ' ' . $value;
			$found = true;
		} else {
			$new_lines[] = $line;
		}
	}
	if (!$found) {
		// Not found - append
		$new_lines[] = $directive . ' ' . $value;
	}
	file_put_contents($config_file, implode("\n", $new_lines));
}

switch ($action)
{
	case "read":
	{
		$r = new stdClass();
		$r->success = true;
		$r->password_auth    = cfg_read('password_auth');
		$r->permit_empty_pw  = cfg_read('permit_empty_pw');
		$r->check_interval   = cfg_read('check_interval');
		echo json_encode($r);
	}
		break;

	case "write":
	{
		$password_auth    = isset($_POST['password_auth'])    ? $_POST['password_auth']    : '0';
		$permit_empty_pw  = isset($_POST['permit_empty_pw'])  ? $_POST['permit_empty_pw']  : '0';
		$check_interval   = isset($_POST['check_interval'])   ? $_POST['check_interval']   : '30';

		// Validate values
		$password_auth    = ($password_auth == '1') ? '1' : '0';
		$permit_empty_pw  = ($permit_empty_pw == '1') ? '1' : '0';
		$check_interval   = intval($check_interval);
		if ($check_interval < 1) $check_interval = 1;
		if ($check_interval > 86400) $check_interval = 86400;

		// Ensure config directory exists
		if (!is_dir(dirname($CONFIG_FILE))) {
			mkdir(dirname($CONFIG_FILE), 0755, true);
		}

		cfg_write('password_auth', $password_auth);
		cfg_write('permit_empty_pw', $permit_empty_pw);
		cfg_write('check_interval', $check_interval);

		$r->success = true;
		echo json_encode($r);
	}
		break;

	case "apply":
	{
		$password_auth    = isset($_POST['password_auth'])    ? $_POST['password_auth']    : '0';
		$permit_empty_pw  = isset($_POST['permit_empty_pw'])  ? $_POST['permit_empty_pw']  : '0';

		// Validate values
		$password_auth    = ($password_auth == '1') ? '1' : '0';
		$permit_empty_pw  = ($permit_empty_pw == '1') ? '1' : '0';

		$sshd_config = '/etc/ssh/sshd_config';

		// Function to set a directive in sshd_config
		set_sshd_directive($sshd_config, 'PasswordAuthentication', ($password_auth == '1') ? 'yes' : 'no');
		set_sshd_directive($sshd_config, 'PermitEmptyPasswords', ($permit_empty_pw == '1') ? 'yes' : 'no');

		// Reload sshd if running
		if (file_exists('/var/run/sshd.pid')) {
			exec('kill -HUP ' . escapeshellarg(file_get_contents('/var/run/sshd.pid')) . ' 2>/dev/null');
		}

		$r->success = true;
		echo json_encode($r);
	}
		break;

	default:
	{
		$r->message = "Invalid action";
		echo json_encode($r);
	}
		break;
}
?>
