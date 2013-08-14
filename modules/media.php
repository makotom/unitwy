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

/*
media - multimedia contents uploading function
Copyright (C) 2011 Makoto Mizukami.
*/

class MediaPost{
	public $url, $text, $error;

	protected function parseJson($json){
		$this->url = $json->url;
	}

	protected function post(){
		global $credentials, $msgs;

		require_once "modules/OAuth.php";

		$header = array("X-Auth-Service-Provider: https://api.twitter.com/1/account/verify_credentials.json");

		$sha1_method = new OAuthSignatureMethod_HMAC_SHA1();
		$consumer = new OAuthConsumer(OAUTH_CONSUMER_KEY, OAUTH_CONSUMER_SECRET);
		$oauth_token = $credentials->token;
		$oauth_token_secret = $credentials->secret;
		$token = new OAuthConsumer($oauth_token, $oauth_token_secret);
		$signingURL = "https://api.twitter.com/1/account/verify_credentials.json";
		$request = OAuthRequest::from_consumer_and_token($consumer, $token, "GET", $signingURL);
		$request->sign_request($sha1_method, $consumer, $token);

		$header[] = "X-Verify-Credentials-".$request->to_header("http://api.twitter.com/");

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch,CURLOPT_URL, $this->api);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $this->mediaData);

		$result = curl_exec($ch);
		$response_info = curl_getinfo($ch);
		$json = json_decode($result);

		curl_close($ch);

		if($response_info["http_code"] !== 200){
			$error = $json->errors[0]->code ? $json->errors[0]->code : $response_info["http_code"];
			$message = $json->errors[0]->message ? $json->errors[0]->message : "";
			$this->error = "<h2>{$msgs->msg44509}</h2><p>{$error}: {$message}</p>";
			return;
		}

		$this->parseJson($json);
		return;
	}

	public function __construct($media, $text){
		if(!method_exists($this, "constructMediaData") || !isset($this->api)){
			return;
		}
		$this->mediaData = $this->constructMediaData($media, $text);
		$this->text = $text;
		$this->post();

		return;
	}
}

class PostTwitterMedia{
	public $url, $media, $text;

	public function __construct($media, $text){
		$response = twitter_post($text, $media);
		$this->url = $response->entities->media[0]->media_url;
	}
}

class PostTwitpic extends MediaPost{
	protected $api = "http://api.twitpic.com/2/upload.json";

	protected function constructMediaData($media, $text){
		return array("media" => $media, "message" => $text, "key" => TWITPIC_KEY);
	}
}

class PostMobypic extends MediaPost{
	protected $api = "https://api.mobypicture.com/2.0/upload.json";

	protected function parseJson($json){
		$this->url = $json->media->mediaurl;
		return;
	}

	protected function constructMediaData($media, $text){
		return array("media" => $media, "message" => $text, "key" => MOBYPIC_KEY);
	}
}

class PostImgly extends MediaPost{
	protected $api = "http://img.ly/api/2/upload.json";

	protected function constructMediaData($media, $text){
		return array("media" => $media, "message" => $text);
	}
}

function media_exec($media, $text){
	$media = "@{$media}";
	$text = preg_replace("/^@/", " @", $text);

	switch($_POST["service"]){
		default:
		case "twitter":
			return new PostTwitterMedia($media, $text);
		case "twitpic":
			return new PostTwitpic($media, $text);
		case "mobypic":
			return new PostMobypic($media, $text);
		case "img.ly":
			return new PostImgly($media, $text);
	}

	return;
}
