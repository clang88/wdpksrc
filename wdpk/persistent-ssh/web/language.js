/*
 * SPDX-FileCopyrightText: 2026 clang88
 *
 * SPDX-License-Identifier: GPL-2.0-or-later
 */
var PS_XML_LANGUAGE_EN;
var PS_XML_LANGUAGE;

function ps_load_language() {
	var lang_array = [
		"en-US", "fr-FR", "it-IT", "de-DE", "es-ES",
		"zh-CN", "zh-TW", "ko-KR", "ja-JP", "ru-RU",
		"pt-BR", "cs-CZ", "nl-NL", "hu-HU", "no-NO",
		"pl-PL", "sv-SE", "tr-TR"
	];

	var filename = "/apps/persistent-ssh/lang/" + lang_array[parseInt(MULTI_LANGUAGE, 10)] + ".xml";

	wd_ajax({
		type: "GET",
		url: filename,
		dataType: "xml",
		async: false,
		cache: false,
		error: function () {},
		success: function (xml) {
			PS_XML_LANGUAGE = xml;
		}
	});
}

function ps_load_en_language() {
	wd_ajax({
		type: "GET",
		url: "/apps/persistent-ssh/lang/en-US.xml",
		dataType: "xml",
		async: false,
		cache: false,
		error: function () {},
		success: function (xml) {
			PS_XML_LANGUAGE_EN = xml;
		}
	});
}

function _T_PS(c, id) {
	var str = "";
	var find = false;

	if (typeof PS_XML_LANGUAGE == 'undefined') ps_load_language();
	if (typeof PS_XML_LANGUAGE_EN == 'undefined') ps_load_en_language();

	$(PS_XML_LANGUAGE).find(c).each(function () {
		str = $(this).find(id).text();
		if (str != "") {
			find = true;
		}
		return false;
	});

	if (find == false) {
		$(PS_XML_LANGUAGE_EN).find(c).each(function () {
			str = $(this).find(id).text();
			return false;
		});
	}
	return str;
}

function ps_language() {
	$('._text_ps').each(function () {
		var str = _T_PS($(this).attr('lang'), $(this).attr('datafld'));
		if (str != "") {
			$(this).empty();
			$(this).html(str);
		}
	});
}

function ps_ready_language() {
	ps_load_en_language();
	ps_load_language();
	ps_language();
}
