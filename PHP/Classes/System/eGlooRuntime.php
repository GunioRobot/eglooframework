<?php
/**
 * eGlooRuntime Class File
 *
 * Contains the class definition for the eGlooRuntime
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
 * eGlooRuntime Class
 * 
 * This is a class that exists solely to execute the eGloo runtime in a protected
 * context to better enforce OOP access types and package security.
 * 
 * @package Core
 */
class eGlooRuntime extends eGlooCombine {

	protected function __construct() {
		
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'eGloo Runtime Help';
	}

	public static function run() {
	}

}