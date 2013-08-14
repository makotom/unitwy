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

	if(empty($args[1]) || !is_numeric($args[1])){
		return;
	}
	$id = (string) $args[1];

	$is_reply = 0;
	if(isset($_GET["reply"])){
		$is_reply = $_GET["reply"] === "all" ? 2 : 1;
	}

	$status = twitter_process("statuses/show/{$id}.json?include_entities=1");

	if(array_search(USERNAME, extractor_extract_usernames($status)) !== FALSE){
		notification_mark_mentions($id);
	}

	$pretext = extractor_get_users_str_to_reply($status, $is_reply === 2 ? TRUE : FALSE);

	$content = $is_reply > 0 ? "<p>{$msgs->msg77791} (<a href=\"media/{$id}" . ($is_reply === 2 ? "/all" : "") . "\">{$msgs->msg75804}</a>):</p>\n" : "";
	$content .= $dsgn->statusForm($pretext, $status->id_str) . "\n\n";
	$content .= orgr_statusBox($status, "even", FALSE, TRUE, TRUE);

	$relstats_data = array();
	$relstats_text = array();
	$conv = "";

	$related_results = twitter_process("related_results/show/{$status->id_str}.json?include_entities=1&cout=20", FALSE, TRUE);
	if(!empty($related_results[0]->results)){
		foreach($related_results[0]->results as $related){
			$relstats_data[$related->value->id_str] = $related->value;
		}
	}

	if(!empty($status->in_reply_to_status_id_str) && !isset($relstats_data[$status->in_reply_to_status_id_str])){
		$in_reply_to = twitter_process("statuses/show/{$status->in_reply_to_status_id_str}.json?include_entities=1", FALSE, TRUE);
		if(!empty($in_reply_to->id_str)){
			$relstats_data[$status->in_reply_to_status_id_str] = $in_reply_to;
		}
	}

	if(!empty($relstats_data)){
		$relstats_data[$status->id_str] = $status;
		krsort($relstats_data);

		$i = 0;
		foreach($relstats_data as $status){
			$test = $relstats_data;
			$relstats_text[] = orgr_statusBox($status, ($i % 2 === 1 ? "odd" : "even"));
			$i += 1;
		}

		$conv = implode("\n\n", $relstats_text);
	}

	if(!empty($conv)){
		$content .= "\n\n<p>{$msgs->msg52719}:</p>\n{$conv}";
	}

	output_page(($is_reply > 0 ? $msgs->msg81829 : $msgs->msg33898) . " {$id}", $content);

	return;
}
