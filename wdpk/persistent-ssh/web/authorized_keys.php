<?php
/*
 * Persistent SSH - Authorized Keys Management Backend
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

// Determine the persistent storage path
$APKG_PATH = "/shares/Volume_1/Nas_Prog/persistent-ssh";
$AUTHORIZED_KEYS_FILE = $APKG_PATH . "/data/authorized_keys";

$SSH_DIR = "/home/root/.ssh";

switch ($action)
{
	case "read":
	{
		$r = new stdClass();
		$r->success = false;

		if (file_exists($AUTHORIZED_KEYS_FILE)) {
			$r->keys = file_get_contents($AUTHORIZED_KEYS_FILE);
			$r->success = true;
		} else {
			$r->keys = "";
			$r->success = true;
		}

		echo json_encode($r);
	}
		break;

	case "write":
	{
		$keys = isset($_POST['keys']) ? $_POST['keys'] : "";

		// Ensure directory exists
		if (!is_dir(dirname($AUTHORIZED_KEYS_FILE))) {
			mkdir(dirname($AUTHORIZED_KEYS_FILE), 0755, true);
		}

		// Write keys to persistent storage
		if (file_put_contents($AUTHORIZED_KEYS_FILE, $keys) !== false) {
			// Also restore to /home/root/.ssh/authorized_keys immediately
			if (!is_dir($SSH_DIR)) {
				mkdir($SSH_DIR, 0700, true);
			}

			file_put_contents($SSH_DIR . "/authorized_keys", $keys);
			chmod($SSH_DIR . "/authorized_keys", 0600);

			$r->success = true;
		} else {
			$r->message = "Failed to write keys";
		}

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
