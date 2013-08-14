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
	$woeid = isset($_GET["woeid"]) && (string) $_GET["woeid"] !== "" ? intval($_GET["woeid"]) : "1";

	$available = twitter_process("trends/available.json");
	$woeids = array();
	foreach($available as $one){
		$woeids[$one->woeid] = $one->name;
	}
	ksort($woeids);
	$woeids = markup_select("woeid", $woeids, $woeid);

	$trends = twitter_process("trends/" . $woeid . ".json");
	$rows = array();
	foreach($trends[0]->trends as $trend){
		$trend_query = secure_urlencode(htmlspecialchars($trend->name, ENT_QUOTES, "UTF-8", FALSE));
		$rows[] = array("<strong><a href=\"search?q={$trend_query}\">{$trend->name}</a></strong>");
	}
	$trends_table = markup_table($rows);

	$content = <<<EOD
<form action="trends" method="get">
{$woeids}
<input type="submit" value="{$msgs->msg77884}">
</form>

{$trends_table}
EOD;

	output_page($msgs->msg84032, $content);

	return;
}
