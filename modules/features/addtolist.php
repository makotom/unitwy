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
	global $msgs;
	$key = security_key_create();

	if(!isset($args[1]) || (string) $args[1] === ""){
		return;
	}
	$screen_name = $args[1];
	$sn_url = rawurlencode($screen_name);
	$sn_html = htmlspecialchars($screen_name, ENT_QUOTES, "UTF-8", FALSE);

	$content = "<p>{$msgs->msg67751}: <strong><a href=\"user/{$sn_url}\">{$sn_html}</a></strong><br>\n{$msgs->msg86983}</p>\n";

	$json = twitter_process("lists.json");
	if(count($json->lists) === 0){
		$content .= "<p>{$msgs->msg31114} <a href=\"/lists\">{$msgs->msg33034}</a></p>";
	}

	$rows = array();
	foreach($json->lists as $list){
		$name = $list->slug !== $list->name ? " <strong>({$list->name})</strong>" : "";

		$row = array("<a href=\"list/" . secure_urlencode(USERNAME) . "/{$list->slug}/adduser?screen_name={$sn_url}&key={$key}\"><strong>{$list->slug}</strong></a>$name");
		if($list->mode === "private"){
			$row[0] .= " <small>({$msgs->msg47125})</small>";
		}
		if(is_current_user($list->user->screen_name)){
			$row[0] .= " <small>[<a href=\"list/" . secure_urlencode(USERNAME) . "/{$list->slug}/edit\">{$msgs->msg33817}</a>]</small>";
		}
		if($list->description){
			$row[0] .= "<br><small>{$list->description}</small>";
		}

		$rows[] = $row;
	}

	$content .= markup_table($rows);

	output_page("{$msgs->msg32820}: {$sn_html}", $content);

	return;
}
