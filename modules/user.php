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

function user_close_sessions($username, $ids, $is_reverse = FALSE){
	$store = new simpleStore(DATA_STORE);

	if(!($sessions = $store->selectEntry("sessions", $username))){
		return FALSE;
	}

	foreach($ids as $id){
		unset($sessions[$id]);
	}

	return (!empty($sessions) ? $store->replaceEntry("sessions", $username, $sessions) : $store->deleteEntry("sessions", $username)) !== FALSE ? $sessions : FALSE;
}

function user_valid_sessions($username){
	$time = time();
	$invalid_ids = array();
	$store = new simpleStore(DATA_STORE);

	if(!($sessions = $store->selectEntry("sessions", $username))){
		return array();
	}

	foreach($sessions as $id => $last_time){
		if($last_time < $time - 3600){
			$invalid_ids[] = $id;
		}
	}

	return user_close_sessions($username, $invalid_ids);;
}

function user_session_is_valid($username){
	$sessions = user_valid_sessions($username);
	return isset($sessions[CLIENT]);
}

function user_save_credentials($token = NULL, $secret = NULL, $user = NULL){
	global $credentials;

	if($token !== NULL){
		$credentials->token = $token;
	}
	if($secret !== NULL){
		$credentials->secret = $secret;
	}

	if($user !== NULL){
		define("USERNAME", (string) $user);

		define("ALIAS", ($alias = array_search(USERNAME, user_list_valid_aliases())) !== FALSE ? (string) $alias : bin2hex(openssl_random_pseudo_bytes(4)));

		$aliases = !empty($_COOKIE["ALIASES"]) ? unserialize(base64_decode($_COOKIE["ALIASES"])) : array();
		$aliases[ALIAS] = USERNAME;
		setcookie("ALIASES", $_COOKIE["ALIAS"] = base64_encode(serialize($aliases)), 0, dirname($_SERVER["SCRIPT_NAME"]));

		$certs = !empty($_COOKIE["CERTS"]) ? unserialize(simple_decrypt($_COOKIE["CERTS"])) : array();
		$certs[USERNAME] = $credentials;
		setcookie("CERTS", $_COOKIE["CERTS"] = simple_encrypt(serialize($certs)), 0, dirname($_SERVER["SCRIPT_NAME"]));

		$store = new simpleStore(DATA_STORE);
		$sessions = $store->selectEntry("sessions", USERNAME);
		$sessions[CLIENT] = time();
		$store->replaceEntry("sessions", USERNAME, $sessions);
	}

	return;
}

function user_authenticate_user(){
	global $dsgn, $msgs;
	$store = new simpleStore(DATA_STORE);

	if(isset($_GET["start_oauth"])){
		require_once "modules/OAuth.php";

		$response = twitter_process("https://api.twitter.com/oauth/request_token", array("oauth_callback" => BASE_URL));
		parse_str($response, $token);

		page_refresh("https://api.twitter.com/oauth/authorize?oauth_token={$token["oauth_token"]}");

		return;
	}

	if(!empty($_GET["oauth_token"])){
		user_save_credentials(secure_urlencode($_GET["oauth_token"]));

		$response = twitter_process("https://api.twitter.com/oauth/access_token", array("oauth_verifier" => $_GET["oauth_verifier"]));
		parse_str($response, $token);
		user_save_credentials($token["oauth_token"], $token["oauth_token_secret"]);

		$user = twitter_process("https://api.twitter.com/1/account/verify_credentials.json");
		user_save_credentials($token["oauth_token"], $token["oauth_token_secret"], $user->screen_name);
	}else if(KEEP_CREDENTIALS && !empty($_POST["client"]) && (string) $_POST["client"] === (string) CLIENT && isset($_POST["username"]) && isset($_POST["password"])){
		if($user = $store->selectEntry("users", $_POST["username"], array("password" => user_pwhash($_POST["password"])))){
			user_save_credentials($user["oauth_token"], simple_decrypt($user["oauth_secret"]), $user["screen_name"]);
		}else{
			output_error("{$msgs->msg51113}");
			return;
		}
	}

	if(defined("ALIAS") && defined("USERNAME")){
		if(KEEP_CREDENTIALS){
			if($user = $store->selectEntry("fb", USERNAME)){
				$fb = new stdClass();
				$fb->id = $user["id"];
				$fb->token = simple_decrypt($user["access_token"]);
				setcookie("FB", simple_encrypt(serialize($fb)), 0, parse_url(BASE_URL, PHP_URL_PATH) . ALIAS . "/");
			}
		}

		page_refresh(ALIAS . "/");

		return;
	}else{
		output_page($msgs->msg27713, $dsgn->loginForm);
		return;
	}

	exit();
}

function user_load_cookie($alias, $load_only = FALSE){
	global $credentials;

	$aliases = !empty($_COOKIE["ALIASES"]) ? unserialize(base64_decode($_COOKIE["ALIASES"])) : array();
	if(empty($aliases[$alias]) || !user_session_is_valid($aliases[$alias])){
		return FALSE;
	}

	$username = (string) $aliases[$alias];

	$certs = !empty($_COOKIE["CERTS"]) ? unserialize(simple_decrypt($_COOKIE["CERTS"])) : array();
	if(empty($certs[$username])){
		return FALSE;
	}
	$credentials = $certs[$username];

	$store = new simpleStore(DATA_STORE);
	$sessions = user_valid_sessions($username);
	$sessions[CLIENT] = time();
	$store->replaceEntry("sessions", $username, $sessions);

	if(!$load_only){
		define("ALIAS", (string) $alias);
		define("USERNAME", (string) $username);
	}

	return TRUE;
}

function user_clear_cookie($clear){
	$aliases = !empty($_COOKIE["ALIASES"]) ? unserialize(base64_decode($_COOKIE["ALIASES"])) : array();
	$store = new simpleStore(DATA_STORE);

	if($clear === TRUE){
		$certs = array();
		foreach($aliases as $username){
			user_close_sessions($username, array(CLIENT));
		}
		$aliases = array();
	}else if(isset($aliases[$clear])){
		$certs = !empty($_COOKIE["CERTS"]) ? unserialize(simple_decrypt($_COOKIE["CERTS"])) : array();
		if(isset($certs[$aliases[$clear]])){
			unset($certs[$aliases[$clear]]);
			setcookie("CERTS", $_COOKIE["CERTS"] = !empty($certs) ? simple_encrypt(serialize($certs)) : FALSE, 0, dirname($_SERVER["SCRIPT_NAME"]));
		}
		user_close_sessions($aliases[$clear], array(CLIENT));
		unset($aliases[$clear]);
	}

	setcookie("ALIASES", $_COOKIE["ALIAS"] = !empty($aliases) ? base64_encode(serialize($aliases)) : FALSE, 0, dirname($_SERVER["SCRIPT_NAME"]));

	return;
}

function user_list_valid_aliases(){
	$aliases = !empty($_COOKIE["ALIASES"]) ? unserialize(base64_decode($_COOKIE["ALIASES"])) : array();
	$store = new simpleStore(DATA_STORE);
	$ret = array();

	foreach($aliases as $alias => $username){
		if(user_session_is_valid($username)){
			$ret[$alias] = $username;
		}
	}

	return $ret;
}

function user_pwhash($password){
	return hash("sha256", $password);
}
