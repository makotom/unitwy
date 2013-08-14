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

if(!isset($argv[1]) || !is_file($argv[1])){
	exit(1);
}

$msgs_global = file_get_contents($argv[1]);
$msgs_local_new = preg_replace("/^\s*class UnitwyMsgs\s*{/mu", "class UnitwyMsgsLocal extends UnitwyMsgs{", preg_replace("/^(\t*)public \\\$(msg\d{5}) = ([^\n]+);$/mu", "$1// $2 $3\n$1// public \$$2 = \"\";", $msgs_global));

if(isset($argv[2])){
	if(!is_file($argv[2])){
		exit(1);
	}

	class UnitwyMsgs{
	}

	require($argv[2]);
	$msgs_local_org_file = file_get_contents($argv[2]);
	$msgs_local_org = new UnitwyMsgsLocal();

	preg_match_all("/^\t*public \\\$(msg\d{5}) = ([^\n]+);$/mu", $msgs_global, $matches, PREG_SET_ORDER);

	foreach($matches as $match){
		$msgid = preg_quote($match[1], "/");
		$msg = preg_quote($match[2], "/");

		if(isset($msgs_local_org->$msgid)){
			$msgs_local_new = preg_replace("/\/\/ public \\\${$msgid} = \"\";/", "public \${$msgid} = \"{$msgs_local_org->$msgid}\";", $msgs_local_new);

			if(!preg_match("/\/\/ {$msgid} {$msg}$/mu", $msgs_local_org_file)){
				$msgs_local_new = preg_replace("/public \\\${$msgid}/", "public \$fuz_{$msgid}", $msgs_local_new);
			}

			continue;
		}

		$fuzid = "fuz_{$msgid}";
		if(isset($msgs_local_org->$fuzid)){
			$msgs_local_new = preg_replace("/\/\/ public \\\${$msgid} = \"\";/", "public \$fuz_{$msgid} = \"" . str_replace("\"", "\\\"", $msgs_local_org->$fuzid) . "\";", $msgs_local_new);
		}
	}
}

echo $msgs_local_new;
?>
