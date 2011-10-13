<?php
namespace eGloo\Combine;

/**
 * eGloo\Combine\Version Class File
 *
 * Contains the class definition for the eGloo\Combine\Version
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
 * @category $category
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGloo\Combine\Version
 *
 * $short_description
 *
 * $long_description
 *
 * @category $category
 * @package $package
 * @subpackage $subpackage
 */
class Version extends Combine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'_empty' => array(),
		'_zero_argument' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case '_empty' :
				$retVal = $this->printVersionInfo();
				break;
			case '_zero_argument' :
				$retVal = $this->printVersionInfo();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function printVersionInfo() {
		echo self::getHelpString() . "\n";
	}

	public function commandRequirementsSatisfied() {
		return true;
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		$retVal = 'Version 1.0 Developer Preview 2';

		return $retVal;
	}

}

