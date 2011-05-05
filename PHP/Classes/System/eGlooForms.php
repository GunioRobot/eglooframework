<?php
/**
 * eGlooForms Class File
 *
 * Contains the class definition for the eGlooForms
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
 * eGlooForms
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooForms {

	/**
	 * @var string Name of the command to execute
	 */
	protected $_command = null;

	/**
	 * @var array Command arguments for the specified command
	 */
	protected $_command_arguments = null;

	/**
	 * @var bool If this forms processing object is ready to be executed
	 */
	protected $_is_executable = false;

	/**
	 * @var array Parsed options (key/value)
	 */
	protected $_parsed_options = null;

	/**
	 * @var array Raw arguments for specified command
	 */
	protected $_raw_arguments = null;

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'list' => array(),
	);

	public function __construct() {
		
	}

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case 'list' :
				$retVal = $this->_list();
				break;
			default :
				break;
		}

		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		$formDirector = FormDirector::getInstance();

		$formDefinitions = null;

		try {
			$formDefinitions = $formDirector->getParsedFormDefinitionsArrayFromXML();
		} catch ( FormDirectorException $e ) {
			// TODO better error handling.  For now this probably means the Forms.xml
			// file was not found locally.  Just print message and move on, since this is
			// just a listing command.
			echo $e->getMessage() . "\n";
		}

		// die_r($formDefinitions);

		// $referral = $formDirector->buildForm('referral');
		// $this->formatReferralForm($referral);
		// echo $referral->render();

		if ( $formDefinitions !== null ) {
			$formNodes = $formDefinitions['formNodes'];
			$formAttributeSetNodes = $formDefinitions['formAttributeSetNodes'];
			die_r($formDefinitions);
			// TODO actually branch on arguments
			$this->listFormsAll();
			$retVal = true;
		}

		return $retVal;
	}

	public function listFormsAll() {
		// For now, just this Forms.xml, don't include the framework proper or common
		$this->listFormNodes();
		$this->listFormAttributeSetNodes();
	}

	public function listFormNodes() {
		echo 'Forms Processing Nodes:' . "\n";

		echo "\n";
	}

	public function listFormAttributeSetNodes() {
		echo 'Forms Processing Attribute Set Nodes:' . "\n";

		echo "\n";
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			case 'list' :
				$retVal = $this->listCommandRequirementsSatisfied();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function listCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	public function parseCommandArguments( $arguments = null ) {
		$retVal = false;

		$command_arguments = array();

		if ( !$arguments ) {
			$arguments = $this->_raw_arguments;
		}

		foreach($arguments as $argument) {
			$matches = array();
			preg_match('/^([a-zA-Z0-9]+)$/', $argument, $matches);

			if ( !empty($matches) && isset($matches[1]) ) {
				$command_arguments[] = $matches[1];
			}
		}

		$this->_command_arguments = $command_arguments;

		$retVal = true;

		return $retVal;
	}

	public function parseOptions( $arguments = null ) {
		$retVal = false;

		$parsed_options = array();

		if ( !$arguments ) {
			$arguments = $this->_raw_arguments;
		}

		foreach($arguments as $argument) {
			$matches = array();
			preg_match('/^--([a-zA-Z]+?)=([a-zA-Z0-9\/_.: -]+)$/', $argument, $matches);

			if ( !empty($matches) && isset($matches[1]) && isset($matches[2]) ) {
				$value = $matches[2];

				if ( strtolower($value) === 'true' ) {
					$value = true;
				} else if ( strtolower($value) === 'false' ) {
					$value = false;
				} else if ( is_numeric($value) ) {
					if ( strpos($value, '.') !== false ) {
						$value = floatval( $value );
					} else {
						$value = intval( $value );
					}
				}

				$parsed_options[$matches[1]] = $matches[2];
				continue;
			}

			$matches = array();
			preg_match('/^--([a-zA-Z0-9]+?)$/', $argument, $matches);

			if ( !empty($matches) && isset($matches[1]) ) {
				$parsed_options[$matches[1]] = true;
				continue;
			}

			$matches = array();
			preg_match('/^-([a-zA-Z0-9]{1})$/', $argument, $matches);

			if ( !empty($matches) && isset($matches[1]) ) {
				$parsed_options[$matches[1]] = true;
				continue;
			}
		}

		$retVal = true;

		return $retVal;
	}

	public static function supportsCommand( $command ) {
		$retVal = false;

		if ( in_array( $command, array_keys(self::$_supported_commands) ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	// Getters/Setters

	/**
	 * Returns protected class member $_command
	 *
	 * @return string Name of the command to execute
	 */
	public function getCommand() {
		return $this->_command;
	}

	/**
	 * Sets protected class member $_command
	 *
	 * @param command string Name of the command to execute
	 */
	public function setCommand( $command ) {
		$this->_command = $command;
	}

	/**
	 * Returns protected class member $_command_arguments
	 *
	 * @return array Command arguments for the specified command
	 */
	public function getCommandArguments() {
		return $this->_command_arguments;
	}

	/**
	 * Sets protected class member $_command_arguments
	 *
	 * @param command_arguments array Command arguments for the specified command
	 */
	public function setCommandArguments( $command_arguments ) {
		$this->_command_arguments = $command_arguments;
	}

	/**
	 * Returns protected class member $_is_executable
	 *
	 * @return bool If this forms processing object is ready to be executed
	 */
	public function isExecutable() {
		return $this->_is_executable;
	}

	/**
	 * Sets protected class member $_is_executable
	 *
	 * @param is_executable bool If this forms processing object is ready to be executed
	 */
	public function setIsExecutable( $is_executable = true ) {
		$this->_is_executable = $is_executable;
	}

	/**
	 * Returns protected class member $_parsed_options
	 *
	 * @return array Parsed options (key/value)
	 */
	public function getParsedOptions() {
		return $this->_parsed_options;
	}

	/**
	 * Sets protected class member $_parsed_options
	 *
	 * @param parsed_options array Parsed options
	 */
	public function setParsedOptions( $parsed_options ) {
		$this->_parsed_options = $parsed_options;
	}

	/**
	 * Returns protected class member $_raw_arguments
	 *
	 * @return array Raw arguments for specified command
	 */
	public function getRawArguments() {
		return $this->_raw_arguments;
	}

	/**
	 * Sets protected class member $_raw_arguments
	 *
	 * @param raw_arguments array Raw arguments for specified command
	 */
	public function setRawArguments( $raw_arguments ) {
		$this->_raw_arguments = $raw_arguments;
	}

	/**
	 * Returns static class member $_supported_commands
	 *
	 * @return array List of supported commands and their options/required arguments
	 */
	public static function getSupportedCommands() {
		return self::$_supported_commands;
	}

	/**
	 * Sets static class member $_supported_commands
	 *
	 * @param supported_commands array List of supported commands and their options/required arguments
	 */
	public static function setSupportedCommands( $supported_commands ) {
		self::$_supported_commands = $supported_commands;
	}

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'eGloo Forms Help';
	}

	/**
	 * Return an instance of this class build from the provided CLI arguments
	 *
	 * @return eGlooForms object
	 * @author George Cooper
	 **/
	public static function getInstanceFromCLIArgumentArray( $arguments ) {
		$retVal = null;

		$formsObject = null;
		$command = null;

		if ( !empty($arguments) ) {
			$command = array_shift($arguments);

			if ( is_string($command) && trim($command) !== '' && self::supportsCommand($command) ) {
				$formsObject = new eGlooForms();

				$formsObject->setCommand( $command );
				$formsObject->setRawArguments( $arguments );

				$formsObject->parseOptions();
				$formsObject->parseCommandArguments();

				if ( $formsObject->commandRequirementsSatisfied() ) {
					$formsObject->setIsExecutable();
				}
			}
		}

		$retVal = $formsObject;

		return $retVal;
	}

}

