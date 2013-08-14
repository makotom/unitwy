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

function markup_options($options, $selected = NULL, $value_is_label = FALSE, $recurse = 0){
	if(count($options) === 0){
		return "";
	}

	$output = "";
	foreach($options as $value => $name){
		if(is_array($name) && $recurse < 20){
			$output .= "<optgroup label=\"{$value}\">\n";
			$output .= markup_options($name, $selected, $value_is_label, $recurse + 1);
			$output .= "</optgroup>\n";
		}else{
			if($value_is_label){
				$value = $name;
			}
			$output .= "<option value=\"{$value}\"" . ((string) $selected === (string) $value ? " selected" : "") . ">{$name}</option>\n";
		}
	}
	return $output;
}

function markup_select($name, $options, $selected = NULL, $value_is_label = FALSE){
	return "<select name=\"$name\">\n" . markup_options($options, $selected, $value_is_label) . "</select>";
}

function markup_tr($row, $class, $is_header = FALSE){
	$row_body = "";
	if(is_array($row)){
		foreach($row as $cell){
			$tagname = $is_header ? "th" : "td";
			$row_body .= "<{$tagname}>$cell</{$tagname}>";
		}
	}
	return "<tr class=\"{$class}\">{$row_body}</tr>";
}

function markup_table($rows, $incl_header = FALSE){
	$table_body = array();
	if(is_array($rows)){
		$i = 0;
		if($incl_header){
			$header = array_shift($rows);
			$table_body[] = markup_tr($header, "even", TRUE);
			$i += 1;
		}
		foreach($rows as $row){
			$table_body[] = markup_tr($row, ($i % 2 === 1 ? "odd" : "even"));
			$i += 1;
		}
	}
	$table_body = implode("\n", $table_body);
	return "<table>\n{$table_body}\n</table>";
}

function markup_external_anchor($url, $content = NULL){
	$content = $content !== NULL ? $content : $url;
	$url = preg_match("/https?:\/\/(?:p|pbs)\.twimg\.com\//", $url) ? $url . ":large" : $url;
	$url_formated = htmlspecialchars(get_prefs("gwt") ? "http://google.com/gwt/n?u={$url}" : $url, ENT_QUOTES, "UTF-8", FALSE);
	return "<a href=\"{$url_formated}\" target=\"_blank\">{$content}</a>";
}

function markup_img($src, $alt, $size = NULL){
	return "<img src=\"{$src}\" alt=\"{$alt}\"" . (!empty($size) ? " width=\"{$size}\" height=\"{$size}\"" : "") . ">";
}

function markup_external_img($url, $src, $alt, $size = NULL){
	return markup_external_anchor($url, markup_img($src, $alt, $size));
}
