<?php
namespace eGloo\Combine;

/**
 * eGloo\Combine\Combine Class File
 *
 * Contains the class definition for the eGloo\Combine\Combine
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
 * eGloo\Combine\Combine
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 * @subpackage Combines
 */
abstract class Combine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array();

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
	 * Return an instance of this class built from the provided CLI arguments
	 *
	 * @return eGloo\Combine\Combine subclass object
	 * @author George Cooper
	 **/
	public static function getInstanceFromCLIArgumentArray( $arguments ) {
		$retVal = null;

		$combineObject = null;
		$command = null;

		if ( !empty($arguments) ) {
			$command = array_shift($arguments);

			if ( is_string($command) && trim($command) !== '' && self::supportsCommand($command) ) {
				$combineObject = new static();

				$combineObject->setCommand( $command );
				$combineObject->setRawArguments( $arguments );

				$combineObject->parseOptions();
				$combineObject->parseCommandArguments();

				if ( $combineObject->commandRequirementsSatisfied() ) {
					$combineObject->setIsExecutable();
				}
			} else if ( is_string($command) && trim($command) !== '' && self::supportsEmptyCommand() ) {
				$combineObject = new static();

				array_unshift( $arguments, $command );

				$combineObject->setCommand( '_empty' );
				$combineObject->setRawArguments( $arguments );

				$combineObject->parseOptions();
				$combineObject->parseCommandArguments();

				if ( $combineObject->commandRequirementsSatisfied() ) {
					$combineObject->setIsExecutable();
				}
			}
		} else if ( self::supportsZeroArgumentCommand() ) {
			$combineObject = new static();

			$combineObject->setCommand( '_zero_argument' );
			$combineObject->setRawArguments( array() );

			$combineObject->parseOptions();
			$combineObject->parseCommandArguments();

			if ( $combineObject->commandRequirementsSatisfied() ) {
				$combineObject->setIsExecutable();
			}
		}

		$retVal = $combineObject;

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

				if ( isset($parsed_options[$matches[1]]) && !is_array($parsed_options[$matches[1]]) ) {
					$parsed_options[$matches[1]] = array($parsed_options[$matches[1]]);
					$parsed_options[$matches[1]][] = $matches[2];
				} else {
					$parsed_options[$matches[1]] = $matches[2];
				}

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

		$this->_parsed_options = $parsed_options;

		$retVal = true;

		return $retVal;
	}

	public static function supportsCommand( $command ) {
		$retVal = false;

		if ( in_array( $command, array_keys(static::$_supported_commands) ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public static function supportsEmptyCommand() {
		$retVal = false;

		if ( in_array( '_empty', array_keys(static::$_supported_commands) ) ) {
			$retVal = true;
		}

		return $retVal;
	}

	public static function supportsZeroArgumentCommand() {
		$retVal = false;

		if ( in_array( '_zero_argument', array_keys(static::$_supported_commands) ) ) {
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
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'Prepare for unforeseen consequences';
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
		return static::$_supported_commands;
	}

	/**
	 * Sets static class member $_supported_commands
	 *
	 * @param supported_commands array List of supported commands and their options/required arguments
	 */
	public static function setSupportedCommands( $supported_commands ) {
		static::$_supported_commands = $supported_commands;
	}

}

