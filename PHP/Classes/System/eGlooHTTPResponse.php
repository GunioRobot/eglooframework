<?php
/**
 * eGlooHTTPResponse Class File
 *
 * $file_block_description
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooHTTPResponse
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooHTTPResponse {

	public static function issueRaw404Response() {
		self::resetHeaders();

		// TODO branch on different 404 type if using FastCGI: header("Status: 404 Not Found"); or header("HTTP/1.0 404 Not Found")
		header('HTTP/1.0 404 Not Found', true, 404);
		exit;
	}

	public static function issueCustom404Response( $custom_error_template = null ) {
		
	}

	public static function resetHeaders() {
		header_remove('Cache-Control');
		header_remove('Content-type');
		header_remove('Expires');
		header_remove('Pragma');
	}

}

