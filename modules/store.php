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

class simpleStore{
	private $storeRootDir;

	public function selectEntry($store, $entry, $cond = NULL){
		if(!file_exists($datafile = "{$this->storeRootDir}/" . rawurlencode($store) . "/" . md5($entry))){
			return FALSE;
		}

		$data = unserialize(file_get_contents($datafile));

		if(!is_array($data)){
			return NULL;
		}else if($cond === NULL){
			return $data;
		}else if(!is_array($cond)){
			return NULL;
		}else{
			foreach($cond as $key => $value){
				if(!isset($data[$key]) || $data[$key] !== $value){
					return NULL;
				}
			}

			return $data;
		}
	}

	public function replaceEntry($store, $entry, $set, $cond = NULL){
		if(($cond !== NULL && $this->selectEntry($store, $entry, $cond) === FALSE) || !is_array($set)){
			return FALSE;
		}

		foreach($set as $key => $value){
			if(is_bool($value)){
				$set[$key] = (int) $value;
			}
		}

		return file_put_contents("{$this->storeRootDir}/" . rawurlencode($store) . "/" . md5($entry), serialize($set));
	}

	public function deleteEntry($store, $entry, $cond = NULL){
		if($this->selectEntry($store, $entry, $cond) === FALSE){
			return FALSE;
		}
		return unlink("{$this->storeRootDir}/" . rawurlencode($store) . "/" . md5($entry));
	}

	public function __construct($storeRootDir){
		$this->storeRootDir = $storeRootDir;
		return;
	}
}
