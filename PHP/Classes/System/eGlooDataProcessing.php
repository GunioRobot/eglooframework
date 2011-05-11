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
class eGlooDataProcessing extends eGlooCombine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'info' => array(),
		'list' => array(),
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

	/**
	 * Return the help information for this class as a string
	 *
	 * @return string the help information for this class
	 * @author George Cooper
	 **/
	public static function getHelpString() {
		return 'eGloo Data Processing Help';
	}

}

