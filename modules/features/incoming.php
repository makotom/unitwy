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

	if(!empty($args[1]) && isset($args[2]) && $args[2] !== ""){
		switch($args[1]){
			case "accept":
				twitter_process("friendships/accept.json", array("screen_name" => $args[2]));
				page_refresh("followers");
				return;
			case "deny":
				twitter_process("friendships/deny.json", array("screen_name" => $args[2]));
				page_refresh("incoming");
				return;
		}
		return;
	}else{
		output_page($msgs->msg36971, "<p>{$msgs->msg56138}</p>\n\n" . orgr_incomingUsersList(twitter_get_friendships("friendships/incoming.json")));
		return;
	}
}
