<?php
/**
 * eGlooDiff Class File
 *
 * Contains the class definition for the eGlooDiff
 * 
 * Copyright 2011 eGloo LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *        http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *  
 * @author George Cooper
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooDiff
 *
 * $short_description
 *
 * Class uses code from Paul Butler's Sample Diff Algorithm.  Modification was made
 * to allow the functions to be invoked as static class methods as well as to
 * improve formatting
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooDiff {

	/*
		Paul's Simple Diff Algorithm v 0.1
		(C) Paul Butler 2007 <http://www.paulbutler.org/>
		May be used and distributed under the zlib/libpng license.

		This code is intended for learning purposes; it was written with short
		code taking priority over performance. It could be used in a practical
		application, but there are a few ways it could be optimized.

		Given two arrays, the function diff will return an array of the changes.
		I won't describe the format of the array, but it will be obvious
		if you use print_r() on the result of a diff on some test data.

		htmlDiff is a wrapper for the diff command, it takes two strings and
		returns the differences in HTML. The tags used are <ins> and <del>,
		which can easily be styled with CSS.  
	*/
	public static function diff( $old, $new ) {
		$matrix = array();
		$maxlen = 0;

		foreach($old as $oindex => $ovalue){
			$nkeys = array_keys($new, $ovalue);
			foreach($nkeys as $nindex){
				$matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
					$matrix[$oindex - 1][$nindex - 1] + 1 : 1;
				if($matrix[$oindex][$nindex] > $maxlen){
					$maxlen = $matrix[$oindex][$nindex];
					$omax = $oindex + 1 - $maxlen;
					$nmax = $nindex + 1 - $maxlen;
				}
			}	
		}

		if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
		return array_merge(
			eGlooDiff::diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
			array_slice($new, $nmax, $maxlen),
			eGlooDiff::diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
	}

	public static function htmlDiff($old, $new){
		$diff = eGlooDiff::diff(explode(' ', $old), explode(' ', $new));

		$ret = '';
		foreach($diff as $k){
			if(is_array($k))
				$ret .= (!empty($k['d'])?"<del>".implode(' ', $k['d'])."</del> ":'').
					(!empty($k['i'])?"<ins>".implode(' ', $k['i'])."</ins> ":'');
			else $ret .= $k . ' ';
		}
		return $ret;
	}

}
