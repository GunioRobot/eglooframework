<?php
/**
 * eGlooString Class File
 *
 * Contains the class definition for the eGlooString
 * 
 * Copyright 2009 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Core
 * @version 1.0
 */

/**
 * eGlooString Class
 * 
 * @package Core
 * @subpackage Utilities
 */
class eGlooString {

	private $string_as_UTF8 = '';

	public function __construct($string) {
		$this->setString($string);
	}

	public function setString($string) {
		$this->string_as_UTF8 = eGlooString::UTF8Encode($string);
	}

	public function getString() {
		return $this->string_as_UTF8;
	}

	public static function UTF8Encode($string) {
		$retVal = '';

		$cur_encoding = mb_detect_encoding($string) ;

		if ($cur_encoding == "UTF-8" && mb_check_encoding($string,"UTF-8")) {
			$retVal = $string;
		} else {
			$retVal = utf8_encode($string);
		}

		return $retVal;
	}

	public function __toString() {
		return $this->string_as_UTF8;
	}

}
