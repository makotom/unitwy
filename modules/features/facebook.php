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
	$store = new simpleStore(DATA_STORE);

	if(!defined("FACEBOOK_ID") || !defined("FACEBOOK_SECRET")){
		return;
	}

	if(empty($_GET["state"]) || !security_key_check($_GET["state"])){
		return;
	}

	if(isset($_GET["destroy"])){
		setcookie("FB", FALSE);

		if(KEEP_CREDENTIALS){
			$store->deleteEntry("fb", USERNAME);
		}

		page_refresh("settings");

		return;
	}else if(isset($_GET["keep"])){
		if(!empty($_COOKIE["FB"]) && KEEP_CREDENTIALS){
			$fb = unserialize(simple_decrypt($_COOKIE["FB"]));
			$store->replaceEntry("fb", USERNAME, array("id" => $fb->id, "access_token" => simple_encrypt($fb->token)));
		}

		page_refresh("settings");

		return;
	}else if(!empty($_GET["error"])){
		output_error($_GET["error_reason"] === "user_denied" ? $msgs->msg13704 : $msgs->msg38735);
		return;
	}else if(!empty($_GET["code"])){
		$code = secure_urlencode($_GET["code"]);

		parse_str(file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=" . FACEBOOK_ID . "&client_secret=" . FACEBOOK_SECRET . "&redirect_uri=" . BASE_URL . "facebook&state={$key}&code={$_GET["code"]}"), $params);
		if(!empty($params["access_token"])){
			$token_tmp = $params["access_token"];

			$me = twitter_process("https://graph.facebook.com/me?access_token={$token_tmp}");
			if(empty($me->id)){
				output_error("<p>{$msgs->msg38735}</p>");
				return;
			}

			$fb = new stdClass();
			$fb->id = $me->id;

			parse_str(file_get_contents("https://graph.facebook.com/oauth/access_token?client_id=" . FACEBOOK_ID . "&client_secret=" . FACEBOOK_SECRET . "&grant_type=client_credentials&redirect_uri=" . BASE_URL . "facebook&state={$key}&code={$_GET["code"]}"), $params);
			if(!empty($params["access_token"])){
				$fb->token = $params["access_token"];
				setcookie("FB", simple_encrypt(serialize($fb)));
			}

			page_refresh("https://www.facebook.com/logout.php?access_token={$token_tmp}&next=" . BASE_URL . "settings");

			return;
		}else{
			output_error("<p>{$msgs->msg38735}</p>");
			return;
		}
		return;
	}else{
		page_refresh("https://www.facebook.com/dialog/oauth?client_id=" . FACEBOOK_ID . "&redirect_uri=" . BASE_URL . "facebook&state={$key}&scope=publish_stream");
		return;
	}
}
