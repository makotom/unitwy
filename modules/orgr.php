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

function orgr_actionButtons($status, $is_dm = FALSE){
	global $dsgn;

	$key = security_key_create();

	$actions = array();

	if(!$is_dm){
		$actions[] = $dsgn->actionIcon("status/{$status->id_str}?reply", "../images/reply.png", "@");
	}

	if(count(extractor_extract_usernames($status)) > 0){
		$actions[] = $dsgn->actionIcon("status/{$status->id_str}/?reply=all", "../images/replyall.png", "@@");
	}

	if(!is_current_user($status->user->screen_name)){
		$actions[] = $dsgn->actionIcon("dm/new/{$status->user->screen_name}", "../images/dm.png", "D");
	}

	if($is_dm){
		$actions[] = $dsgn->actionIcon("dm/destroy/{$status->id_str}", "../images/trash.png", "E");
	}else{
		if(!is_current_user($status->user->screen_name)){
			if(!empty($status->favorited) && $status->favorited){
				$actions[] = $dsgn->actionIcon("unfavourite/{$status->id_str}?key={$key}", "../images/favt.png", "U");
			}else{
				$actions[] = $dsgn->actionIcon("favourite/{$status->id_str}?key={$key}", "../images/favf.png", "F");
			}

			$actions[] = $dsgn->actionIcon("retweet/{$status->id_str}?key={$key}", "../images/rt.png", "R");
		}

		$actions[] = $dsgn->actionIcon("quote/{$status->id_str}", "../images/qt.png", "Q");

		if(is_current_user($status->user->screen_name)){
			$actions[] = $dsgn->actionIcon("delete/{$status->id_str}", "../images/trash.png", "E");
		}

		if($status->geo !== NULL){
			$actions[] =$dsgn->actionIcon("http://maps.google.com/?q={$status->geo->coordinates[0]},{$status->geo->coordinates[1]}", "../images/map.png", "M");
		}

		if(!empty($status->in_reply_to_status_id_str)){
			$actions[] = $dsgn->actionIcon("status/{$status->id_str}", "../images/conv.png", "C");
		}
	}

	return implode("\n", $actions);
}

function orgr_formatDatetime($time, $iso8601 = FALSE){
	global $msgs;

	if($iso8601){
		return date("c", $time);
	}

	$interval = time() - (int) $time;
	if($interval < 60 * 60 * 24){
		$unit = array($msgs->msg27380, $msgs->msg39136, $msgs->msg89052);
		for($i = 0; $interval / 60 >= 1; $i += 1){
			$interval = floor($interval / 60);
		}
		$ret = format_unit($interval, $unit[$i]) . $msgs->msg49898;
	}else{
		$ret = date("Y-m-d H:i", $time);
	}

	return $ret;
}

function orgr_paginationCursor($url, $json, $label = NULL){
	global $dsgn, $msgs;

	if(!is_array($label)){
		$label = array($msgs->msg19570, $msgs->msg91205);
	}

	$cursor = !empty($_GET["cursor"]) && is_numeric($_GET["cursor"]) ? $_GET["cursor"] : "-1";

	$links = array();

	if($json->previous_cursor_str !== "0"){
		$links[] = $dsgn->paginationAnchor(htmlspecialchars(add_query($url, "cursor", $json->previous_cursor_str), ENT_QUOTES, "UTF-8", FALSE), $label[0], "7");
	}

	if($json->next_cursor_str !== "0"){
		$links[] = $dsgn->paginationAnchor(htmlspecialchars(add_query($url, "cursor", $json->next_cursor_str), ENT_QUOTES, "UTF-8", FALSE), $label[1], "9");
	}

	return !empty($links) ? $dsgn->paginationBox($links) : "";
}

function orgr_paginationPage($url, $no_next = FALSE, $label = NULL){
	global $dsgn, $msgs;

	if(!is_array($label)){
		$label = array($msgs->msg19570, $msgs->msg91205);
	}

	$page = !empty($_GET["page"]) && ($page = intval($_GET["page"])) ? $page : 1;

	$links = array();

	if($page > 1){
		$links[] = $dsgn->paginationAnchor(htmlspecialchars(add_query($url, "page", $page - 1), ENT_QUOTES, "UTF-8", FALSE), $label[0], "7");
	}

	if(!$no_next){
		$links[] = $dsgn->paginationAnchor(htmlspecialchars(add_query($url, "page", $page + 1), ENT_QUOTES, "UTF-8", FALSE), $label[1], "9");
	}

	return !empty($links) ? $dsgn->paginationBox($links) : "";
}

function orgr_statusBox($status, $odd_even = "even", $no_highlight = FALSE, $no_apparent_rt = FALSE, $in_detail = FALSE, $is_dm = FALSE){
	global $dsgn, $msgs;

	$retweet = "";
	if(!empty($status->retweeted_status)){
		if(!$no_apparent_rt){
			$retweet = "<br>\n<small>" . sprintf($msgs->msg57999, "<a href=\"status/{$status->id_str}\">", "</a>") . ": <a href=\"user/{$status->user->screen_name}\">{$status->user->screen_name}</a></small>";
			$status = $status->retweeted_status;
		}else{
			$retweet = "<br>\n<small><a href=\"status/{$status->retweeted_status->id_str}\">{$msgs->msg29289}</a></small>";
		}
	}else if($in_detail && !empty($status->retweet_count)){
		$retweet = "<br>\n<small>{$msgs->msg40771}: {$status->retweet_count}</small>";
	}

	$icon = ($icon = $dsgn->userPicture($status->user)) ? "<span class=\"icon\">\n{$icon}\n</span>\n" : "";

	$actions = ($actions = orgr_actionButtons($status, $is_dm)) ? "<span class=\"action_buttons\">\n{$actions}\n</span>" : "";

	$datetime = ($datetime = strtotime($status->created_at)) ? orgr_formatDatetime($datetime, $in_detail) : $status->created_at;
	if(!empty($status->id_str)){
		if($in_detail){
			$datetime = markup_external_anchor("http://twitter.com/{$status->user->screen_name}/status/{$status->id_str}", $datetime);
		}else{
			$datetime = "<a href=\"status/{$status->id_str}\">{$datetime}</a>";
		}
	}

	$source = "";
	if(!empty($status->source)){
		if(!preg_match("/^<a /", $status->source)){
			$source = $status->source;
		}else{
			preg_match("/<a (?:[^>]+ )?href=\"([^\"]+)\"(?: [^>]+)?>([^<]+)<\/a>/", $status->source, $matches);
			$source = markup_external_anchor($matches[1], $matches[2]);
		}
		$source = "\n" . sprintf($msgs->msg83527, $source);
	}

	$in_reply_to = $in_detail && $status->in_reply_to_status_id_str ? "\n<a href=\"status/{$status->in_reply_to_status_id_str}\">in reply to {$status->in_reply_to_screen_name}</a>" : "";

	$text = nl2br(thumbnailer_embed_thumbnails(extractor_extract_urls($status), extractor_replace_anchors($status, $in_detail)));

	$class = $odd_even;
	if(!$no_highlight){
		if(is_current_user($status->user->screen_name)){
			$class .= " mine";
		}else if(array_search(USERNAME, extractor_extract_usernames($status)) !== FALSE){
			$class .= " reply";
		}
	}

	return $dsgn->statusBoxMarkup($class, $icon, $status->user->screen_name, $actions, $datetime, $source, $in_reply_to, $text, $retweet);
}

function orgr_userList($users, $ids_to_highlight = NULL, $user_editable = FALSE){
	global $dsgn, $msgs;
	$key = security_key_create();

	if(count($users) === 0){
		return "<p>{$msgs->msg31439}</p>";
	}

	$user_boxes = array();

	$i = 0;
	foreach($users as $user){
		if($user === NULL){
			continue;
		}

		$full_name = !empty($user->name) ? " ({$user->name})" : "";

		$icon = ($icon = $dsgn->userPicture($user)) ? "<span class=\"icon\">\n{$icon}\n</span>\n" : "";

		$action = "";
		if(is_array($user_editable) && !empty($user_editable)){
			$action = array();
			foreach($user_editable as $action_data){
				$action_icon = !empty($action_data[1]) ? $action_data[1] : "../images/trash.png";
				$action_alt = !empty($action_data[2]) ? $action_data[2] : "D";
				$action[] = $dsgn->actionIcon(htmlspecialchars(add_query("{$action_data[0]}{$user->screen_name}", "key", $key), ENT_QUOTES, "UTF-8", FALSE), $action_icon, $action_alt);
			}
			$action = "<span class=\"action_icons\">" . implode(" ", $action) . "</span>";
		}

		$info = "";
		if(!empty($user->description)){
			$info = htmlspecialchars($user->description, ENT_QUOTES, "UTF-8", FALSE);
		}else if(!empty($user->url)){
			$info = markup_external_anchor($user->url);
		}else if(!empty($user->location)){
			$info = "@ {$user->location}";
		}

		$tweet_stat = "";
		if(!empty($user->status->created_at)){
			$last_status_time = strtotime($user->status->created_at);
			$tweet_stat = "{$msgs->msg13760}: " . ($last_status_time !== FALSE ? date("r", $last_status_time) : $user->status->created_at);
		}else if($user->protected){
			$tweet_stat = $msgs->msg69416;
		}else if($user->statuses_count == 0){
			$tweet_stat = $msgs->msg59371;
		}

		$info .= !empty($info) ? "<br>\n{$tweet_stat}" : $tweet_stat;

		$class = $i % 2 === 1 ? "odd" : "even";
		$class .= (is_array($ids_to_highlight) && array_search($user->id, $ids_to_highlight) !== FALSE) || (!is_array($ids_to_highlight) && $user->following) ? " reply" : "";

		$user_boxes[] = $dsgn->userBoxMarkup($class, $icon, $user->screen_name, $full_name, $action, $info);

		$i += 1;
	}

	return implode("\n\n", $user_boxes);
}

function orgr_dmTimeline($url, $texts, $is_sent = FALSE){
	global $dsgn, $msgs;

	if(count($texts) === 0){
		return "<p>{$msgs->msg66508}</p>" . orgr_paginationPage($url, TRUE);
	}

	$messages = array();

	$i = 0;
	foreach($texts as $text){
		$tmp = $text;

		$tmp->user = $is_sent ? $text->recipient : $text->sender;
		unset($tmp->sender);
		unset($tmp->recipient);

		$messages[] = orgr_statusBox($tmp, ($i % 2 === 1 ? "odd" : "even"), TRUE, FALSE, FALSE, TRUE);

		$i += 1;
	}

	return implode("\n\n", $messages) . orgr_paginationPage($url, FALSE);
}

function orgr_friendshipList($url, $users, $followers_list = NULL){
	global $dsgn;

	return orgr_userList($users, $followers_list, $followers_list !== NULL ? array(array("unfollow/")) : FALSE) . orgr_paginationPage($url, count($users) < 20 ? TRUE : FALSE);
}

function orgr_incomingUsersList($users){
	global $dsgn;

	return orgr_userList($users) . orgr_paginationPage("incoming", count($users) < 20 ? TRUE : FALSE);
}

function orgr_listList($page, $json){
	global $msgs;

	$lists = array();
	if(is_array($json)){
		$lists = $json;
	}else if(!empty($json->lists)){
		$lists = $json->lists;
	}
	if(count($lists) === 0){
		return "<p>{$msgs->msg47066}</p>" . orgr_paginationPage($page, TRUE);
	}

	$rows = array(array($msgs->msg78625, $msgs->msg74462, $msgs->msg47526));
	foreach($lists as $list){
		$url = "list/{$list->user->screen_name}/{$list->slug}";

		$name = "";
		if($list->slug !== $list->name){
			$name = " <strong>({$list->name})</strong>";
		}

		$row = array(
			"<a href=\"user/{$list->user->screen_name}\">@{$list->user->screen_name}</a>/<a href=\"{$url}\"><strong>{$list->slug}</strong></a>$name",
			"<a href=\"{$url}/members\">{$list->member_count}</a>",
			"<a href=\"{$url}/subscribers\">{$list->subscriber_count}</a>",
		);
		if($list->mode === "private"){
			$row[0] .= " <small>({$msgs->msg47125})</small>";
		}
		if(is_current_user($list->user->screen_name)){
			$row[0] .= " <small>[<a href=\"{$url}/edit\">{$msgs->msg33817}</a>]</small>";
		}
		if($list->description){
			$row[0] .= "<br><small>" . htmlspecialchars($list->description, ENT_QUOTES, "UTF-8", FALSE) . "</small>";
		}

		$rows[] = $row;
	}

	$content = markup_table($rows, TRUE);
	$content .= orgr_paginationCursor($page, $json);

	return $content;
}

function orgr_listUsersList($url, $json, $is_owner = FALSE){
	global $dsgn;

	return orgr_userList($json->users, NULL, $is_owner !== FALSE ? array(array($is_owner)) : FALSE) . orgr_paginationCursor($url, $json);
}

function orgr_searchTimeline($url, $statuses){
	global $dsgn, $msgs;

	if(count($statuses) === 0){
		return "<p>{$msgs->msg42227}</p>" . orgr_paginationPage($url, TRUE);
	}

	$tweets = array();

	$i = 0;
	foreach($statuses as $status){
		$tmp = $status;

		$tmp->user->screen_name = $tmp->from_user;
		$tmp->user->profile_image_url = $tmp->profile_image_url;
		$tmp->source = htmlspecialchars_decode($tmp->source);

		$tweets[] = orgr_statusBox($tmp, ($i % 2 === 1 ? "odd" : "even"));

		$i += 1;
	}

	return implode("\n\n", $tweets) . orgr_paginationPage($url, FALSE);
}

function orgr_statusesTimeline($url, $statuses, $no_highlight = FALSE, $no_apparent_rt = FALSE){
	global $dsgn, $msgs;

	if(count($statuses) === 0 || !is_array($statuses)){
		return "<p>{$msgs->msg58524}</p>" . orgr_paginationPage($url, TRUE);
	}

	$tweets = array();

	$i = 0;
	foreach($statuses as $status){
		$tweets[] = orgr_statusBox($status, ($i % 2 === 1 ? "odd" : "even"), $no_highlight, $no_apparent_rt);
		$i += 1;
	}

	return implode("\n\n", $tweets) . orgr_paginationPage($url, FALSE);
}

function orgr_userHeader($user, $relationship){
	global $dsgn, $msgs;
	$key = security_key_create();

	$icon = ($icon = $dsgn->userPicture($user, TRUE)) ? "<span class=\"icon\">\n{$icon}\n</span>\n" : "";

	$name = markup_external_anchor("http://twitter.com/{$user->screen_name}", $user->screen_name);
	if($user->name !== ""){
		$name .= " ({$user->name})";
	}

	$user_type = array();
	if($user->verified){
		$user_type[] = $msgs->msg66761;
	}
	if($user->protected){
		$user_type[] = $msgs->msg84551;
	}
	$user_type = ($user_type = implode("<br>", $user_type)) ? "<strong>\n{$user_type}</strong><br>\n" : "";

	$desc = htmlspecialchars($user->description, ENT_QUOTES, "UTF-8", FALSE);

	$url = count($user->url) > 0 ? markup_external_anchor($user->url) : "";

	$location = count($user->location) > 0 ? markup_external_anchor("http://maps.google.com/?q=" . rawurlencode($user->location), htmlspecialchars($user->location, ENT_QUOTES, "UTF-8", FALSE)) : "";

	$epoch = (int) strtotime($user->created_at);
	$tweet_data = "{$msgs->msg22805}: ". date("r", $epoch) . " (" . format_unit(round($user->statuses_count / ((time() - $epoch) / 86400), 1), $msgs->msg51248) . "/{$msgs->msg36831})";

	$followed_by = $relationship->followed_by ? "<br>\n<strong>{$msgs->msg67761}</strong>" : "";

	$user_actions = array();

	$user_actions[] = format_unit($user->statuses_count, $msgs->msg51248);
	$user_actions[] = "<a href=\"friends/{$user->screen_name}\">{$msgs->msg76010}: {$user->friends_count}</a>";
	$user_actions[] = "<a href=\"followers/{$user->screen_name}\">{$msgs->msg45529}: {$user->followers_count}</a>";
	$user_actions[] = "<a href=\"favourites/{$user->screen_name}\">{$msgs->msg98629}: {$user->favourites_count}</a>";
	$user_actions[] = "<a href=\"lists/{$user->screen_name}/memberships\">{$msgs->msg79736}: {$user->listed_count}</a>";
	if(!is_current_user($user->screen_name)){
		if($relationship->followed_by){
			$user_actions[] = "<a href=\"dm/new/{$user->screen_name}\">{$msgs->msg41827}</a>";
		}
		if($relationship->following){
			$user_actions[] = "<a href=\"unfollow/{$user->screen_name}?key={$key}\">{$msgs->msg44099}</a>";
			$user_actions[] = $relationship->want_retweets ? "<a href=\"friendship/{$user->screen_name}/retweets/0?key={$key}\">{$msgs->msg30205}</a>" : "<a href=\"friendship/{$user->screen_name}/retweets/1?key={$key}\">{$msgs->msg46065}</a>";
		}else{
			$user_actions[] = "<a href=\"follow/{$user->screen_name}?key={$key}\">{$msgs->msg23918}</a>";
		}
	}
	$user_actions[] = "<a href=\"addtolist/{$user->screen_name}\">{$msgs->msg28926}</a>";
	if(is_current_user($user->screen_name)){
		if($user->protected){
			$user_actions[] = "<a href=\"incoming\">{$msgs->msg35389}</a>";
		}
	}else{
		if($relationship->blocking){
			$user_actions[] = "<a href=\"unblock/{$user->screen_name}\">{$msgs->msg13305}</a>";
		}else{
			$user_actions[] = "<a href=\"block/{$user->screen_name}\">{$msgs->msg22781}</a>";
		}
		$user_actions[] = "<a href=\"spam/{$user->screen_name}\">{$msgs->msg14790}</a>";
	}
	$user_actions = implode(" | ", $user_actions);

	return $dsgn->userHeaderMarkup($icon, $name, $user_type, $desc, $url, $location, $tweet_data, $followed_by, $user_actions);
}
