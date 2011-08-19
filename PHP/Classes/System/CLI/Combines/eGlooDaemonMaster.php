<?php
/**
 * eGlooDaemonMaster Class File
 *
 * Contains the class definition for the eGlooDaemonMaster
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
 * eGlooDaemonMaster
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 * @subpackage Combines
 */
class eGlooDaemonMaster extends eGlooCombine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'info' => array(),
		'list' => array(),
		'start' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case 'info' :
				$retVal = $this->info();
				break;
			case 'list' :
				$retVal = $this->_list();
				break;
			case 'start' :
				$retVal = $this->start();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function info() {
		$retVal = false;

		if ( isset( $this->_command_arguments[0]) ) {
			$info_subject = $this->_command_arguments[0];


		}

		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		$daemonDefinitions = array();

		if ( $daemonDefinitions !== null ) {
			// TODO actually branch on arguments
			$this->listDaemonsAll( $daemonDefinitions );
			$retVal = true;
		}

		return $retVal;
	}

	protected function start() {
		$retVal = false;

		if ( !function_exists('pcntl_fork') ) {
			throw new ErrorException('PCNTL extension not found');
		} else if ( isset( $this->_command_arguments[0]) ) {			
			$daemon_name = $this->_command_arguments[0];
			$daemonObj = null;

			if ( class_exists($daemon_name) ) {
				$daemonObj = new $daemon_name();
			}

			if ( is_object($daemonObj) && $daemonObj instanceof eGlooDaemon ) {
				// TODO better checking between each call and setting $retVal to something useful
				$daemonObj->setParsedOptions( $this->getParsedOptions() );
				$daemonObj->start();
				$daemonObj->run();
				$daemonObj->stop();
			} else if ( is_object($daemonObj) && !($daemonObj instanceof eGlooDaemon) ) {
				echo '"' . $daemon_name . '" is not a valid eGlooDaemon class.' . "\n";
			} else if ( !is_object($daemonObj) ) {
				echo '"' . $daemon_name . '" is not a known eGlooDaemon class or ID.' . "\n";
			}
		}

		return $retVal;
	}

	public function listDaemonsAll( $daemonDefinitions ) {
		// For now, just this Daemons.xml, don't include the framework proper or common
		echo 'Listing Daemons:' . "\n";
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			case 'info' :
				$retVal = $this->infoCommandRequirementsSatisfied();
				break;
			case 'list' :
				$retVal = $this->listCommandRequirementsSatisfied();
				break;
			case 'start' :
				$retVal = $this->startCommandRequirementsSatisfied();
				break;
			default :
				break;
		}

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

	protected function startCommandRequirementsSatisfied() {
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
		return 'eGloo Daemons Help';
	}

}

