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

	if(empty($args[1]) || !is_numeric($args[1])){
		return;
	}
	$id = (string) $args[1];

	if(confirmation_check()){
		$status = twitter_process("statuses/destroy/{$id}.json?include_entities=1", TRUE);

		if(array_search(USERNAME, extractor_extract_usernames($status)) !== FALSE){
			notification_mark_mentions($id);
		}

		page_refresh("");

		return;
	}else{
		confirmation_lock("delete/{$id}", $msgs->msg23382);
		return;
	}
}
