<?php
/**
 * eGlooDataProcessing Class File
 *
 * Contains the class definition for the eGlooDataProcessing
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
 * eGlooDataProcessing
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooDataProcessing {

	/**
	 * @var string Name of the command to execute
	 */
	protected $_command = null;

	/**
	 * @var array Command arguments for the specified command
	 */
	protected $_command_arguments = null;

	/**
	 * @var bool If this data processing object is ready to be executed
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
		'info' => array(),
		'list' => array(),
	);

	public function __construct() {
		
	}

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
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

	protected function info() {
		$retVal = false;

		if ( isset( $this->_command_arguments[0]) ) {
			$info_subject = $this->_command_arguments[0];

			$dpDirector = eGlooDPDirector::getInstance( null, null );

			$dpDefinitions = null;

			try {
				$dpDefinitions = $dpDirector->getParsedDefinitionsArrayFromXML();
			} catch ( eGlooDPDirectorException $e ) {
				// TODO better error handling.  For now this probably means the Forms.xml
				// file was not found locally.  Just print message and move on, since this is
				// just a listing command.
				echo $e->getMessage() . "\n";
			}

			if ( $dpDefinitions !== null ) {
				$results = 0;

				if ( isset($dpDefinitions['dataProcessingProcedures'][$info_subject]) ) {
					$procedure_info = $dpDefinitions['dataProcessingProcedures'][$info_subject];
					$results++;
				}

				if ( isset($dpDefinitions['dataProcessingSequences'][$info_subject]) ) {
					$sequence_info = $dpDefinitions['dataProcessingSequences'][$info_subject];
					$results++;
				}

				if ( isset($dpDefinitions['dataProcessingStatements'][$info_subject]) ) {
					$statement_info = $dpDefinitions['dataProcessingStatements'][$info_subject];
					$results++;
				}

				$result_summary = 'Search for "' . $info_subject . '" has ' . $results;
				$result_summary .= $results === 1 ? ' result.' : ' results.';
				$result_summary .= "\n\n";

				echo $result_summary;

				if ( !empty($procedure_info ) ) {
					
				}

				if ( !empty($sequence_info ) ) {
					
				}

				if ( !empty($statement_info ) ) {
					echo 'Statement Class: ' . $statement_info['statementClass'] . "\n";
					// echo 'Statements Provided:' . "\n";
					echo 'Statements Provided: ';

					$count = 1;

					foreach($statement_info['statements'] as $statement_name => $statement) {
						echo $statement_name;
						if ( $count < count($statement_info['statements']) ) {
							echo ', ';
							$count++;
						}
					}

					echo "\n";
					// print_r($statement_info);
				}

				$retVal = true;
			}
		}


		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		$dpDirector = eGlooDPDirector::getInstance( null, null );

		$dpDefinitions = null;

		try {
			$dpDefinitions = $dpDirector->getParsedDefinitionsArrayFromXML();
		} catch ( eGlooDPDirectorException $e ) {
			// TODO better error handling.  For now this probably means the Forms.xml
			// file was not found locally.  Just print message and move on, since this is
			// just a listing command.
			echo $e->getMessage() . "\n";
		}

		if ( $dpDefinitions !== null ) {
			$dataProcessingProcedures = $dpDefinitions['dataProcessingProcedures'];
			$dataProcessingSequences = $dpDefinitions['dataProcessingSequences'];
			$dataProcessingStatements = $dpDefinitions['dataProcessingStatements'];
			$this->listDPAll( $dpDefinitions );
			$retVal = true;
		}

		return $retVal;
	}

	public function listDPAll( $dpDefinitions ) {
		$dataProcessingProcedures = $dpDefinitions['dataProcessingProcedures'];
		$dataProcessingSequences = $dpDefinitions['dataProcessingSequences'];
		$dataProcessingStatements = $dpDefinitions['dataProcessingStatements'];

		$this->listDPProcedures( $dataProcessingProcedures );
		$this->listDPSequences( $dataProcessingSequences );
		$this->listDPStatements( $dataProcessingStatements );
	}

	public function listDPProcedures( $dataProcessingProcedures ) {
		if ( !empty($dataProcessingProcedures) ) {
			echo 'Data Processing Procedures:' . "\n";

			foreach( $dataProcessingProcedures as $dp_procedure_id => $dp_procedure_node ) {
				echo "\t" . $dp_procedure_id . "\n";
			}

			echo "\n";
		}
	}

	public function listDPSequences( $dataProcessingSequences ) {
		if ( !empty($dataProcessingSequences) ) {
			echo 'Data Processing Sequences:' . "\n";

			foreach( $dataProcessingSequences as $dp_sequence_id => $dp_sequence_node ) {
				echo "\t" . $dp_sequence_id . "\n";
			}

			echo "\n";
		}
	}

	public function listDPStatements( $dataProcessingStatements ) {
		if ( !empty($dataProcessingStatements) ) {
			echo 'Data Processing Statements:' . "\n";

			foreach( $dataProcessingStatements as $dp_statement_id => $dp_statement_node ) {
				echo "\t" . $dp_statement_id . "\n";
			}

			echo "\n";
		}
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
	 * @return bool If this data processing object is ready to be executed
	 */
	public function isExecutable() {
		return $this->_is_executable;
	}

	/**
	 * Sets protected class member $_is_executable
	 *
	 * @param is_executable bool If this data processing object is ready to be executed
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
		return 'eGloo Data Processing Help';
	}

	/**
	 * Return an instance of this class build from the provided CLI arguments
	 *
	 * @return eGlooDataProcessing object
	 * @author George Cooper
	 **/
	public static function getInstanceFromCLIArgumentArray( $arguments ) {
		$retVal = null;

		$dataProcessingObject = null;
		$command = null;

		if ( !empty($arguments) ) {
			$command = array_shift($arguments);

			if ( is_string($command) && trim($command) !== '' && self::supportsCommand($command) ) {
				$dataProcessingObject = new eGlooDataProcessing();

				$dataProcessingObject->setCommand( $command );
				$dataProcessingObject->setRawArguments( $arguments );

				$dataProcessingObject->parseOptions();
				$dataProcessingObject->parseCommandArguments();

				if ( $dataProcessingObject->commandRequirementsSatisfied() ) {
					$dataProcessingObject->setIsExecutable();
				}
			}
		}

		$retVal = $dataProcessingObject;

		return $retVal;
	}

}

