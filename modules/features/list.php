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
	$key = security_key_create();

	if(count($args) < 3){
		return;
	}
	if((string) $args[1] === ""){
		return;
	}

	$screen_name = (string) $args[1];
	$list = (string) $args[2];
	$method = isset($args[3]) ? (string) $args[3] : "";

	if($list === ""){
		return;
	}

	$sn_url = rawurlencode($screen_name);
	$sn_html = htmlspecialchars($screen_name, ENT_QUOTES, "UTF-8", FALSE);
	$list_url = rawurlencode($list);
	$list_html = htmlspecialchars($list, ENT_QUOTES, "UTF-8", FALSE);
	$list_id = "owner_screen_name={$sn_url}&slug=" . $list_url;

	switch($method){
		case "":
			$page = !empty($_GET["page"]) && ($page = intval($_GET["page"])) ? (string) $page : "1";
			$resp = twitter_process("lists/statuses.json?{$list_id}&include_rts=1&include_entities=1&page={$page}");

			$list_info = twitter_process("lists/show.json?{$list_id}");
			$list = $list_info->slug;
			$list_url = secure_urlencode($list_info->slug);
			$list_html= htmlspecialchars($list_info->slug, ENT_QUOTES, "UTF-8", FALSE);
			$list_page = "list/{$sn_url}/{$list_url}";

			$content = "";

			$refresh_int = (int) get_prefs("list_int", DEF_REF_INT);
			if($refresh_int !== 0){
				$content .= $dsgn->refreshScript($list_page, $refresh_int, "\n\n");
			}

			$content .= $dsgn->statusForm("", "", $list_page) . "\n\n";

			$meta = "";
			if($list_info->slug !== $list_info->name){
				$meta .= " <strong>({$list_info->name})</strong>";
			}
			$image = $dsgn->userPicture($list_info->user, TRUE);
			if($list_info->description !== ""){
				$meta .= " <small>{$list_info->description}</small>";
			}

			$chstat = "";
			if(is_current_user($screen_name)){
				$chstat = "<a href=\"{$list_page}/edit\">{$msgs->msg23469}</a>";
			}else{
				if($list_info->following){
					$chstat = "<a href=\"{$list_page}/unsubscribe?key={$key}\">{$msgs->msg85505}</a>";
				}else{
					$chstat = "<a href=\"{$list_page}/subscribe?key={$key}\">{$msgs->msg41051}</a>";
				}
			}

			$members = format_unit($list_info->member_count, $msgs->msg52540);
			$subscribers = format_unit($list_info->subscriber_count, $msgs->msg49853);

			$content .= <<<EOD
<p>
{$image}<br>
@<a href="user/{$sn_url}">{$sn_html}</a>/<strong>{$list_html}</strong>{$meta}<br>
<a href="{$list_page}/members">{$members}</a> | <a href="{$list_page}/subscribers">{$subscribers}</a> | {$chstat}
</p>


EOD;
			$content .= orgr_statusesTimeline(build_path_from_args($args), $resp);

			output_page("{$msgs->msg78625} {$sn_html}/{$list_html}", $content);

			return;
		case "members":
			$content = "<p>@<a href=\"user/{$sn_url}\">{$sn_html}</a>/<a href=\"list/{$sn_url}/{$list_url}\">{$list_html}</a> > <strong>{$msgs->msg74462}</strong></p>\n";
			$resp = twitter_cursor_paginated_process("lists/members.json?{$list_id}");

			if(is_current_user($screen_name)){
				$content .= <<<EOD

<form action="list/{$sn_url}/{$list_url}/adduser?key={$key}" method="get">
<input type="hidden" name="key" value="{$key}">
{$msgs->msg62270}: <input type="text" id="screen_name" name="screen_name"> <input type="submit" value="{$msgs->msg47706}">
</form>

EOD;
				$content .= orgr_listUsersList(build_path_from_args($args), $resp, "list/{$sn_url}/{$list_url}/userdel?screen_name=");
			}else{
				$content .= orgr_listUsersList(build_path_from_args($args), $resp);
			}

			output_page("{$sn_html}/{$list_html} > {$msgs->msg74462}", $content);

			return;
		case "subscribers":
			$content = "<p>@<a href=\"user/{$sn_url}\">{$sn_html}</a>/<a href=\"list/{$sn_url}/{$list_url}\">{$list_html}</a> > <strong>{$msgs->msg47526}</strong></p>\n";
			$content .= orgr_listUsersList(build_path_from_args($args), twitter_cursor_paginated_process("lists/subscribers.json?{$list_id}"));

			output_page("{$sn_html}/{$list_html} > {$msgs->msg47526}", $content);

			return;
		case "edit":
			if(!is_current_user($screen_name)){
				return;
			}

			$list_info = twitter_process("lists/show.json?{$list_id}");
			$list_url = secure_urlencode($list_info->slug);
			$list_html= htmlspecialchars($list_info->slug, ENT_QUOTES, "UTF-8", FALSE);

			$content = $dsgn->listInfoForm("list/{$sn_url}/{$list_url}/update", "<strong>{$msgs->msg27324}</strong>: @<a href=\"lists/{$sn_url}\">{$sn_html}</a>/<a href=\"list/{$sn_url}/{$list_url}\"><strong>{$list_html}</strong></a>", $msgs->msg93977, $list_info->name, $list_info->mode, htmlspecialchars($list_info->description, ENT_QUOTES, "UTF-8", FALSE));
			$content .= "\n\n<form action=\"rmls/{$list_url}\" method=\"post\"><input type=\"submit\" value=\"{$msgs->msg98797}\"></form>";

			output_page("{$msgs->msg84818} {$sn_html}/{$list_url}", $content);

			return;
		case "subscribe":
			if(empty($_GET["key"]) || !security_key_check($_GET["key"])){
				return;
			}

			twitter_process("lists/subscribers/create.json?{$list_id}", TRUE);
			page_refresh("lists/" . secure_urlencode(USERNAME) . "/subscriptions");

			return;
		case "unsubscribe":
			if(empty($_GET["key"]) || !security_key_check($_GET["key"])){
				return;
			}

			twitter_process("lists/subscribers/destroy.json?{$list_id}", TRUE);
			page_refresh("lists/" . secure_urlencode(USERNAME) . "/subscriptions");

			return;
		case "adduser":
			if(isset($_GET["screen_name"]) && (string) $_GET["screen_name"] !== "" && empty($_GET["key"]) || !security_key_check($_GET["key"])){
				return;
			}

			$target_sn_url = rawurlencode($_GET["screen_name"]);

			twitter_process("lists/members/create.json?{$list_id}&screen_name={$target_sn_url}", TRUE);
			page_refresh("list/" . secure_urlencode(USERNAME) . "/{$list_url}/members");

			return;
		case "userdel":
			if(isset($_GET["screen_name"]) && (string) $_GET["screen_name"] !== "" && empty($_GET["key"]) || !security_key_check($_GET["key"])){
				return;
			}

			$target_sn_url = rawurlencode($_GET["screen_name"]);

			twitter_process("lists/members/destroy.json?{$list_id}&screen_name={$target_sn_url}", TRUE);
			page_refresh("list/" . secure_urlencode(USERNAME) . "/{$list_url}/members");

			return;
		case "update":
			if(empty($_POST["key"]) || !security_key_check($_POST["key"])){
				return;
			}

			$data = array("name" => "", "mode" => "", "description" => "");

			foreach($_POST as $name => $value){
				if(isset($data[$name])){
					$data[$name] = $value;
				}
			}

			twitter_process("lists/update.json?{$list_id}", $data);
			page_refresh("lists/{$sn_url}");

			return;
	}

	return;
}
