<?php
/*
 * DuckDNS - Configuration Backend
 *
 * Reads/writes the DuckDNS update URL (domain + token) stored in
 * <package>/config/duckdns.conf, which is read by bin/duckdns.sh.
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

// Persistent storage path (same location bin/duckdns.sh reads from)
$APKG_PATH = "/shares/Volume_1/Nas_Prog/duckdns";
$CONFIG_FILE = $APKG_PATH . "/config/duckdns.conf";

// Mask the token when returning the URL to the UI, so it is not
// displayed in plain text. The full URL is still saved on disk.
function mask_token($url)
{
	return preg_replace('/(token=)[a-zA-Z0-9\-]+/', '${1}****', $url);
}

// Read the real token from the existing config file (if any).
function read_stored_token()
{
	if (file_exists($CONFIG_FILE)) {
		$line = file_get_contents($CONFIG_FILE);
		if (preg_match('/token=([a-zA-Z0-9\-]+)/', $line, $m)) {
			return $m[1];
		}
	}
	return "";
}

switch ($action)
{
	case "read":
	{
		$r = new stdClass();
		$r->success = false;

		if (file_exists($CONFIG_FILE)) {
			$line = file_get_contents($CONFIG_FILE);
			$pos = strpos($line, "url=");
			if ($pos !== false) {
				$r->url = substr($line, $pos + 4);
			} else {
				$r->url = "";
			}
			$r->success = true;
		} else {
			$r->url = "";
			$r->success = true;
		}

		// Do not expose the token in the UI
		$r->url = mask_token($r->url);

		echo json_encode($r);
	}
		break;

	case "write":
	{
		$url = isset($_POST['url']) ? trim($_POST['url']) : "";

		// Validate: must be a DuckDNS update URL (or empty to clear)
		$prefix = "https://www.duckdns.org/update?domains=";
		if ($url != "" && strncmp($url, $prefix, strlen($prefix)) != 0) {
			$r->message = "Invalid URL. It must start with " . $prefix;
			echo json_encode($r);
			exit;
		}

		// Validate: the template placeholders must have been replaced
		if (strpos($url, "{domain}") !== false || strpos($url, "{token}") !== false) {
			$r->message = "Please replace {domain} and {token} with your actual values.";
			echo json_encode($r);
			exit;
		}

		// If the user did not change the masked token, keep the
		// previously stored token instead of saving the mask.
		if (strpos($url, "token=****") !== false) {
			$stored_token = read_stored_token();
			if ($stored_token != "") {
				$url = preg_replace('/token=[a-zA-Z0-9\-]*/', 'token=' . $stored_token, $url);
			}
		}

		// Ensure directory exists
		if (!is_dir(dirname($CONFIG_FILE))) {
			mkdir(dirname($CONFIG_FILE), 0755, true);
		}

		if (file_put_contents($CONFIG_FILE, "url=" . $url . "\n") !== false) {
			chmod($CONFIG_FILE, 0600); // contains the token
			$r->success = true;
		} else {
			$r->message = "Failed to write configuration";
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
