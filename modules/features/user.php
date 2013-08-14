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

	if(!isset($args[1]) || (string) $args[1] === ""){
		return;
	}
	$screen_name = $args[1];
	$sn_url = rawurlencode($screen_name);
	$sn_html = htmlspecialchars($screen_name, ENT_QUOTES, "UTF-8", FALSE);

	if(isset($args[2]) && $args[2] !== ""){
		page_refresh("list/{$sn_url}/" . rawurlencode($args[2]));
		return;
	}

	$page = !empty($_GET["page"]) && ($page = intval($_GET["page"])) ? (string) $page : "1";
	$user = twitter_process("users/show.json?screen_name={$sn_url}");
	$statuses = twitter_process("statuses/user_timeline.json?screen_name={$sn_url}&include_rts=1&include_entities=1&count=20&page={$page}", FALSE, TRUE);

	$screen_name = (!empty($user->screen_name) ? $user->screen_name : $screen_name);
	$sn_url = rawurlencode($screen_name);
	$sn_html = htmlspecialchars($screen_name, ENT_QUOTES, "UTF-8", FALSE);

	$content = $dsgn->statusForm(!is_current_user($screen_name) ? "@{$sn_html} " : "") . "\n\n";
	$content .= orgr_userHeader($user, twitter_process("friendships/show.json?target_screen_name={$sn_url}")->relationship->source) . "\n\n";
	$content .= orgr_statusesTimeline("user/{$sn_url}", $statuses, is_current_user($screen_name), TRUE);

	output_page("{$msgs->msg84393} {$user->screen_name}", $content);

	return;
}
