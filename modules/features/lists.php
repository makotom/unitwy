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

function unitwy_feature($args){
	global $dsgn, $msgs;

	$screen_name = isset($args[1]) && (string) $args[1] !== "" ? (string) $args[1] : USERNAME;
	$sn_url = secure_urlencode($screen_name);
	$sn_html = htmlspecialchars($screen_name, ENT_QUOTES, "UTF-8", FALSE);

	$content = "<p><b>@<a href=\"user/{$sn_url}\">{$sn_html}</a> > {$msgs->msg63089}</b>: ";

	$mode = isset($args[2]) ? $args[2] : "";

	switch($mode){
		case "subscriptions":
			$content .= "<a href=\"lists/{$sn_url}\">{$msgs->msg66568}</a> | <strong>{$msgs->msg58309}</strong> | <a href=\"lists/{$sn_url}/memberships\">{$msgs->msg94757}</a></p>\n";

			$lists = twitter_cursor_paginated_process("lists/subscriptions.json?screen_name={$sn_url}");
			$content .= orgr_listList(build_path_from_args($args), $lists);

			output_page("{$sn_html} > {$msgs->msg63089}: {$msgs->msg58309}", $content);

			return;
		case "memberships":
			$content .= "<a href=\"lists/{$sn_url}\">{$msgs->msg66568}</a> | <a href=\"lists/{$sn_url}/subscriptions\">{$msgs->msg58309}</a> | <strong>{$msgs->msg94757}</strong></p>\n";

			$lists = twitter_cursor_paginated_process("lists/memberships.json?screen_name={$sn_url}");
			$content .= orgr_listList(build_path_from_args($args), $lists);

			output_page("{$sn_html} > {$msgs->msg63089}: {$msgs->msg94757}", $content);

			return;
		case "belongings":
		default:
			$content .= "<strong>{$msgs->msg66568}</strong> | <a href=\"lists/{$sn_url}/subscriptions\">{$msgs->msg58309}</a> | <a href=\"lists/{$sn_url}/memberships\">{$msgs->msg94757}</a></p>\n";

			if(is_current_user($screen_name)){
				$content .= "\n" . $dsgn->listInfoForm("mkls", "<strong>{$msgs->msg81973}</strong>", $msgs->msg30397) . "\n\n";
			}

			$lists = twitter_cursor_paginated_process("lists.json?screen_name={$sn_url}");
			$content .= orgr_listList(build_path_from_args($args), $lists);

			output_page("{$sn_html} > {$msgs->msg63089}: {$msgs->msg66568}", $content);

			return;
	}

	return;
}
