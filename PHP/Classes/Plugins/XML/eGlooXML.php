<?php
/**
 * eGlooXML Class File
 *
 * Contains the class definition for the eGlooXML
 * 
 * Copyright 2011 eGloo LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * eGlooXML
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooXML {

	/**
	 * @var array Children of this XML DOM
	 */
	protected $_children = null;

	/**
	 * @var object SimpleXMLElement object
	 */
	protected $_simpleXMLObject = null;

	public function __construct( $xml_location ) {
		$this->_simpleXMLObject = simplexml_load_file( $xml_location );
	}

	/**
	 * Returns protected class member $_children
	 *
	 * @return array Children of this XML DOM
	 */
	public function getChildren() {
		return $this->_children;
	}

	/**
	 * Sets protected class member $_children
	 *
	 * @param children array Children of this XML DOM
	 */
	public function setChildren( $children ) {
		$this->_children = $children;
	}

	/**
	 * Get child by XPath
	 *
	 * @param $xpath XPath expression for child
	 * @return eGlooXMLChild
	 */
	public function getChild( $xpath ) {
		return $this->_children[$xpath];
	}

	/**
	 * Set child by XPath
	 *
	 * @param $xpath XPath expression for child
	 * @param eGlooXMLChild Child to set
	 */
	public function setChild( $xpath, eGlooXMLChild $child ) {
		$this->_children[$xpath] = $child;
	}

	/**
	 * Returns protected class member $_simpleXMLObject
	 *
	 * @return object SimpleXMLElement object
	 */
	public function getSimpleXMLObject() {
		return $this->_simpleXMLObject;
	}

	/**
	 * Sets protected class member $_simpleXMLObject
	 *
	 * @param simpleXMLObject object SimpleXMLElement object
	 */
	public function setSimpleXMLObject( $simpleXMLObject ) {
		$this->_simpleXMLObject = $simpleXMLObject;
	}

	/**
	 * Get eGlooXML object as array
	 *
	 * @return array Array representation of this eGlooXML
	 */
	public function toArray() {
		
	}

	// From http://recursive-design.com/blog/2007/04/05/format-xml-with-php/
	public static function formatXMLString( $xml ) {	

	  // add marker linefeeds to aid the pretty-tokeniser (adds a linefeed between all tag-end boundaries)
	  $xml = preg_replace('/(>)(<)(\/*)/', "$1\n$2$3", $xml);

	  // now indent the tags
	  $token	  = strtok($xml, "\n");
	  $result	  = ''; // holds formatted version as it is built
	  $pad		  = 0; // initial indent
	  $matches	  = array(); // returns from preg_matches()

	  // scan each line and adjust indent based on opening/closing tags
	  while ($token !== false) : 

		// test for the various tag states

		// 1. open and closing tags on same line - no change
		if (preg_match('/.+<\/\w[^>]*>$/', $token, $matches)) : 
			$indent = -1;
			$pad++;
		// 2. closing tag - outdent now
		elseif (preg_match('/^<\/\w/', $token, $matches)) :
		  if ( $indent === -1 ) {
			$indent = 0;
		  }
		  $pad--;
		// 3. opening tag - don't pad this one, only subsequent tags
		elseif (preg_match('/^<\w[^>]*[^\/]>.*$/', $token, $matches)) :
		  $indent=1;
		// 4. no indentation needed
		else :
		  $indent = 0; 
		endif;

		// pad the line with the required number of leading spaces
		$line	 = str_pad($token, strlen($token)+$pad, "\t", STR_PAD_LEFT);
		$result .= $line . "\n"; // add to the cumulative result, with linefeed
		$token	 = strtok("\n"); // get the next token
		$pad	+= $indent; // update the pad size for subsequent lines
	  endwhile; 

	  return $result;
	}
}

