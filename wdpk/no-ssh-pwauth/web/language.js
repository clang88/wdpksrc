/*
 * SPDX-FileCopyrightText: 2026 clang88
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
var NSPA_XML_LANGUAGE_EN;
var NSPA_XML_LANGUAGE;

function nspa_load_language() {
	var lang_array = [
		"en-US", "fr-FR", "it-IT", "de-DE", "es-ES",
		"zh-CN", "zh-TW", "ko-KR", "ja-JP", "ru-RU",
		"pt-BR", "cs-CZ", "nl-NL", "hu-HU", "no-NO",
		"pl-PL", "sv-SE", "tr-TR"
	];

	var filename = "/apps/no-ssh-pwauth/lang/" + lang_array[parseInt(MULTI_LANGUAGE, 10)] + ".xml";

	wd_ajax({
		type: "GET",
		url: filename,
		dataType: "xml",
		async: false,
		cache: false,
		error: function () {},
		success: function (xml) {
			NSPA_XML_LANGUAGE = xml;
		}
	});
}

function nspa_load_en_language() {
	wd_ajax({
		type: "GET",
		url: "/apps/no-ssh-pwauth/lang/en-US.xml",
		dataType: "xml",
		async: false,
		cache: false,
		error: function () {},
		success: function (xml) {
			NSPA_XML_LANGUAGE_EN = xml;
		}
	});
}

function _T_NSPA(c, id) {
	var str = "";
	var find = false;

	if (typeof NSPA_XML_LANGUAGE == 'undefined') nspa_load_language();
	if (typeof NSPA_XML_LANGUAGE_EN == 'undefined') nspa_load_en_language();

	$(NSPA_XML_LANGUAGE).find(c).each(function () {
		str = $(this).find(id).text();
		if (str != "") {
			find = true;
		}
		return false;
	});

	if (find == false) {
		$(NSPA_XML_LANGUAGE_EN).find(c).each(function () {
			str = $(this).find(id).text();
			return false;
		});
	}
	return str;
}

function nspa_language() {
	$('._text_nspa').each(function () {
		var str = _T_NSPA($(this).attr('lang'), $(this).attr('datafld'));
		if (str != "") {
			$(this).empty();
			$(this).html(str);
		}
	});
}

function nspa_ready_language() {
	nspa_load_en_language();
	nspa_load_language();
	nspa_language();
}
