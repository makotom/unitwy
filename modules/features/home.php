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

	$page = !empty($_GET["page"]) && ($page = intval($_GET["page"])) ? $page : "1";

	$content = "";

	$refresh_int = (int) get_prefs("home_int", DEF_REF_INT);
	if($refresh_int !== 0){
		$content .= $dsgn->refreshScript("./", $refresh_int, "\n\n");
	}

	$content .= $dsgn->statusForm() . "\n\n";
	$content .= orgr_statusesTimeline("", twitter_process("statuses/home_timeline.json?include_entities=1&count=20&page={$page}"));

	output_page($msgs->msg54057, $content);

	return;
}
