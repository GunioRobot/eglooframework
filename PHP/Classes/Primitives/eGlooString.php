<?php
/**
 * eGlooString Class File
 *
 * Contains the class definition for the eGlooString
 * 
 * Copyright 2011 eGloo, LLC
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
 * @copyright 2011 eGloo, LLC
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

	protected $safe_html_tokens = array(
		array('search' => '&lt;p&gt;', 'replace' => '<p>'),
		array('search' => '&lt;br /&gt;', 'replace' => '<br />'),
		array('search' => '&lt;/p&gt;', 'replace' => '</p>'),
	);

	protected $garbage_sets = array(
		// For anyone who got a little escape drunk
		array('search' => '\\\’', 'replace' => '’')
	);

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

		if ($cur_encoding == "UTF-8" && mb_check_encoding($string, "UTF-8")) {
			$retVal = $string;
		} else {
			$retVal = utf8_encode($string);
		}

		return $retVal;
	}

	public function getStringWithSafeHTMLTokensDecoded($string = null, $safe_html_tokens = null) {
		$retVal = null;

		if ($string === null) {
			$retVal = $this->string_as_UTF8;
		} else {
			$retVal = $string;
		}

		if ($safe_html_tokens === null) {
			$safe_html_tokens = $this->safe_html_tokens;
		}

		foreach($safe_html_tokens as $token_set) {
			$retVal = str_replace($token_set['search'], $token_set['replace'], $retVal);
		}

		return $retVal;
	}

	public function getStringWithoutGarbage($string = null, $garbage_sets = null, $merge_sets = true) {
		$retVal = null;

		if ($string === null) {
			$retVal = $this->string_as_UTF8;
		} else {
			$retVal = $string;
		}

		if ($garbage_sets === null) {
			$garbage_sets = $this->garbage_sets;
		} else if ($merge_sets) {
			$garbage_sets = array_merge($this->garbage_sets, $garbage_sets);
		}

		foreach($garbage_sets as $token_set) {
			$retVal = str_replace($token_set['search'], $token_set['replace'], $retVal);
		}

		return $retVal;
	}


	public function setSafeHtmlTokens($safe_html_tokens_array) {
		$this->safe_html_tokens = $safe_html_tokens_array;
	}

	public function __toString() {
		return $this->string_as_UTF8;
	}

	public static function toCamelCase( $string, $separator = '_', $ucfirst = false ) {
		$chunks = explode( $separator, $string );
		$chunks = $chunks ? array_map( 'ucfirst', $chunks ) : array( $string );
		$chunks[0] = $ucfirst ? ucfirst( $chunks[0] ) : lcfirst( $chunks[0] );

		return implode( '', $chunks );
	}

	public static function toPrettyPrint( $string, $separator = '_', $ucfirst = false, $title_case = true ) {
		$chunks = explode( $separator, $string );

		if ( $title_case ) {
			$chunks = $chunks ? array_map( 'ucfirst', $chunks ) : array( $string );
		}

		$chunks[0] = $ucfirst ? ucfirst( $chunks[0] ) : lcfirst( $chunks[0] );

		return implode( ' ', $chunks );
	}

	public static function toPrettyPrintFromCamelCase( $string, $separator = '_', $ucfirst = false ) {
		// TODO
	}

}
