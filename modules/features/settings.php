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
	global $credentials, $dsgn, $msgs, $skin_list;
	$key = security_key_create();

	$content = "";

	$changes = 0;
	if(!empty($_GET["changes"])){
		if(empty($_GET["key"]) || !security_key_check($_GET["key"])){
			return;
		}
		$changes = (int) $_GET["changes"];
	}
	switch($changes){
		case 1:
			set_prefs($_POST);
			break;
		case 2:
			set_prefs(NULL);
			setcookie("LANG", FALSE);
			notification_set_state(NULL);
			break;
		case 3:
			$ids_close = array();
			$sessions = user_valid_sessions(USERNAME);
			foreach($sessions as $id => $time){
				if((string) $id !== (string) CLIENT){
					$ids_close[] = $id;
				}
			}
			user_close_sessions(USERNAME, $ids_close);
			break;
		case 4:
			twitter_process("account/update_profile.json", $_POST);
			break;
		case 5:
			if(isset($_FILES["image"]["error"]) && $_FILES["image"]["error"] === 0){
				$url = "account/update_profile_image.json";
				$boundary = "----------" . uniqid();
				$contentType = "Content-Type: multipart/form-data; boundary=$boundary";
				$data = "--{$boundary}\r\nContent-Disposition: form-data; name=\"image\"; filename=\"{$_FILES['image']['name']}\"\r\nContent-Type: {$_FILES['image']['type']}\r\n\r\n" . file_get_contents($_FILES["image"]["tmp_name"]) . "\r\n--{$boundary}--\r\n";
				twitter_process($url, $data, FALSE, array($contentType));
			}
			break;
		case 6:
			if(KEEP_CREDENTIALS && isset($_POST["password"]) && (string) $_POST["password"] !== ""){
				$store = new simpleStore(DATA_STORE);
				$store->replaceEntry("users", USERNAME, array("screen_name" => USERNAME, "password" => user_pwhash($_POST["password"]), "oauth_token" => $credentials->token, "oauth_secret" => simple_encrypt($credentials->secret)));
				unset($store);
			}
			break;
		case 7:
			$store = new simpleStore(DATA_STORE);
			$store->deleteEntry("users", USERNAME);
			unset($store);
			break;
	}
	if(!empty($changes)){
		page_refresh("settings");
		return;
	}

	$skins = markup_select("skin", $skin_list, get_prefs("skin"));
	$gwt = get_prefs("gwt") ? " checked" : "";
	$timestamp = get_prefs("timestamp") ? " checked" : "";
	$embedding = get_prefs("no_embedding") ? " checked" : "";
	$tz = htmlspecialchars(date_default_timezone_get(), ENT_QUOTES, "UTF-8", FALSE);
	$home_int = (string) intval(get_prefs("home_int", DEF_REF_INT));
	$mentions_int = (string) intval(get_prefs("mentions_int", DEF_REF_INT));
	$dm_int = (string) intval(get_prefs("dm_int", DEF_REF_INT));
	$list_int = (string) intval(get_prefs("list_int", DEF_REF_INT));
	$hash_int = (string) intval(get_prefs("hash_int", DEF_REF_INT));
	$notif_int = (string) intval(get_prefs("notif_int", DEF_REF_INT));

	$user = twitter_process("users/show.json?screen_name=" . secure_urlencode(USERNAME));
	$user_desc = htmlspecialchars($user->description, ENT_QUOTES, "UTF-8", FALSE);

	$image = $dsgn->userPicture($user, TRUE);

	$content .= <<<EOD
<fieldset><legend><b>{$msgs->msg37212}</b></legend>
<form action="settings?changes=1&amp;key={$key}" method="post">
<ul class="preferences">
<li>
{$msgs->msg60763}
{$dsgn->langSelect}
</li>

<li>
{$msgs->msg76645}
{$skins}
</li>

<li><label><input type="checkbox" name="gwt" value="1"{$gwt}> {$msgs->msg46874}</label></li>
<li><label><input type="checkbox" name="timestamp" value="1"{$timestamp}> {$msgs->msg88525}</label></li>
<li><label><input type="checkbox" name="no_embedding" value="1"{$embedding}> {$msgs->msg33119}</label></li>
<li><fieldset><legend><b>{$msgs->msg35387}</b></legend>
<ul class="preferences">
<li><label>{$msgs->msg54057} <input type="text" name="home_int" value="{$home_int}" size="5"></label>
<li><label>{$msgs->msg20828} <input type="text" name="mentions_int" value="{$mentions_int}" size="5"></label>
<li><label>{$msgs->msg93026} <input type="text" name="dm_int" value="{$dm_int}" size="5"></label>
<li><label>{$msgs->msg78625} <input type="text" name="list_int" value="{$list_int}" size="5"></label>
<li><label>{$msgs->msg81446} <input type="text" name="hash_int" value="{$hash_int}" size="5"></label>
</ul>
<small>{$msgs->msg27762}</small>
</fieldset></li>
<li><label>{$msgs->msg32196} <input type="text" name="notif_int" value="{$notif_int}" size="5"></label> <small>{$msgs->msg69008}</small>
<li><label>{$msgs->msg77127} <input type="text" name="tz" value="{$tz}"></label> <small><a href="http://php.net/manual/timezones.php" target="_blank">{$msgs->msg66749}</a></small>
</ul>
<input type="submit" value="{$msgs->msg67168}"><br>
<a href="settings?changes=2&amp;key={$key}">{$msgs->msg39021}</a>
</form>
</fieldset>

<fieldset><legend><b>{$msgs->msg23004}</b></legend>
<p><a href="settings?changes=3&amp;key={$key}">{$msgs->msg99134}</a></p>
</fieldset>

<fieldset><legend><b>{$msgs->msg24652}</b></legend>
<form action="settings?changes=4&amp;key={$key}" method="post">
{$msgs->msg54428} <input type="text" id="name" name="name" value="{$user->name}"><br>
{$msgs->msg21443} <input type="url" id="url" name="url" value="{$user->url}"><br>
{$msgs->msg16052} <input type="text" id="location" name="location" value="{$user->location}"><br>
{$msgs->msg31963}<br>
<textarea id="description" name="description" rows="3" style="width:95%; max-width: 400px;">{$user->description}</textarea><br>
<input type="submit" value="{$msgs->msg67168}">
</form>
</fieldset>

<fieldset><legend><b>{$msgs->msg45845}</b></legend>
<form action="settings?changes=5&amp;key={$key}" method="post" enctype="multipart/form-data">
{$image}<br>
{$msgs->msg66219} <input type="file" name="image"> <input type="submit" value="{$msgs->msg67168}">
</form>
</fieldset>
EOD;

	if(defined("FACEBOOK_ID") && defined("FACEBOOK_SECRET")){
		if(!empty($_COOKIE["FB"])){
			$fb = unserialize(simple_decrypt($_COOKIE["FB"]));
			$fb->id = secure_urlencode($fb->id);
			$fb->token = secure_urlencode($fb->token);

			$me = twitter_process("https://graph.facebook.com/{$fb->id}?access_token={$fb->token}");

			$disp = "<p><strong>" . sprintf("{$msgs->msg67196}", markup_external_anchor("http://www.facebook.com/{$fb->id}", (!empty($me->name) ? htmlspecialchars($me->name, ENT_QUOTES, "UTF-8", FALSE) : $msgs->msg89432))) . "</strong></p>\n<p>\n";

			if(KEEP_CREDENTIALS){
				$disp .= file_exists(DATA_STORE . "/fb/" . md5(USERNAME)) ? "<em>{$msgs->msg48127}</em><br>\n" : "<a href=\"facebook?state={$key}&amp;keep\">{$msgs->msg75701}</a><br>\n";
			}
			$disp .= "<a href=\"facebook?state={$key}&amp;destroy\">{$msgs->msg77087}</a></p>";
		}else{
			$disp = "<p><a href=\"facebook?state={$key}\">{$msgs->msg66752}</a></p>";
		}

		$content .= <<<EOD


<fieldset><legend><b>{$msgs->msg93827}</b></legend>
{$disp}
</fieldset>
EOD;
	}

	if(KEEP_CREDENTIALS){
		$keep_credentials_state = file_exists(DATA_STORE . "/users/" . md5(USERNAME)) ? "<p>\n{$msgs->msg26068}<br>\n<a href=\"settings?changes=7&amp;key={$key}\">{$msgs->msg55886}</a>\n</p>\n" : "";

		$content .= <<<EOD


<fieldset><legend><b>{$msgs->msg97036}</b></legend>
<form action="settings?changes=6&amp;key={$key}" method="post">
{$keep_credentials_state}{$msgs->msg30823} <input type="password" name="password"> <input type="submit" value="{$msgs->msg67168}"><br>
<small><em>{$msgs->msg34199}</em></small>
</form>
</fieldset>
EOD;
	}

	output_page($msgs->msg55928, $content);

	return;
}
