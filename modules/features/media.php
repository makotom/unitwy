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
	$show_form = FALSE;

	if(isset($_FILES["media"]["error"]) && $_FILES["media"]["error"] === 0){
		if(empty($_POST["key"]) || !security_key_check($_POST["key"])){
			return;
		}

		$postInfo = media_exec($_FILES["media"]["tmp_name"], $_POST["text"]);

		if(!is_object($postInfo)){
			$show_form = TRUE;
		}else if(!isset($postInfo->error)){
			$content = "<p>{$msgs->msg28139}";
			if($_POST["service"] === "twitter" || (empty($_POST["notweet"]) && twitter_post("{$postInfo->text} {$postInfo->url}"))){
				$content .= " {$msgs->msg52215}";
			}
			$content .= "</p>\n";

			$content .= "<p>" . thumbnailer_embed_thumbnails(array($postInfo->url)) . "</p>";
		}else{
			$content = $postInfo->error;
		}
	}else{
		$show_form = TRUE;
	}

	if($show_form){
		$pretext = "";
		$in_reply_to_id = "";
		if(!empty($args[1]) && is_numeric($args[1])){
			$status = twitter_process("statuses/show/{$args[1]}.json?include_entities=1");
			$pretext = extractor_get_users_str_to_reply($status, isset($args[2]) && $args[2] === "all" ? TRUE : FALSE);
			$in_reply_to_id = $args[1];
		}

		$content = $dsgn->mediaForm($pretext, $in_reply_to_id);
	}

	output_page($msgs->msg19279, $content);

	return;
}
