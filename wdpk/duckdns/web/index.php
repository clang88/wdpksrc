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
<script type="text/javascript" src="/apps/duckdns/language.js"></script>
<script type="text/javascript">

var DD_API_URL = "/apps/duckdns/config.php";
var DD_LOADED = false;

function dd_show_status(msg, type) {
	var cls = (type == "error") ? "error" : "success";
	$("#dd_status").removeClass("hidden success error").addClass(cls).html(msg);
	if (type == "success") {
		setTimeout(function () {
			$("#dd_status").addClass("hidden");
		}, 5000);
	}
}

function dd_load_config() {
	wd_ajax({
		type: "GET",
		url: DD_API_URL,
		dataType: "json",
		data: { action: "read" },
		error: function () {
			dd_show_status(_T_DD('_duckdns', 'load_error'), "error");
		},
		success: function (r) {
			if (r.success) {
				$("#dd_url").val(r.url || "");
			} else {
				dd_show_status(r.message || _T_DD('_duckdns', 'load_error'), "error");
			}
		}
	});
}

function dd_save_config() {
	var url = $("#dd_url").val();

	// Basic client-side validation
	if (url != "" && url.indexOf("https://www.duckdns.org/update?domains=") != 0) {
		dd_show_status(_T_DD('_duckdns', 'invalid_url'), "error");
		return;
	}

	wd_ajax({
		type: "POST",
		url: DD_API_URL,
		dataType: "json",
		data: { action: "write", url: url },
		error: function () {
			dd_show_status(_T_DD('_duckdns', 'save_error'), "error");
		},
		success: function (r) {
			if (r.success) {
				dd_show_status(_T_DD('_duckdns', 'saved'), "success");
			} else {
				dd_show_status(r.message || _T_DD('_duckdns', 'save_error'), "error");
			}
		}
	});
}

function dd_clear_config() {
	if (confirm(_T_DD('_duckdns', 'confirm_clear'))) {
		$("#dd_url").val("");
		dd_save_config();
	}
}

/* Called by the WD web UI shell after the page content is injected
 * into the content area. */
function page_load() {
	if (DD_LOADED) return;
	DD_LOADED = true;

	dd_ready_language();

	// Set placeholder text for the URL field
	var placeholder = _T_DD('_duckdns', 'placeholder');
	if (placeholder != "") {
		$("#dd_url").attr("placeholder", placeholder);
	}

	dd_load_config();
}

</script>
<body>
<div class="h1_content header_2">
	<span class="_text_dd" lang="_duckdns" datafld="title"></span>
</div>
<div class="field_top">
	<span class="_text_dd" lang="_duckdns" datafld="desc"></span>
</div>
<div class="hr_0_content">
	<div class="hr_1"></div>
</div>
<div class="field_top">
	<label for="dd_url" class="_text_dd" lang="_duckdns" datafld="label"></label>
	<input type="text" id="dd_url" size="80" maxlength="512">
</div>
<div class="field_top">
	<span class="dd_help _text_dd" lang="_duckdns" datafld="help"></span>
</div>
<div class="field_top">
	<button type="button" id="dd_apply" onclick="dd_save_config()">
		<span class="_text_dd" lang="_duckdns" datafld="apply"></span>
	</button>
	<button type="button" id="dd_clear" onclick="dd_clear_config()">
		<span class="_text_dd" lang="_duckdns" datafld="clear"></span>
	</button>
</div>
<div id="dd_status" class="dd_status hidden"></div>
<script type="text/javascript">
/* Fallback: if the WD shell did not call page_load() for us,
 * initialize when the document is ready. */
$(document).ready(function () {
	if (typeof page_load == "function") page_load();
});
</script>
<style>
#dd_url {
	padding: 6px 8px;
	border: 1px solid #ccc;
	border-radius: 3px;
	font-family: "Courier New", Courier, monospace;
	font-size: 13px;
}
#dd_apply, #dd_clear {
	margin-right: 8px;
}
.dd_help {
	color: #666;
	font-size: 12px;
}
.dd_status {
	margin-top: 12px;
	padding: 8px 12px;
	border-radius: 3px;
}
.dd_status.success {
	background-color: #d4edda;
	color: #155724;
	border: 1px solid #c3e6cb;
}
.dd_status.error {
	background-color: #f8d7da;
	color: #721c24;
	border: 1px solid #f5c6cb;
}
.dd_status.hidden {
	display: none;
}
</style>
</body>
</html>
