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

/* PHP config */
ini_set("post_max_size", "20M");
ini_set("upload_max_filesize", "20M");
ini_set("magic_quotes_gpc", FALSE);
ini_set("mbstring.internal_encoding", "UTF-8");

/* Global variables initializing */
$api_time = 0;
$credentials = NULL;
$dsgn = NULL;
$msgs = NULL;

/* Fixed Parameters */
define("API_URL", "https://api.twitter.com/1/");
define("CURL_TIMEOUT", 30);
define("DATA_STORE", "data");
define("DEF_REF_INT", 180);
define("T_WIDTH", 180);
$lang_list = array(
	"en" => "en - English",
	"ja" => "ja - 日本語",
);
$skin_list = array(
	"default" => "Default",
	"jsfree" => "JS Free",
);

/* Common funcions */
function get_prefs($param, $default = NULL){
	$prefs = !empty($_COOKIE["PREFS"]) ? (array) unserialize(base64_decode($_COOKIE["PREFS"])) : array();
	return defined("USERNAME") && isset($prefs[USERNAME][$param]) ? $prefs[USERNAME][$param] : $default;
}

function set_prefs($new_prefs){
	$prefs = !empty($_COOKIE["PREFS"]) ? (array) unserialize(base64_decode($_COOKIE["PREFS"])) : array();

	if(empty($new_prefs)){
		unset($prefs[USERNAME]);
	}else if(!empty($prefs[USERNAME])){
		$prefs[USERNAME] = array_merge($prefs[USERNAME], $new_prefs);
	}else{
		$prefs[USERNAME] = $new_prefs;
	}

	$prefs_cookie = base64_encode(serialize($prefs));
	setcookie("PREFS", !empty($prefs) ? $prefs_cookie : FALSE, time() + (3600 * 24 * 365), dirname($_SERVER["SCRIPT_NAME"]));
	$_COOKIE["PREFS"] = $prefs_cookie;

	return TRUE;
}

function get_locale_tz($addr){
	if(!function_exists("geoip_db_avail") || !geoip_db_avail(GEOIP_CITY_EDITION_REV0) || strlen(inet_pton($_SERVER["REMOTE_ADDR"])) !== 4){
		return FALSE;
	}

	$record = geoip_record_by_name($addr);
	if(empty($record)){
		return FALSE;
	}

	return geoip_time_zone_by_country_and_region($record["country_code"], $record["region"]);
}

function build_msgs(){
	global $msgs;

	if(!empty($_GET["lang"])){
		$lang_pref = $_GET["lang"];
	}else if(!empty($_POST["language"])){
		$lang_pref = $_POST["language"];
	}else if(get_prefs("language") !== NULL){
		$lang_pref = get_prefs("language");
	}else if(!empty($_COOKIE["LANG"])){
		$lang_pref = $_COOKIE["LANG"];
	}

	if(!empty($lang_pref)){
		$lang_pref = secure_urlencode($lang_pref);

		if(empty($_COOKIE["LANG"]) || $lang_pref !== $_COOKIE["LANG"]){
			setcookie("LANG", $lang_pref, time() + (3600 * 24 * 365));			
		}

		if(is_file("modules/lang/{$lang_pref}.php")){
			require_once "modules/lang/{$lang_pref}.php";
			$msgs = new UnitwyMsgsLocal();
			define("LANG", $lang_pref);
			return;
		}
	}

	if(!empty($_SERVER["HTTP_ACCEPT_LANGUAGE"])){
		$langs = explode(",", $_SERVER["HTTP_ACCEPT_LANGUAGE"]);
		foreach($langs as $lang){
			$lang = rawurlencode(preg_replace("/;.*$/", "", $lang));
			if(is_file("modules/lang/{$lang}.php")){
				require_once "modules/lang/{$lang}.php";
				$msgs = new UnitwyMsgsLocal();
				define("LANG", $lang);
				return;
			}
		}
	}

	$msgs = new UnitwyMsgs();
	define("LANG", "en");

	return;
}

function build_dsgn(){
	global $dsgn;

	$skin_pref = rawurlencode(!empty($_POST["skin"]) ? $_POST["skin"] : get_prefs("skin"));

	if(isset($skin_pref)){
		if(is_file("modules/skin/{$skin_pref}.php")){
			require_once "modules/skin/{$skin_pref}.php";
			$dsgn = new UnitwyDsgnCustom();
			return;
		}
	}

	$dsgn = new UnitwyDsgn();

	return;
}

function page_refresh($page = NULL){
	if($page !== NULL){
		$page = !preg_match("/^https?:\/\//", $page) ? BASE_URL . $page : $page;
	}else{
		$page = !empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : BASE_URL;
	}
	header("Location: " . $page);
	exit();
}

function output_page($title, $content, $no_notification = 0){
	global $dsgn, $unitwy_start, $api_time;

	$lang = LANG;
	$menu_top = get_menu(FALSE, $no_notification) . "\n";
	$content .= "\n";
	$menu_bottom = get_menu(TRUE, $no_notification);

	if(PRINT_DEBUG){
		$time = microtime(1) - $unitwy_start;
		$debug = "real: " . round($time, 4) . " s\napi: " . round($api_time, 4) . " s\nsys: " . round($time - $api_time, 4) . " s";
	}
	$debug = !empty($debug) ? "\n<!--\n{$debug}\n-->" : "";

	$base = BASE_URL;

	ob_start(/*"ob_gzhandler"*/);

	header("Content-Type: text/html; charset=utf-8");

	echo <<<EOD
<!DOCTYPE html>{$debug}
<html lang="{$lang}">
<head>
<meta charset="UTF-8">
<title>{$title} - Unitwy ({$_SERVER["SERVER_NAME"]})</title>
<meta name="viewport" content="width=device-width; initial-scale=1.0;">
<base href="{$base}">
{$dsgn->htmlHead}
</head>

<body>
{$menu_top}
{$content}
{$menu_bottom}
</body>
</html>
EOD;

	ob_end_flush();

	return;
}

function output_error($message){
	global $msgs;

	output_page($msgs->msg12748, "<h2>{$msgs->msg12748}</h2>\n<p>\n{$message}\n</p>");

	exit();
}

function simple_decrypt($data){
	$td = mcrypt_module_open("rijndael-128", "", "ctr", "");
	$ivsize = mcrypt_enc_get_iv_size($td);

	$data = base64_decode($data);
	$iv = substr($data, 0, $ivsize);
	$cipher = substr($data, $ivsize);

	mcrypt_generic_init($td, ENCRYPTION_KEY, $iv);
	$text = mdecrypt_generic($td, $cipher);
	mcrypt_generic_deinit($td);

	mcrypt_module_close($td);

	return $text;
}

function simple_encrypt($text){
	$td = mcrypt_module_open("rijndael-128", "", "ctr", "");
	$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);

	mcrypt_generic_init($td, ENCRYPTION_KEY, $iv);
	$cipher = mcrypt_generic($td, $text);
	mcrypt_generic_deinit($td);

	mcrypt_module_close($td);

	return base64_encode($iv . $cipher);
}

function simple_oauth_sign(&$url, $is_post = FALSE){
	global $credentials;

	require_once "modules/OAuth.php";

	$sig_method = new OAuthSignatureMethod_HMAC_SHA1();
	$consumer = new OAuthConsumer(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
	$token = new OAuthConsumer(!empty($credentials->token) ? $credentials->token : NULL, !empty($credentials->secret) ? $credentials->secret : NULL);
	$request = OAuthRequest::from_consumer_and_token($consumer, $token, ($is_post ? "POST" : "GET"), $url);
	$request->sign_request($sig_method, $consumer, $token);

	return $request->to_header();
}

function security_key_check($key){
	global $credentials;

	return $key === hash("sha256", ALIAS . "=" . serialize($credentials));
}

function security_key_create(){
	global $credentials;

	return hash("sha256", ALIAS . "=" . serialize($credentials));
}

function confirmation_lock($action, $msg, $add_sure_msg = FALSE){
	global $msgs;

	$action = htmlspecialchars($action, ENT_QUOTES, "UTF-8", FALSE);
	$back = htmlspecialchars(!empty($_SERVER["HTTP_REFERER"]) ? $_SERVER["HTTP_REFERER"] : BASE_URL, ENT_QUOTES, "UTF-8", FALSE);
	$key = security_key_create();
	$msg .= $add_sure_msg ? "<br>\n{$msgs->msg33209}" : "";

	$content = <<<EOD
<form action="{$action}" method="post">
<input type="hidden" name="key" value="{$key}">
<input type="hidden" name="back" value="{$back}">
{$msg}<br>
<input type="submit" value="{$msgs->msg82562}"><br>
<a href="{$back}">{$msgs->msg54650}</a>
</form>

EOD;

	output_page("Confirmation", $content);

	return;
}

function confirmation_check(){
	if(!empty($_POST["key"]) && security_key_check($_POST["key"])){
		return TRUE;
	}else{
		return FALSE;
	}
}

function add_query($url, $name, $value){
	$url_part = explode("?", $url, 2);
	parse_str(isset($url_part[1]) ? $url_part[1] : "", $strs);
	$strs[$name] = $value;
	$str = http_build_query($strs);
	return "{$url_part[0]}?{$str}";
}

function build_path_from_args($args){
	$encoded_args = array();
	foreach($args as $arg){
		$encoded_args[] = secure_urlencode($arg);
	}
	return implode("/", $encoded_args);
}

function format_unit($count, $word){
	global $msgs;

	return "{$count} {$word}" . ((float) $count > 1 ? $msgs->msg12117 : "");
}

function get_menu($is_bottom = FALSE, $no_notification = 0){
	global $msgs;

	$menu = $is_bottom ? "bottom" : "top";
	$menu_items = array(
		$msgs->msg54057 => array(0, "./", TRUE),
		$msgs->msg20828 => array(1, "mentions"),
		$msgs->msg62014 => array(2, "dm"),
		$msgs->msg57807 => array(3, "favourites"),
		$msgs->msg38791 => array(4, "friends"),
		$msgs->msg71717 => array(5, "followers"),
		$msgs->msg63089 => array(6, "lists"),
		$msgs->msg19279 => array(NULL, "media"),
		$msgs->msg91602 => array(NULL, "search"),
		$msgs->msg84032 => array(NULL, "trends"),
		$msgs->msg55928 => array(NULL, "settings"),
		$msgs->msg48620 => array(NULL, "../"),
		$msgs->msg57858 => array(8, "logout"),
	);

	$links = array();
	foreach($menu_items as $title => $meta){
		if(!defined("USERNAME") && empty($meta[2])){
			continue;
		}

		$url = BASE_URL . isset($meta[1]) ? $meta[1] : $title;

		if($is_bottom && is_numeric($meta[0])){
			$links[] = "<li><a href=\"$url\" title=\"$title\" accesskey=\"{$meta[0]}\">$title</a> {$meta[0]}";
		}else{
			$links[] = "<li><a href=\"$url\" title=\"$title\">$title</a>";
		}
	}
	$links = "<ul>\n" . implode("\n", $links) . "\n</ul>";

	$notification = "";
	$clock = date("H:i");

	if(defined("USERNAME")){
		$links = "<b><a href=\"user/". secure_urlencode(USERNAME) . "\">" . htmlspecialchars(USERNAME, ENT_QUOTES, "UTF-8", FALSE) . "</a></b>\n" . $links;

		notification_check($no_notification);
		$notification = notification_indicate();
	}

	return <<<EOD
<div class="menu menu-{$menu}">
{$links}
<span class="menu_indicator">{$notification}{$clock}</span>
</div>
EOD;
}

function is_current_user($username){
	return (defined("USERNAME") && strcasecmp($username, USERNAME) === 0);
}

function secure_urlencode($str){
	if(preg_match("/[^A-Za-z0-9\-_.~%]/", $str)){
		return rawurlencode($str);
	}
	return $str;
}

function strnumcmp($str1, $str2){
	if(!is_numeric($str1) || !is_numeric($str2)){
		return FALSE;
	}

	$str1 = (string) $str1;
	$str2 = (string) $str2;

	$l1 = strlen($str1);
	$l2 = strlen($str2);

	if($l1 > $l2){
		return 1;
	}
	if($l1 < $l2){
		return -1;
	}

	for($i = 0; $i < $l1; $i += 1){
		$n1 = (int) $str1[$i];
		$n2 = (int) $str2[$i];

		if($n1 > $n2){
			return 1;
		}
		if($n1 < $n2){
			return -1;
		}
	}

	return 0;
}

function main(){
	global $msgs;

	if(!isset($_COOKIE["CLIENT"])){
		setcookie("CLIENT", $_COOKIE["CLIENT"] = bin2hex(openssl_random_pseudo_bytes(4)), 0, dirname($_SERVER["SCRIPT_NAME"]));
	}
	define("CLIENT", $_COOKIE["CLIENT"]);

	if(!file_exists("modules/key.dat") || strlen(($encryption_key = file_get_contents("modules/key.dat"))) !== 32){
		if(!touch("modules/key.dat")){
			header("Content-Type: text/plain; charset=UTF-8");
			exit("Could not touch an encryption key file. Please check the permission.");
		}
		chmod("modules/key.dat", 0600);

		$encryption_key = openssl_random_pseudo_bytes(32, $key_is_secure);
		if(!$key_is_secure){
			header("Content-Type: text/plain; charset=UTF-8");
			exit("Could not create a secure encryption key. Please create it manually.");
		}

		file_put_contents("modules/key.dat", $encryption_key);
		chmod("modules/key.dat", 0400);
	}
	define("ENCRYPTION_KEY", $encryption_key);

	$topdir = defined("TOP_URL") ? TOP_URL : "http" . (!empty($_SERVER["HTTPS"]) ? "s" : "") . "://{$_SERVER["HTTP_HOST"]}/" . (($subdir = trim(dirname($_SERVER["SCRIPT_NAME"]), "/\,")) ? "{$subdir}/" : "");

	if(isset($_GET["alias"]) && $_GET["alias"] !== ""){
		page_refresh("{$topdir}" . rawurlencode($_GET["alias"]) . "/");
		return;
	}

	if(isset($_GET["close_all"])){
		user_clear_cookie(TRUE);
		page_refresh($topdir);
		return;
	}

	$args = isset($_GET["query"]) ? explode("/", $_GET["query"]) : array();

	if(isset($_GET["un"]) && $_GET["un"] !== "" && ($alias = array_search($_GET["un"], user_list_valid_aliases())) !== FALSE){
		array_unshift($args, rawurlencode($alias));
		unset($_GET["un"]);
		unset($_GET["query"]);
		page_refresh($topdir . build_path_from_args($args) . (!empty($_GET) ? ("?" . http_build_query($_GET)) : ""));
		return;
	}

	user_load_cookie(isset($args[0]) ? $args[0] : "");
	array_shift($args);

	if(($tz = get_prefs("tz")) !== NULL || ($tz = get_locale_tz($_SERVER["REMOTE_ADDR"])) !== FALSE){
		date_default_timezone_set($tz);
	}

	build_msgs();
	build_dsgn();

	if(!defined("ALIAS")){
		define("BASE_URL", $topdir);
		user_authenticate_user();
		return;
	}

	define("BASE_URL", $topdir . ALIAS . "/");

	$feature = !empty($args[0]) ? rawurlencode($args[0]) : "home";
	if(!is_file("modules/features/{$feature}.php")){
		output_error($msgs->msg62619);
		return;
	}
	require_once "modules/features/{$feature}.php";

	unitwy_feature($args);

	return;
}
