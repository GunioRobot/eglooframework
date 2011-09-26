<?php
/**
 * eGlooApplication Class File
 *
 * Contains the class definition for the eGlooApplication
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
 * @category System
 * @package CLI
 * @subpackage Combines
 * @version 1.0
 */

/**
 * eGlooApplication
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 * @subpackage Combines
 */
class eGlooApplication extends eGlooCombine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'create' => array(),
		'info' => array(),
		'list' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case 'create' :
				$retVal = $this->create();
				break;
			case 'info' :
				$retVal = $this->info();
				break;
			case 'list' :
				$retVal = $this->_list();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function create() {
		$retVal = false;

		return $retVal;
	}

	protected function info() {
		$retVal = false;

		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		return $retVal;
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			case 'create' :
				$retVal = $this->createCommandRequirementsSatisfied();
				break;
			case 'info' :
				$retVal = $this->infoCommandRequirementsSatisfied();
				break;
			case 'list' :
				$retVal = $this->listCommandRequirementsSatisfied();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function createCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	protected function infoCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	protected function listCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'eGloo Application Help';
	}

}

