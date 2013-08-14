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

	if(!isset($args[1]) || (string) $args[1] === ""){
		return;
	}
	$screen_name = $args[1];
	$sn_url = rawurlencode($screen_name);
	$sn_html = htmlspecialchars($screen_name, ENT_QUOTES, "UTF-8", FALSE);

	if(confirmation_check()){
		twitter_process("blocks/create.json?screen_name={$sn_url}", TRUE);
		page_refresh("user/{$sn_url}");
		return;
	}else{
		confirmation_lock("block/{$sn_url}", "{$msgs->msg72755}: <strong>{$sn_html}</strong>", TRUE);
		return;
	}
}
