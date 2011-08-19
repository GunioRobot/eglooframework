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

