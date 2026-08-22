/*
 * SPDX-FileCopyrightText: 2026 clang88
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
var DD_XML_LANGUAGE_EN;
var DD_XML_LANGUAGE;

function dd_load_language() {
	var lang_array = [
		"en-US", "fr-FR", "it-IT", "de-DE", "es-ES",
		"zh-CN", "zh-TW", "ko-KR", "ja-JP", "ru-RU",
		"pt-BR", "cs-CZ", "nl-NL", "hu-HU", "no-NO",
		"pl-PL", "sv-SE", "tr-TR"
	];

	var filename = "/apps/duckdns/lang/" + lang_array[parseInt(MULTI_LANGUAGE, 10)] + ".xml";

	wd_ajax({
		type: "GET",
		url: filename,
		dataType: "xml",
		async: false,
		cache: false,
		error: function () {},
		success: function (xml) {
			DD_XML_LANGUAGE = xml;
		}
	});
}

function dd_load_en_language() {
	wd_ajax({
		type: "GET",
		url: "/apps/duckdns/lang/en-US.xml",
		dataType: "xml",
		async: false,
		cache: false,
		error: function () {},
		success: function (xml) {
			DD_XML_LANGUAGE_EN = xml;
		}
	});
}

function _T_DD(c, id) {
	var str = "";
	var find = false;

	if (typeof DD_XML_LANGUAGE == 'undefined') dd_load_language();
	if (typeof DD_XML_LANGUAGE_EN == 'undefined') dd_load_en_language();

	$(DD_XML_LANGUAGE).find(c).each(function () {
		str = $(this).find(id).text();
		if (str != "") {
			find = true;
		}
		return false;
	});

	if (find == false) {
		$(DD_XML_LANGUAGE_EN).find(c).each(function () {
			str = $(this).find(id).text();
			return false;
		});
	}
	return str;
}

function dd_language() {
	$('._text_dd').each(function () {
		var str = _T_DD($(this).attr('lang'), $(this).attr('datafld'));
		if (str != "") {
			$(this).empty();
			$(this).html(str);
		}
	});
}

function dd_ready_language() {
	dd_load_en_language();
	dd_load_language();
	dd_language();
}
