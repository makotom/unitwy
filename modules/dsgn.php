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

class UnitwyDsgn{
	public $commonCss, $commonJs, $htmlHead, $jsCounter, $geoJs, $sessionChooser, $twitterLogin, $accountLogin, $aboutUnitwy, $loginForm;

	public function __construct(){
		global $msgs, $lang_list;

		$client = CLIENT;

		$this->commonStyle = <<<EOD
<style>
body{
	background: #003;
	color: #fff;
	margin: 0;
	font-size: 0.9em;
}
fieldset{
	margin: 8px;
}
table{
	border-collapse: collapse;
}
td{
	vertical-align: middle;
	padding: 3px;
}
form{
	margin: 8px 6px;
}
p, h2, h3{
	margin-left: 5px;
	margin-right: 5px;
}
a{
	color: #fb0;
}
small, small a{
	color: #999;
}
img{
	border: 0;
}



.menu{
	color: #000;
	background: #ccc;
	padding: 2px;
}
.menu > ul{
	display: inline;
	padding: 0;
}
.menu > ul > li{
	display: inline;
}
.menu > ul > li:not(:first-child):before, .menu > ul:not(:first-child) > li:first-child:before{
	content: " | ";
}
.menu a{
	color: #000;
	text-decoration: none;
}
.menu_indicator{
	float: right;
	background-color: inherit;
}

.login{
	padding: 5px;
}
.about_unitwy{
	margin: 2em;
}
.login_lang{
	text-align: right;
}

.status{
	padding: 5px;
}
.user_header{
	padding:12px 5px;
}

.icon{
	display: table-cell;
	padding: 0 2px;
	vertical-align: top;
}
.status_text{
	display: table-cell;
	padding: 0 2px;
	vertical-align: top;
}
.status_text > a > img{
	max-width: 180px;
}

.even{
	background: rgba(255, 255, 255, 0.25);
	color: #fff;
}
.odd{
	background: rgba(221, 221, 221, 0.25);
	color: #fff;
}
.reply.even{
	background: rgba(0, 238, 85, 0.4);
	color: #fff;
}
.reply.odd{
	background: rgba(0, 204, 51, 0.4);
	color: #fff;
}
.mine.even{
	background: rgba(255, 119, 119, 0.4);
	color: #fff;
}
.mine.odd{
	background: rgba(255, 85, 85, 0.4);
	color: #fff;
}

.form_text{
	width: 32em;
	max-width: 80%;
}

ul.preferences{
	padding: 0;
	list-style-type: none;
}
</style>
EOD;

		$this->commonScript = <<<EOD
<script>
(function(){
	var f = function(){
		var rf = function(){ var f = document.getElementById("filtermasque"); if(f !== null){ f.parentNode.removeChild(f); } }, f = document.createElement("div");

		f.id = "filtermasque";
		f.style.position = "fixed";
		f.style.left = "0";
		f.style.top = "0";
		f.style.width = "100%";
		f.style.height = "100%";
		f.style.backgroundColor = "rgba(0, 0, 0, 0.3)";

		window.addEventListener("blur", rf, false);
		window.addEventListener("unload", rf, false);
		f.addEventListener("click", rf, false);

		document.documentElement.appendChild(f);
	};

	addEventListener("click", function(e){ if(e.target.tagName.toLowerCase() === "a" && e.target.target !== "_blank" && e.ctrlKey === false && e.shiftKey === false && e.altKey === false && e.metaKey === false){ f(); } }, false);
	addEventListener("submit", function(e){ if(e.ctrlKey === false && e.shiftKey === false && e.altKey === false && e.metaKey === false){ f(); } }, false);
})();
</script>
EOD;

		$this->htmlHead = "{$this->commonStyle}\n{$this->commonScript}";

		$this->jsCounter = <<<EOD
<script>
window.addEventListener("DOMContentLoaded", function(){
	var text = document.getElementById("text"), textCounter = function(){
		var disp = document.getElementById("text_counter"), count = 140 - text.value.length;

		while(disp.firstChild){
			disp.removeChild(disp.firstChild);
		}
		disp.appendChild(document.createTextNode(count.toString()));

		if(count < 0){
			disp.style.color = "#FF0000";
			disp.style.fontWeight = "bold";
		}else{
			disp.style.color = "inherit";
			disp.style.fontWeight = "normal";
		}
	}, interval;

	textCounter();
	text.addEventListener("focus", function(){ interval = setInterval(textCounter, 100); }, false);
	text.addEventListener("blur", function(){ clearInterval(interval); }, false);
}, false);
</script>
EOD;

		$this->geoJs = <<<EOD
<script>
(function(){
	var label, chk, msg, a;
	if(navigator.geolocation){
		label = document.createElement("label");
		label.id = "geoLabel";

		chk = document.createElement("input");
		chk.id = "geoSw";
		chk.type = "checkbox";
		chk.name = "location";

		label.appendChild(chk);

		label.appendChild(document.createTextNode(" "));

		msg = document.createElement("span");
		msg.id = "geoMsg";
		msg.addMsg = function(msg){
			while(this.firstChild){
				this.removeChild(this.firstChild);
			}
			this.appendChild(document.createTextNode(msg));
		};
		msg.addAnchor = function(href, msg){
			while(this.firstChild){
				this.removeChild(this.firstChild);
			}
			a = document.createElement("a");
			a.href = href;
			a.target = "_blank";
			a.appendChild(document.createTextNode(msg));
			this.appendChild(a);
		};
		msg.addMsg("{$msgs->msg30883}");

		label.appendChild(msg);

		document.getElementById("geolocation").appendChild(label);
	}
})();
document.getElementById("geoSw").addEventListener("click", function(){
	var geoSuccess = function(position){
		document.getElementById("geoMsg").addAnchor("http://maps.google.com/?q=" + position.coords.latitude + "," + position.coords.longitude, "{$msgs->msg30883}");
		document.getElementById("geoSw").value = position.coords.latitude + "," + position.coords.longitude;
	}, geoFail = function(){
		document.getElementById("geoMsg").addMsg("{$msgs->msg87768}");
		document.getElementById("geoSw").checked = 0;
		document.getElementById("geoSw").disabled = 1;
	};
	document.getElementById("geoMsg").addMsg("{$msgs->msg70982}");
	navigator.geolocation.getCurrentPosition(geoSuccess, geoFail);
}, false);
</script>
EOD;

		$this->sessionChooser = ($aliases = user_list_valid_aliases()) ? "<form action=\"./\">\n<b>{$msgs->msg25595}</b>:\n" . markup_select("alias", $aliases) . "\n<input type=\"submit\" value=\"{$msgs->msg97367}\">\n<a href=\"?close_all\">{$msgs->msg99990}</a>\n</form>" : "";

		$this->twitterLogin = <<<EOD
<p style="font-size:1.2em"><a href="?start_oauth"><img src="images/sign-in-with-twitter-d.png" alt="{$msgs->msg61921}"></a></p>
EOD;

		$this->accountLogin .= <<<EOD
<form action="./" method="post">
<input type="hidden" name="client" value="{$client}">
{$msgs->msg49493}:<br>
{$msgs->msg92977} <input name="username" size="15"><br>
{$msgs->msg84133} <input name="password" type="password" size="15"><br>
<input type="submit" value="{$msgs->msg40944}">
</form>
EOD;

		$this->aboutUnitwy .= <<<EOD
<fieldset class="about_unitwy">
<b>{$msgs->msg57003}:</b>
<ul>
<li>{$msgs->msg38234}
<li>{$msgs->msg20406}
<li>{$msgs->msg16618}
<li>{$msgs->msg12527}
<li><b>{$msgs->msg15347}</b>
</ul>
</fieldset>
EOD;

		$this->langSelect = markup_select("lang", $lang_list, (defined("LANG") ? LANG : "en"));

		$this->loginForm = "<div class=\"login\">\n" . (!empty($this->sessionChooser) ? "{$this->sessionChooser}\n\n<p><strong>{$msgs->msg25369}</strong>:</p>\n" : "") . "{$this->twitterLogin}\n" . (KEEP_CREDENTIALS ? "\n{$this->accountLogin}\n" : "") . "\n{$this->aboutUnitwy}\n\n<div class=\"login_lang\">\n<form action=\"./\" method=\"get\">\n{$this->langSelect}\n<input type=\"submit\" value=\"{$msgs->msg43948}\">\n</form>\n</div>\n</div>";

		if(method_exists($this, "__overrideDefaults")){
			$this->__overrideDefaults();
		}

		return;
	}

	public function refreshScript($url, $time, $suffix = ""){
		$time = (($time = (int) $time) && $time !== 0) ? (string) $time * 1000 : (string) ((int) DEF_REF_INT) * 1000;
		return <<<EOD
<script>
setInterval(function(){
	var t = document.getElementById("text");
	if(t.value === t.defaultValue){
		location.href = "{$url}";
	}
}, {$time});
</script>{$suffix}
EOD;
	}

	public function statusForm($text = "", $in_reply_to_id = "", $from = ""){
		global $msgs;

		$key = security_key_create();

		return <<<EOD
<form action="update" method="post">
<input type="hidden" name="key" value="{$key}">
<input type="hidden" name="in_reply_to_id" value="{$in_reply_to_id}">
<input type="hidden" name="from" value="{$from}">
<textarea id="text" class="form_text" name="status" rows="3">{$text}</textarea> <span id="text_counter"></span><br>
<input type="submit" value="{$msgs->msg22815}"> <input type="reset" value="{$msgs->msg33103}"> <span id="geolocation"></span>
</form>
{$this->jsCounter}
{$this->geoJs}
EOD;
	}

	public function dmForm($to = ""){
		global $msgs;

		$key = security_key_create();

		return <<<EOD
<form action="dm/new" method="post">
<input type="hidden" name="key" value="{$key}">
{$msgs->msg43620}: <input name="to" value="{$to}"><br>
<textarea id="text" class="form_text" name="text" rows="3"></textarea> <span id="text_counter"></span><br>
<input type="submit" value="{$msgs->msg22815}">
</form>
{$this->jsCounter}
EOD;
	}

	public function mediaForm($pretext, $in_reply_to_id){
		global $msgs;

		$key = security_key_create();

		return <<<EOD
<form action="media" method="post" enctype="multipart/form-data">
<b>{$msgs->msg79889}</b><br>
<input type="hidden" name="key" value="{$key}">
{$msgs->msg98280} <input type="file" name="media"><br>
{$msgs->msg99713}<br>
<textarea id="message" name="text" class="form_text" rows="3">{$pretext}</textarea><br>
{$msgs->msg80077}
<select name="service">
<option value="twitter">Twitter
<option value="twitpic">Twitpic
<option value="mobypic">MobyPicture
<option value="img.ly">img.ly
</select><br>
<label><input type="checkbox" name="notweet"> {$msgs->msg60380}</label><br>
<input type="hidden" name="in_reply_to_id" value="{$in_reply_to_id}">
<input type="submit" value="{$msgs->msg54277}"> <span id="geolocation"></span><br>
</form>
{$this->jsCounter}
{$this->geoJs}
EOD;
	}

	public function listInfoForm($action, $title, $button, $listName = "", $listMode = "1", $listDesc = ""){
		global $msgs;

		$key = security_key_create();
		$mode = markup_select("mode", array("public" => $msgs->msg51241, "private" => $msgs->msg64051), $listMode);

		return <<<EOD
<form action="{$action}" method="post">
<input type="hidden" name="key" value="{$key}">
{$title}<br>
{$msgs->msg59772}
<input type="text" id="name" name="name" value="{$listName}"><br>
{$msgs->msg69849}
{$mode}<br>
{$msgs->msg97956}<br>
<textarea id="description" class="form_text" name="description" rows="3">{$listDesc}</textarea><br>
<input type="submit" value="{$button}">
</form>
EOD;
	}

	public function searchForm($keyword){
		global $msgs;

		return <<<EOD
<form action="search" method="get">
<input type="text" name="q" value="{$keyword}">
<input type="submit" value="{$msgs->msg31476}">
</form>
EOD;
	}

	public function statusBoxMarkup($class, $icon, $name, $actions, $datetime, $source, $in_reply_to, $text, $retweet){
		return <<<EOD
<div class="status {$class}">
{$icon}<span class="status_text">
<b><a href="user/{$name}" class="username">{$name}</a></b>
{$actions}
<small>
{$datetime}{$source}{$in_reply_to}
</small>
<br>
{$text}{$retweet}
</span>
</div>
EOD;
	}

	public function userBoxMarkup($class, $icon, $screen_name, $full_name, $action, $info){
		return <<<EOD
<div class="status {$class}">
{$icon}<span class="status_text">
<b><a href="user/{$screen_name}" class="username">{$screen_name}</a>{$full_name}</b>
{$action}<br>
<small>
{$info}
</small>
</span>
</div>
EOD;
	}

	public function userHeaderMarkup($icon, $name, $user_type, $desc, $url, $location, $tweet_data, $followed_by, $user_actions){
		global $msgs;

		return <<<EOD
<div class="user_header">
{$icon}<span class="status_text">
<b>{$name}</b><br>
<small>
{$user_type}{$desc}<br>
{$msgs->msg60890}: {$url}<br>
{$msgs->msg73197}: {$location}<br>
{$tweet_data}{$followed_by}
</small>
</span>
<div class="user_actions">{$user_actions}</div>
</div>
EOD;
	}

	public function userPicture($user, $force_large = FALSE){
		$size = $force_large ? 48 : 24;
		$original = preg_replace("/_normal(\.[^.]+)?$/", "$1", $user->profile_image_url);

		return markup_external_img($original, $user->profile_image_url, "icon", $size);
	}

	public function actionIcon($url, $src, $alt){
		if(preg_match("/^https?:\/\//iu", $url)){
			return markup_external_img($url, $src, $alt);
		}else{
			return "<a href=\"{$url}\">" . markup_img($src, $alt) . "</a>";
		}
	}

	public function paginationBox($links){
		return "\n\n<p>\n" . implode("<br>\n", $links) . "\n</p>";
	}

	public function paginationAnchor($url, $label, $key = ""){
		return "<a href=\"{$url}\" title=\"{$label}\" accesskey=\"{$key}\">{$label}</a> {$key}";
	}
}
