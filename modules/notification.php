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

function notification_load_state($force_init = FALSE){
	$notif = !empty($_COOKIE["NOTIF"]) ? unserialize(base64_decode($_COOKIE["NOTIF"])) : array();
	$state = !$force_init && isset($notif[USERNAME]) && is_object($notif[USERNAME]) ? $notif[USERNAME] : (object) array("ids" => array(array(), array()), "updated" => NULL);

	return $state;
}

function notification_set_state($state){
	$notif = !empty($_COOKIE["NOTIF"]) ? unserialize(base64_decode($_COOKIE["NOTIF"])) : array();

	if(!empty($state)){
		$notif[USERNAME] = $state;
	}else if(!empty($notif[USERNAME])){
		unset($notif[USERNAME]);
	}

	setcookie("NOTIF", $_COOKIE["NOTIF"] = !empty($notif) ? base64_encode(serialize($notif)) : FALSE, 0, dirname($_SERVER["SCRIPT_NAME"]));

	return;
}

function notification_check($no_notification = 0){
	if($no_notification === 1){
		return;
	}

	$notif_int = isset($_POST["notif_int"]) ? intval($_POST["notif_int"]) : intval(get_prefs("notif_int", DEF_REF_INT));

	if($notif_int === 0){
		notification_set_state(NULL);
		return;
	}

	$state = notification_load_state();

	if($state->updated + $notif_int > time()){
		return;
	}

	if($state->updated !== NULL){
		$zone_keys = array("mentions" => 0, "dm" => 1);
		$tests = array("statuses/mentions.json", "direct_messages.json");

		foreach($tests as $key => $test){
			if(isset($zone_keys[$no_notification]) && $key === $zone_keys[$no_notification]){
				continue;
			}
			if(!is_array($state->ids[$key])){
				$state = notification_load_state(TRUE);
			}

			if(empty($state->ids[$key][0])){
				$test = twitter_process($test, FALSE, TRUE);
				if(is_array($test)){
					if(empty($test)){
						$state->ids[$key][0] = "1";
					}else if(isset($test[0]->id_str)){
						$state->ids[$key][0] = $test[0]->id_str;
					}
				}
			}else{
				$test = twitter_process($test . "?since_id={$state->ids[$key][0]}&count=200", FALSE, TRUE);

				if(!is_array($test) || empty($test[0])){
					continue;
				}

				$appended = FALSE;
				foreach($test as $target){
					if(isset($target->user->screen_name) && is_current_user($target->user->screen_name)){
						continue;
					}
					$appended = TRUE;
					$state->ids[$key][] = $target->id_str;
				}
				if(!$appended){
					continue;
				}

				unset($state->ids[$key][0]);

				$state->ids[$key] = array_unique($state->ids[$key]);
				usort($state->ids[$key], "strnumcmp");
				array_unshift($state->ids[$key], $state->ids[$key][count($state->ids[$key]) - 1]);
			}
		}

		$state->updated = time();
	}else{
		$state->updated = time() - $notif_int + 20;
	}

	notification_set_state($state);

	return;
}

function notification_clear_flags($zone, $last_id){
	$zone_keys = array("mentions" => 0, "dm" => 1);
	if(!isset($zone_keys[$zone])){
		return;
	}
	$key = $zone_keys[$zone];

	$state = notification_load_state();
	$state->ids[$key] = array((empty($state->ids[$key][0]) || (!empty($last_id) && strnumcmp($last_id, $state->ids[$key][0]) === 1) ? $last_id : $state->ids[$key][0]));

	notification_set_state($state);

	return;
}

function notification_mark_mentions($id){
	$state = notification_load_state();

	if(!is_array($state->ids[0]) || !is_numeric($id)){
		return;
	}

	if(count($state->ids[0]) > 1){
		$last_id = $state->ids[0][0];
		unset($state->ids[0][0]);

		$key = array_search($id, $state->ids[0]);
		if($key !== FALSE){
			unset($state->ids[0][$key]);
		}
		usort($state->ids[0], "strnumcmp");

		array_unshift($state->ids[0], strnumcmp($id, $last_id) === 1 ? $id : $last_id);
	}else{
		$state->ids[0][0] = isset($state->ids[0][0]) && strnumcmp($id, $state->ids[0][0]) === -1 ? $state->ids[0][0] : $id;
	}

	notification_set_state($state);

	return;
}

function notification_indicate(){
	$notification_targets = 2;
	$notification_chars = array("<a href=\"mentions\">@%d</a>", "<a href=\"dm\">D%d</a>");
	$notification = array();

	$state = notification_load_state();
	for($i = 0; $i < $notification_targets; $i += 1){
		if(($count = count($state->ids[$i])) > 1){
			$notification[] = sprintf($notification_chars[$i], $count - 1);
		}
	}

	return !empty($notification) ? "<strong>" . implode(" ", $notification) . "</strong> " : "";
}
