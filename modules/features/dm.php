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

	$create = "<a href=\"dm/new\">{$msgs->msg40339}</a>";
	$inbox = "<a href=\"dm/inbox\">{$msgs->msg24993}</a>";
	$sent = "<a href=\"dm/sent\">{$msgs->msg32082}</a>";

	$page = !empty($_GET["page"]) && ($page = intval($_GET["page"])) ? (string) $page : "1";

	$no_notification = 0;

	switch(!empty($args[1]) ? $args[1] : ""){
		case "new":
			if(!isset($_POST["to"]) || !isset($_POST["text"])){
				$create = "<strong>{$msgs->msg40339}</strong>";
				$page_title = $msgs->msg86601;

				$screen_name = "";
				if(isset($args[2]) && (string) $args[2] !== ""){
					$relationship = twitter_process("friendships/show.json?target_screen_name=" . rawurlencode($args[2]))->relationship->target;
					if($relationship->following !== TRUE){
						$content = "<p>{$msgs->msg93268}</p>";
						break;
					}
					$screen_name = htmlspecialchars($relationship->screen_name, ENT_QUOTES, "UTF-8", FALSE);
				}

				$content = $dsgn->dmForm($screen_name);
				break;
			}

			if(empty($_POST["key"]) || !security_key_check($_POST["key"])){
				return;
			}

			twitter_process("direct_messages/new.json", array("user" => $_POST["to"], "text" => $_POST["text"]));
			page_refresh("dm/sent");

			return;
		case "destroy":
			if(!isset($args[2]) || !is_numeric($args[2])){
				return;
			}
			$id = $args[2];

			if(confirmation_check()){
				twitter_process("direct_messages/destroy/{$id}.json", TRUE);
				page_refresh($_POST["back"]);
				return;
			}else{
				confirmation_lock("dm/destroy/{$id}", $msgs->msg62099);
			}
			return;
		case "inbox":
		default:
			$inbox = "<strong>{$msgs->msg24993}</strong>";

			$statuses = twitter_process("direct_messages.json?include_entities=1&count=20&page={$page}");

			$no_notification = "dm";
			notification_clear_flags("dm", $statuses[0]->id_str);

			$page_title = $msgs->msg93026;

			$content = "";

			$refresh_int = (int) get_prefs("dm_int", DEF_REF_INT);
			if($refresh_int !== 0){
				$content .= $dsgn->refreshScript("dm", $refresh_int, "\n\n");
			}

			$content .= orgr_dmTimeline(build_path_from_args($args), $statuses);

			break;
		case "sent":
			$sent = "<strong>{$msgs->msg32082}</strong>";
			$page_title = $msgs->msg48554;
			$content = orgr_dmTimeline(build_path_from_args($args), twitter_process("direct_messages/sent.json?include_entities=1&count=20&page={$page}"), TRUE);
			break;
	}

	$dm_menu = implode(" | ", array($create, $inbox, $sent));

	$content = <<<EOD
<p><b>{$msgs->msg62014}</b>: {$dm_menu}</p>

{$content}
EOD;

	output_page($page_title, $content, $no_notification);

	return;
}
