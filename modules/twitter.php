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

function twitter_json_ent($var){
	if(is_object($var)){
		foreach($var as $key => $value){
			if($key !== "source"){
				$var->{$key} = twitter_json_ent($value);
			}else{
				$var->{$key} = $value;
			}
		}
		return $var;
	}

	if(is_array($var)){
		foreach($var as $key => $value){
			$var[$key] = twitter_json_ent($value);
		}
		return $var;
	}

	if(is_string($var)){
		return htmlspecialchars($var, ENT_QUOTES, "UTF-8", FALSE);
	}else{
		return $var;
	}
}

function twitter_process($url, $post_data = FALSE, $ignore_errors = FALSE, $headers = array()){
	global $api_time, $msgs;

	if(!preg_match("/^https?:/", $url)){
		$url = API_URL . $url;
	}
	if($post_data === TRUE){
		$post_data = array();
	}
	if(!is_array($headers)){
		$headers = array();
	}
	$headers[] = "Expect:";

	if(preg_match("/^https?:\/\/[^.]+\.twitter\.com/", $url)){
		$headers[] = simple_oauth_sign($url, $post_data !== FALSE ? TRUE : FALSE);
		if(is_array($post_data)){
			foreach($post_data as $key => $value){
				if($key !== "media[]"){
					$post_data[$key] = preg_replace("/^@/", " @", $value);
				}
			}
		}
	}

	$api_start = microtime(1);
	$ch = curl_init($url);
	if($post_data !== FALSE){
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
	}
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_TIMEOUT, CURL_TIMEOUT);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_HEADER, FALSE);

	$response = curl_exec($ch);
	$response_info = curl_getinfo($ch);
	$erno = curl_errno($ch);
	$er = curl_error($ch);
	curl_close($ch);
	$api_time += microtime(1) - $api_start;

	$json = json_decode($response);

	switch(intval($response_info["http_code"])){
		case 200:
		case 201:
			if(is_array($json) || is_object($json)){
				return twitter_json_ent($json);
			}
			return $response;
		case 0:
			if($ignore_errors){
				return;
			}
			output_error("<strong>{$msgs->msg59543}</strong><br>\n<b>{$erno}</b>: {$er}");
			return;
		default:
			if($ignore_errors){
				return;
			}

			$result = $msgs->msg26577;
			$debug = "";
			if($json !== NULL){
				if(!empty($json->error)){
					if(is_string($json->error)){
						$result = $json->error;
					}else if(!empty($json->error->message)){
						$result = $json->error->message;
					}
				}else if(!empty($json->errors)){
					if(isset($json->errors[0]->message)){
						$result = $json->errors[0]->message;
					}else{
						$errors = explode("\n", $json->errors, 2);
						$result = $errors[0] . (!empty($errors[1]) ? "<!--\n{$errors[1]}\n-->" : "");
					}
				}
			}else{
				ob_start();
				var_dump($response);
				$response = ob_get_contents();
				ob_end_clean();
				$debug = "\n<!--\n" . htmlspecialchars($response, ENT_QUOTES, "UTF-8", FALSE) . "-->";
			}

			output_error("<strong>{$msgs->msg66113}</strong><br>\n<b>{$response_info["http_code"]}</b>: {$result}<br>\n<code>{$url}</code>{$debug}");

			return;
	}
}

function twitter_cursor_paginated_process($url){
	$cursor = isset($_GET["cursor"]) ? $_GET["cursor"] : "";
	if(!is_numeric($cursor)){
		$cursor = -1;
	}
	$url = add_query($url, "cursor", $cursor);
	return twitter_process($url);
}

function twitter_get_friendships($url){
	$upp = 20;

	$page = !empty($_GET["page"]) && ($page = intval($_GET["page"])) ? $page : 1;

	$ids_raw = twitter_process($url);
	$ids = array_slice(isset($ids_raw->ids) ? $ids_raw->ids : $ids_raw, ($page - 1) * $upp, $upp);
	$friendships_raw = !empty($ids) ? twitter_process("users/lookup.json?user_id=" . implode(",", $ids) . "&include_entities=1") : array();

	$friendships = array();
	foreach($friendships_raw as $one){
		$id_key = array_search($one->id, $ids);
		$friendships[$id_key] = $one;
		unset($ids[$id_key]);
	}
	foreach($ids as $id_key => $id){
		$friendships[$id_key] = NULL;
	}
	ksort($friendships);

	return $friendships;
}

function twitter_post($status = "", $media = NULL){
	$api = "statuses/update.json";

	if((string) $status === "" && empty($media)){
		page_refresh();
		return;
	}
	$post_data = array("status" => $status);

	if(!empty($media)){
		$api = "https://upload.twitter.com/1/statuses/update_with_media.json";
		$post_data["media[]"] = $media;
	}

	$in_reply_to_id = (string) $_POST["in_reply_to_id"];
	if(is_numeric($in_reply_to_id)){
		$post_data["in_reply_to_status_id"] = $in_reply_to_id;
	}

	if(!empty($_POST["location"])){
		$latlon = explode(",", $_POST["location"]);
		if(is_numeric($latlon[0]) && is_numeric($latlon[1])){
			$post_data["lat"] = $latlon[0];
			$post_data["long"] = $latlon[1];
		}
	}

	$resp = twitter_process($api, $post_data);

	if(!empty($_COOKIE["FB"]) && !preg_match("/^\s*@/", $status)){
		$fb = unserialize(simple_decrypt($_COOKIE["FB"]));
		twitter_process("https://graph.facebook.com/" . secure_urlencode($fb->id) . "/feed", array("access_token" => $fb->token, "message" => $status));
	}

	return $resp;
}

function twitter_search($search_query){
	$api = "http://search.twitter.com/search.json?q=" . secure_urlencode($search_query) . "&include_entities=1&rpp=20&result_type=recent";
	if(!empty($_GET["page"]) && is_numeric($_GET["page"])){
		$api = add_query($api, "page", $_GET["page"]);
	}
	return twitter_process($api);
}
