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
</head>
<body>
<script type="text/javascript" src="/apps/persistent-ssh/language.js"></script>
<script type="text/javascript">

var PS_API_URL = "/apps/persistent-ssh/authorized_keys.php";

function ps_show_status(msg, type) {
	var cls = (type == "error") ? "error" : "success";
	$("#ps_status").removeClass("hidden success error").addClass(cls).html(msg);
	if (type == "success") {
		setTimeout(function () {
			$("#ps_status").addClass("hidden");
		}, 5000);
	}
}

function ps_load_keys() {
	wd_ajax({
		type: "GET",
		url: PS_API_URL,
		dataType: "json",
		data: { action: "read" },
		error: function () {
			ps_show_status(_T_PS('_persistent_ssh', 'load_error'), "error");
		},
		success: function (r) {
			if (r.success) {
				$("#ps_keys").val(r.keys || "");
			} else {
				ps_show_status(r.message || _T_PS('_persistent_ssh', 'load_error'), "error");
			}
		}
	});
}

function ps_save_keys() {
	var keys = $("#ps_keys").val();
	wd_ajax({
		type: "POST",
		url: PS_API_URL,
		dataType: "json",
		data: { action: "write", keys: keys },
		error: function () {
			ps_show_status(_T_PS('_persistent_ssh', 'save_error'), "error");
		},
		success: function (r) {
			if (r.success) {
				ps_show_status(_T_PS('_persistent_ssh', 'saved'), "success");
			} else {
				ps_show_status(r.message || _T_PS('_persistent_ssh', 'save_error'), "error");
			}
		}
	});
}

function ps_clear_keys() {
	if (confirm(_T_PS('_persistent_ssh', 'confirm_clear'))) {
		$("#ps_keys").val("");
		ps_save_keys();
	}
}

$(document).ready(function () {
	ps_ready_language();

	var html = "";
	html += "<div class=\"ps_container\">";
	html += "<h2 class=\"_text_ps\" lang=\"_persistent_ssh\" datafld=\"title\"></h2>";
	html += "<p class=\"ps_desc _text_ps\" lang=\"_persistent_ssh\" datafld=\"desc\"></p>";
	html += "<div class=\"ps_form_group\">";
	html += "<label for=\"ps_keys\" class=\"_text_ps\" lang=\"_persistent_ssh\" datafld=\"label\"></label>";
	html += "<textarea id=\"ps_keys\" rows=\"12\"></textarea>";
	html += "</div>";
	html += "<div class=\"ps_actions\">";
	html += "<button id=\"ps_apply\" class=\"btn\" onclick=\"ps_save_keys()\"><span class=\"_text_ps\" lang=\"_persistent_ssh\" datafld=\"apply\"></span></button>";
	html += "<button id=\"ps_clear\" class=\"btn btn_secondary\" onclick=\"ps_clear_keys()\"><span class=\"_text_ps\" lang=\"_persistent_ssh\" datafld=\"clear\"></span></button>";
	html += "</div>";
	html += "<div id=\"ps_status\" class=\"ps_status hidden\"></div>";
	html += "</div>";

	$("body").append(html);
	ps_language();

	// Set placeholder text for the textarea
	var placeholder = _T_PS('_persistent_ssh', 'placeholder');
	if (placeholder != "") {
		$("#ps_keys").attr("placeholder", placeholder);
	}

	ps_load_keys();
});

</script>
<style>
.ps_container {
	padding: 10px 20px;
}
.ps_container h2 {
	margin-bottom: 8px;
}
.ps_desc {
	margin-bottom: 16px;
	color: #666;
}
.ps_form_group label {
	display: block;
	margin-bottom: 6px;
	font-weight: bold;
}
.ps_form_group textarea {
	width: 100%;
	padding: 8px;
	border: 1px solid #ccc;
	border-radius: 3px;
	font-family: "Courier New", Courier, monospace;
	font-size: 13px;
	resize: vertical;
}
.ps_actions {
	margin-top: 12px;
}
.ps_actions button {
	margin-right: 8px;
	padding: 6px 16px;
	cursor: pointer;
}
.ps_status {
	margin-top: 12px;
	padding: 8px 12px;
	border-radius: 3px;
}
.ps_status.success {
	background-color: #d4edda;
	color: #155724;
	border: 1px solid #c3e6cb;
}
.ps_status.error {
	background-color: #f8d7da;
	color: #721c24;
	border: 1px solid #f5c6cb;
}
.ps_status.hidden {
	display: none;
}
</style>
</body>
</html>
