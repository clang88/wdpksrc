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
<script type="text/javascript" src="/apps/persistent-ssh/language.js"></script>
<script type="text/javascript">

var PS_API_URL = "/apps/persistent-ssh/authorized_keys.php";
var PS_LOADED = false;

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

/* Called by the WD web UI shell after the page content is injected
 * into the content area. */
function page_load() {
	if (PS_LOADED) return;
	PS_LOADED = true;

	ps_ready_language();

	// Set placeholder text for the textarea
	var placeholder = _T_PS('_persistent_ssh', 'placeholder');
	if (placeholder != "") {
		$("#ps_keys").attr("placeholder", placeholder);
	}

	ps_load_keys();
}

</script>
<body>
<div class="h1_content header_2">
	<span class="_text_ps" lang="_persistent_ssh" datafld="title"></span>
</div>
<div class="field_top">
	<span class="_text_ps" lang="_persistent_ssh" datafld="desc"></span>
</div>
<div class="hr_0_content">
	<div class="hr_1"></div>
</div>
<div class="field_top">
	<label for="ps_keys" class="_text_ps" lang="_persistent_ssh" datafld="label"></label>
	<textarea id="ps_keys" rows="12"></textarea>
</div>
<div class="field_top">
	<button type="button" id="ps_apply" onclick="ps_save_keys()">
		<span class="_text_ps" lang="_persistent_ssh" datafld="apply"></span>
	</button>
	<button type="button" id="ps_clear" onclick="ps_clear_keys()">
		<span class="_text_ps" lang="_persistent_ssh" datafld="clear"></span>
	</button>
</div>
<div id="ps_status" class="ps_status hidden"></div>
<script type="text/javascript">
/* Fallback: if the WD shell did not call page_load() for us,
 * initialize when the document is ready. */
$(document).ready(function () {
	if (typeof page_load == "function") page_load();
});
</script>
<style>
#ps_keys {
	width: 100%;
	padding: 8px;
	border: 1px solid #ccc;
	border-radius: 3px;
	font-family: "Courier New", Courier, monospace;
	font-size: 13px;
	resize: vertical;
}
#ps_apply, #ps_clear {
	margin-right: 8px;
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
