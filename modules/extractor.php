<?php
/*
Copyright (C) 2013 Makoto Mizukami. All rights reserved.

Permission is hereby granted, free of charge, to any person obtaining a
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction, including without limitation
the rights to use, copy, modify, merge, publish, distribute, sublicense,
and/or sell copies of the Software, and to permit persons to whom the
Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.
*/

/*
extractor - reference extractor
Copyright (C) 2011 Makoto Mizukami.
*/

function extractor_hashtag_regex(){
	$UNICODE_SPACES = 
		"\011-\015" .				// White_Space # Cc   [5] <control-0009> / <control-000D>
		"\040" .				// White_Space # Zs       SPACE
		"\302\205" .				// White_Space # Cc       <control-0085>
		"\302\240" .				// White_Space # Zs       NO-BREAK SPACE
		"\341\232\200" .			// White_Space # Zs       OGHAM SPACE MARK
		"\341\243\240" .			// White_Space # Zs       MONGOLIAN VOWEL SEPARATOR
		"\342\200\200-\342\200\212" .		// White_Space # Zs  [11] EN QUAD / HAIR SPACE
		"\342\200\250" .			// White_Space # Zl       LINE SEPARATOR
		"\342\200\251" .			// White_Space # Zp       PARAGRAPH SEPARATOR
		"\342\200\257" .			// White_Space # Zs       NARROW NO-BREAK SPACE
		"\342\201\237" .			// White_Space # Zs       MEDIUM MATHEMATICAL SPACE
		"\343\200\200" .			// White_Space # Zs       IDEOGRAPHIC SPACE
	"";
	$LATIN_ACCENTS = "ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûüýþş\303\277";
	$NON_LATIN_HASHTAG_CHARS = 
		"\320\200-\323\277" .			// Cyrillic (U+0400 / U+04ff)
		"\324\200-\324\247" .			// Cyrillic Supplement (U+0500 / U+0527)
		"\342\267\240-\342\267\277" .		// Cyrillic Extended A (U+2DE0 / U+2DFF)
		"\352\231\200-\352\232\237" .		// Cyrillic Extended B (U+a640 / U+a69f)

		"\341\204\200-\341\207\277" .		// Hangul Jamo (U+1100 / U+11ff)
		"\343\204\260-\343\206\205" .		// Hangul Compatibility Jamo (U+3130 / U+3185)
		"\352\245\240-\352\245\277" .		// Hangul Jamo Extended-A (U+A960 / U+A97F)
		"\352\260\200-\355\236\257" .		// Hangul Syllables (U+AC00 / U+D7AF)
		"\355\236\260-\355\237\277" .		// Hangul Jamo Extended-B (U+D7B0 / U+D7FF)
		"\357\276\241-\357\277\234" .		// half-width Hangul (U+FFA1 / U+FFDC

		"\343\202\241-\343\203\272" .		// Katakana (full-width) (U+30A1 / U+30FA)
		"\343\203\274-\343\203\276" .		// Katakana Chouon and iteration marks (full-width) (U+30FC / U+30FE)
		"\357\275\246-\357\276\237" .		// Katakana (half-width) (U+FF66 / U+FF9F)
		"\357\275\260" .				// Katakana Chouon (half-width) (U+FF70 / U+FF70)
		"\357\274\220-\357\274\231" .		// Fullwidth Digit (U+FF10 / U+FF19)
		"\357\274\241-\357\274\272" .		// Latin (full-width, capital) (U+FF21 / U+FF3A)
		"\357\275\201-\357\275\232" .		// Latin (full-width, small) (U+FF41 / U+FF5A)
		"\343\201\200-\343\202\226" .		// Hiragana (U+3041 / U+3096)
		"\343\202\231-\343\202\236" .		// Hiragana voicing and iteration mark (U+3099 / U+309E)
		"\343\220\200-\344\266\277" .		// Kanji (CJK Extension A) (U+3400 / U+4DBF)
		"\344\270\200-\351\277\277" .		// Kanji (Unified) (U+4E00 / U+9FFF)

		"\360\240\200\200-\360\252\233\237" .	// Kanji (CJK Extension B) (U+20000 / U+2A6DF)
		"\360\252\234\200-\360\253\234\277" .	// Kanji (CJK Extension C) (U+2A700 / U+2B73F)
		"\360\253\235\200-\360\253\240\237" .	// Kanji (CJK Extension D) (U+2B740 / U+2B81F)
		"\360\257\240\200-\360\257\250\237" .	// Kanji (CJK supplement) (U+2F800 / U+2FA1F)
		"\343\200\205" .			// Kanji (iteration mark) (U+3005)
		"\343\200\273" .			// Han iteration mark (U+303B)

	"";
	$HASHTAG_BOUNDARY = "^|$|[^&\/a-z0-9_{$LATIN_ACCENTS}{$NON_LATIN_HASHTAG_CHARS}]";

	$HASHTAG_ALPHA = "[a-z_{$LATIN_ACCENTS}{$NON_LATIN_HASHTAG_CHARS}]";
	$HASHTAG_ALPHANUMERIC = "[a-z0-9_{$LATIN_ACCENTS}{$NON_LATIN_HASHTAG_CHARS}]";

	$HASHTAG = "/({$HASHTAG_BOUNDARY})(#|＃)({$HASHTAG_ALPHANUMERIC}*{$HASHTAG_ALPHA}{$HASHTAG_ALPHANUMERIC}*)/iu";

	return $HASHTAG;
}

function extractor_url_regex(){
	$REGEX_URL_BEFORE = "(?:[^-\/\"'!=A-Z0-9_@＠]|^)";
	$REGEX_URL_DOMAIN = "(?:[^[:punct:]\s][\.-](?=[^[:punct:]\s])|[^[:punct:]\s]){1,}\.[a-z]{2,}(?::[0-9]+)?";
	$REGEX_URL_PATH_CHARS = "[a-z0-9!\*';:=\+\,\$\/%#\[\]\-_~]";
	$REGEX_URL_PATH = "(?:(?:{$REGEX_URL_PATH_CHARS}+)|(?:@{$REGEX_URL_PATH_CHARS}\/)|(?:[\.,]{$REGEX_URL_PATH_CHARS}+)|(?:\({$REGEX_URL_PATH_CHARS}+\)))*";
	$REGEX_URL_QUERY = "[a-z0-9!\*'\(\);:&=\+\$\/%#\[\]\-_\.,~]*[a-z0-9_&=#\/]";
	return "/" .
		"({$REGEX_URL_BEFORE})" .	// $1 Preceding character
		"(" .				// $2 URL
		"(https?:\/\/)" .		// $3 Protocol
		"({$REGEX_URL_DOMAIN})" .	// $4 Domain(s) and optional port number
		"(\/{$REGEX_URL_PATH})?" .	// $5 URL Path and anchor
		"(\?{$REGEX_URL_QUERY})?" .	// $6 Query String
		")" .
		"/iux";
}

define("EXTRACTOR_HASHTAG_REGEX", extractor_hashtag_regex());
define("EXTRACTOR_USER_REGEX", "/(^|[^a-zA-Z0-9_])([@＠])([a-zA-Z0-9_]{1,20})(?=(.|$))/imu");
define("EXTRACTOR_USER_LIST_REGEX", "/(^|[^a-zA-Z0-9_])([@＠])([a-zA-Z0-9_]{1,20}(?:\/[a-zA-Z][a-zA-Z0-9_\-\302\200-\303\277]{0,24})?)(?=(.|$))/imu");
define("EXTRACTOR_URL_REGEX", extractor_url_regex());
define("EXTRACTOR_ISBN_REGEX", "/([^-\/\"'!=A-Z0-9_@＠]|^)(urn:isbn:([\d\-X]+))/imu");

function expand_tco($text, $entities){
	$urls = $entities->urls;
	if(!empty($entities->media)){
		$urls = array_merge($urls, $entities->media);
	}

	foreach($urls as $obj_url){
		if(empty($obj_url->expanded_url)){
			continue;
		}
		$obj_url->expanded_url = !empty($obj_url->media_url) ? $obj_url->media_url : $obj_url->expanded_url;
		$text = preg_replace("/" . preg_quote($obj_url->url, "/") . "/u", $obj_url->expanded_url, $text);
	}

	return $text;
}

function long_url($url){
	$lu_re = "/^https?:\/\/(bit\.ly|cli\.gs|digg\.com|fb\.me|is\.gd|j\.mp|kl\.am|su\.pr|tinyurl\.com|goo\.gl|307\.to|adjix\.com|b23\.ru|bacn\.me|bloat\.me|budurl\.com|clipurl\.us|cort\.as|dFL8\.me|dwarfurl\.com|ff\.im|fff\.to|href\.in|idek\.net|korta\.nu|lin\.cr|livesi\.de|ln-s\.net|loopt\.us|lost\.in|memurl\.com|merky\.de|migre\.me|moourl\.com|nanourl\.se|om\.ly|ow\.ly|peaurl\.com|ping\.fm|piurl\.com|plurl\.me|pnt\.me|poprl\.com|post\.ly|rde\.me|reallytinyurl\.com|redir\.ec|retwt\.me|rubyurl\.com|short\.ie|short\.to|smallr\.com|sn\.im|sn\.vc|snipr\.com|snipurl\.com|snurl\.com|tiny\.cc|tinysong\.com|togoto\.us|tr\.im|tra\.kz|trg\.li|twurl\.cc|twurl\.nl|u\.mavrev\.com|u\.nu|ur1\.ca|url\.az|url\.ie|urlx\.ie|w34\.us|xrl\.us|yep\.it|zi\.ma|zurl\.ws|chilp\.it|notlong\.com|qlnk\.net|trim\.li|url4\.eu|htn\.to|t\.co)\//";
	if(!preg_match($lu_re, $url)){
		return $url;
	}
	$headers = get_headers($url, 1);

	if(empty($headers["Location"])){
		return $url;
	}
	$location = is_array($headers["Location"]) ? $headers["Location"][0] : $headers["Location"];

	return $location;
}

function extractor_rplexturl_anchor($matches, $in_detail = FALSE){
	$url = $matches[2];
	$disp = !$in_detail && mb_strlen($url) > 128 ? mb_substr($url, 0, 125) . "..." : $url;

	return $matches[1] . markup_external_anchor($url, $disp);
}

function extractor_rplexturl_anchor_detail($matches){
	$matches[2] = long_url($matches[2]);
	return extractor_rplexturl_anchor($matches, TRUE);
}

function extractor_rplhash_anchor($matches){
	return "{$matches[1]}<a href=\"hash/" . rawurlencode($matches[3]) . "\">{$matches[2]}{$matches[3]}</a>";
}

function extractor_rplisbn_anchor($matches){
	$anchor = $matches[2];
	$isbn = preg_replace("/[^\dX]/i", "", $matches[3]);

	if(is_numeric($isbn) && strlen($isbn) === 13){
		$anchor = markup_external_anchor("http://duckduckgo.com/?q=isbn%20{$isbn}", $matches[2]);
	}else if((is_numeric($isbn) && strlen($isbn) === 10) || (strlen($isbn) === 9 && strtoupper($matches[3][9]) === "X")){
		$isbn13 = "978";
		$cd = (9 * 1) + (7 * 3) + (8 * 1);

		for($i = 0; $i < 9; $i += 1){
			$isbn13 .= $isbn[$i];
			$cd += (int) $isbn[$i] * ($i % 2 === 1 ? 1 : 3);
		}

		$isbn13 .= (string) ((10 - ($cd % 10)) % 10);

		$anchor = markup_external_anchor("http://duckduckgo.com/?q=isbn%20{$isbn13}", $matches[2]);
	}

	return $matches[1] . $anchor;
}

function extractor_replace_anchors($status, $in_detail = FALSE){
	$text = $status->text;
	$text = !empty($status->entities) ? expand_tco($text, $status->entities) : $text;
	$text = preg_replace_callback(EXTRACTOR_HASHTAG_REGEX, "extractor_rplhash_anchor", $text);
	$text = preg_replace(EXTRACTOR_USER_LIST_REGEX, "$1$2<a href=\"user/$3\">$3</a>", $text);
	$text = preg_replace_callback(EXTRACTOR_URL_REGEX, ($in_detail ? "extractor_rplexturl_anchor_detail" : "extractor_rplexturl_anchor"), $text);
	$text = preg_replace_callback(EXTRACTOR_ISBN_REGEX, "extractor_rplisbn_anchor", $text);
	return $text;
}

function extractor_extract_usernames($status){
	if(empty($status->entities)){
		preg_match_all(EXTRACTOR_USER_REGEX, $status->text, $matches);
		return $matches[4];
	}
	$entities = $status->entities;

	$names = array();
	foreach($entities->user_mentions as $user){
		$names[] = $user->screen_name;
	}

	return $names;
}

function extractor_extract_urls($status){
	$entities = !empty($status->entities->urls) ? $status->entities->urls : array();
	if(!empty($status->entities->media)){
		$entities = array_merge($entities, $status->entities->media);
	}
	$urls = array();
	foreach($entities as $entity){
		$urls[] = !empty($entity->media_url) ? $entity->media_url : (!empty($entity->expanded_url) ? $entity->expanded_url : $entity->url);
	}

	preg_match_all(EXTRACTOR_URL_REGEX, $status->text, $matches);
	$urls = array_unique(array_merge($matches[2], $urls));

	return $urls;
}

function extractor_get_users_str_to_reply($status, $is_to_all = FALSE){
	$to_users = array($status->user->screen_name);
	if($is_to_all){
		$found = extractor_extract_usernames($status);
		$to_users = array_unique(array_merge($to_users, $found));
		if(!is_current_user($status->user->screen_name) && $me = array_search(USERNAME, $to_users)){
			array_splice($to_users, $me, 1);
		}
	}
	return "@" . implode(" @", $to_users) . " ";
}
