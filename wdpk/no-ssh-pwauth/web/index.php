<!doctype html>
<html>
<!--
SPDX-FileCopyrightText: 2026 clang88

SPDX-License-Identifier: GPL-2.0-or-later
-->
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="PRAGMA" content="no-cache">
<meta http-equiv="Expires" content="-1">
<meta http-equiv="Cache-Control" content="no-cache">
<script type="text/javascript" src="/apps/no-ssh-pwauth/language.js"></script>
<script type="text/javascript">

var NSPA_API_URL = "/apps/no-ssh-pwauth/config.php";
var NSPA_LOADED = false;

function nspa_show_status(msg, type) {
	var cls = (type == "error") ? "error" : "success";
	$("#nspa_status").removeClass("hidden success error").addClass(cls).html(msg);
	if (type == "success") {
		setTimeout(function () {
			$("#nspa_status").addClass("hidden");
		}, 5000);
	}
}

function nspa_load_config() {
	wd_ajax({
		type: "GET",
		url: NSPA_API_URL,
		dataType: "json",
		data: { action: "read" },
		error: function () {
			nspa_show_status(_T_NSPA('_no_ssh_pwauth', 'load_error'), "error");
		},
		success: function (r) {
			if (r.success) {
				// Set toggle states
				if (r.password_auth == "1") {
					$("#nspa_pw_toggle").val("1");
					$("#nspa_pw_toggle").text(_T_NSPA('_no_ssh_pwauth', 'enabled'));
					$("#nspa_pw_toggle").removeClass("btn-off").addClass("btn-on");
				} else {
					$("#nspa_pw_toggle").val("0");
					$("#nspa_pw_toggle").text(_T_NSPA('_no_ssh_pwauth', 'disabled'));
					$("#nspa_pw_toggle").removeClass("btn-on").addClass("btn-off");
				}
				if (r.permit_empty_pw == "1") {
					$("#nspa_empty_toggle").val("1");
					$("#nspa_empty_toggle").text(_T_NSPA('_no_ssh_pwauth', 'enabled'));
					$("#nspa_empty_toggle").removeClass("btn-off").addClass("btn-on");
				} else {
					$("#nspa_empty_toggle").val("0");
					$("#nspa_empty_toggle").text(_T_NSPA('_no_ssh_pwauth', 'disabled'));
					$("#nspa_empty_toggle").removeClass("btn-on").addClass("btn-off");
				}
				$("#nspa_interval").val(r.check_interval || "30");
			} else {
				nspa_show_status(r.message || _T_NSPA('_no_ssh_pwauth', 'load_error'), "error");
			}
		}
	});
}

function nspa_toggle_setting(type) {
	var btnId = (type === 'pw') ? '#nspa_pw_toggle' : '#nspa_empty_toggle';
	var $btn = $(btnId);
	var current = $btn.val();
	var newVal = (current == "1") ? "0" : "1";
	$btn.val(newVal);
	if (newVal == "1") {
		$btn.text(_T_NSPA('_no_ssh_pwauth', 'enabled')).removeClass("btn-off").addClass("btn-on");
	} else {
		$btn.text(_T_NSPA('_no_ssh_pwauth', 'disabled')).removeClass("btn-on").addClass("btn-off");
	}
}

function nspa_save_config() {
	var password_auth = $("#nspa_pw_toggle").val();
	var permit_empty_pw = $("#nspa_empty_toggle").val();
	var check_interval = $("#nspa_interval").val();

	// First apply settings immediately
	$.ajax({
		type: "POST",
		url: NSPA_API_URL,
		dataType: "json",
		data: {
			action: "apply",
			password_auth: password_auth,
			permit_empty_pw: permit_empty_pw
		},
		error: function () {
			nspa_show_status(_T_NSPA('_no_ssh_pwauth', 'apply_error'), "error");
		}
	});

	// Then save config to file
	wd_ajax({
		type: "POST",
		url: NSPA_API_URL,
		dataType: "json",
		data: {
			action: "write",
			password_auth: password_auth,
			permit_empty_pw: permit_empty_pw,
			check_interval: check_interval
		},
		error: function () {
			nspa_show_status(_T_NSPA('_no_ssh_pwauth', 'save_error'), "error");
		},
		success: function (r) {
			if (r.success) {
				nspa_show_status(_T_NSPA('_no_ssh_pwauth', 'saved'), "success");
			} else {
				nspa_show_status(r.message || _T_NSPA('_no_ssh_pwauth', 'save_error'), "error");
			}
		}
	});
}

/* Called by the WD web UI shell after the page content is injected
 * into the content area. */
function page_load() {
	if (NSPA_LOADED) return;
	NSPA_LOADED = true;

	nspa_ready_language();
	nspa_load_config();
}

</script>
<style>
.nspa-toggle-btn {
	padding: 4px 14px;
	border-radius: 3px;
	cursor: pointer;
	font-size: 14px;
	border: 1px solid #ccc;
	background: #f5f5f5;
	color: #333;
	transition: background-color 0.2s, border-color 0.2s;
}
.nspa-toggle-btn.btn-on {
	background: #4CAF50;
	border-color: #4CAF50;
	color: #fff;
}
.nspa-toggle-btn.btn-off {
	background: #f44336;
	border-color: #f44336;
	color: #fff;
}
.nspa-toggle-btn:hover {
	opacity: 0.85;
}
#nspa_interval {
	width: 100px;
	padding: 4px 8px;
	border: 1px solid #ccc;
	border-radius: 3px;
}
</style>
<body>
<div class="h1_content header_2">
	<span class="_text_nspa" lang="_no_ssh_pwauth" datafld="title"></span>
</div>
<div class="field_top">
	<span class="_text_nspa" lang="_no_ssh_pwauth" datafld="desc"></span>
</div>
<div class="hr_0_content">
	<div class="hr_1"></div>
</div>

<!-- Password Authentication toggle -->
<div class="field_top">
	<label class="_text_nspa" lang="_no_ssh_pwauth" datafld="pw_auth_label"></label>
	<button type="button" id="nspa_pw_toggle" class="nspa-toggle-btn btn-off" onclick="nspa_toggle_setting('pw')"></button>
</div>

<!-- Permit Empty Passwords toggle -->
<div class="field_top">
	<label class="_text_nspa" lang="_no_ssh_pwauth" datafld="empty_pw_label"></label>
	<button type="button" id="nspa_empty_toggle" class="nspa-toggle-btn btn-off" onclick="nspa_toggle_setting('empty')"></button>
</div>

<!-- Check Interval -->
<div class="field_top">
	<label for="nspa_interval" class="_text_nspa" lang="_no_ssh_pwauth" datafld="interval_label"></label>
	<input type="number" id="nspa_interval" min="1" max="86400" value="30">
	<span class="_text_nspa" lang="_no_ssh_pwauth" datafld="interval_unit"></span>
</div>

<div class="field_top">
	<button type="button" id="nspa_save" onclick="nspa_save_config()">
		<span class="_text_nspa" lang="_no_ssh_pwauth" datafld="save"></span>
	</button>
</div>

<div id="nspa_status" class="nspa_status hidden"></div>

<script type="text/javascript">
/* Fallback: if the WD shell did not call page_load() for us,
 * initialize when the document is ready. */
$(document).ready(function () {
	if (typeof page_load == "function") page_load();
});
</script>
</body>
</html>
