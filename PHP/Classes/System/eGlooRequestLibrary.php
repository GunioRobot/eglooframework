<?php
/**
 * eGlooRequestLibrary Class File
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
 * eGlooRequestLibrary
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class eGlooRequestLibrary extends eGlooCombine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'info' => array(),
		'list' => array(),
		'rebuild' => array(),
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
			case 'rebuild' :
				$retVal = $this->rebuild();
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
					echo 'Statements: ' . count($statement_info['statements']) . "\n";

					$longest = 0;

					foreach($statement_info['statements'] as $statement_name => $statement) {
						if (strlen($statement_name) > $longest) {
							$longest = strlen($statement_name);
						}
					}

					foreach($statement_info['statements'] as $statement_name => $statement) {
						$output_string = "\t"; // . ($statement['required'] === true ? '(R) ' : '');
						$output_string .= $statement_name;

						$name_length = $longest - strlen($statement_name);

						if ( ($name_length / 8) < 1 ) {
							$tab_count = 0;
						} else if ( ($name_length / 8) === 1 ) {
							$tab_count = 1;
						} else {
							$tab_count = ceil($name_length / 8);
						}

						for( $i = 0; $i <= $tab_count; $i++ ) {
							$output_string .= "\t";
						}

						$output_string .= 'T=' . $statement['type'];
						$output_string .= "\n";

						echo $output_string;

						echo "\t  " . 'Argument Lists: ' . count($statement['argumentLists']) . "\n";
						foreach( $statement['argumentLists'] as $argument_list_name => $argument_list ) {
							$argument_list_output = "\t    " . $argument_list_name;
							$argument_list_output .= ' (' . $argument_list['parameterPreparation'] . ')';
							$argument_list_output .= "\n";

							echo $argument_list_output;

							foreach( $argument_list['arguments'] as $argument_name => $argument ) {
								$argument_output = "\t      " . $argument_name;
								$argument_output .= "\t" . ' T=' . $argument['argumentType'];

								if ( $argument['argumentType'] === 'string' ) {
									$argument_output .= "\t" . 'Pattern="' . $argument['pattern'] . '"';
								} else if ( $argument['argumentType'] === 'integer' ) {
									$argument_output .= "\t" . 'Min=' . $argument['min'] . "\t" . 'Max=' . $argument['max'];
								}

								$argument_output .= "\n";

								echo $argument_output;
							}
						}

						echo "\t  " . 'Statement Return: ' . $statement['statementReturn']['type'] . "\n";

						echo "\t    " . 'Statement Return Column Sets: ' . count($statement['statementReturn']['statementReturnColumnSets']) . "\n";
						if ( !empty($statement['statementReturn']['statementReturnColumnSets']) ) {
							foreach( $statement['statementReturn']['statementReturnColumnSets'] as $return_column_set_name => $return_column_set ) {
								$return_column_set_output = "\t      " . $return_column_set_name;
								$return_column_set_output .= "\t" . 'T=' . $return_column_set['type'] . ' Pattern="' . $return_column_set['pattern'] . '"';
								$return_column_set_output .= "\n";

								echo $return_column_set_output;
							}
						}

						echo "\t    " . 'Statement Return Columns: ' . count($statement['statementReturn']['statementReturnColumns']) . "\n";
						if ( !empty($statement['statementReturn']['statementReturnColumns']) ) {
							foreach( $statement['statementReturn']['statementReturnColumns'] as $return_column_name => $return_column ) {
								$return_column_output = "\t      " . $return_column_name;

								$return_column_output .= "\t" . 'T=' . $return_column['type'];

								$return_column_output .= "\n";

								echo $return_column_output;
							}
						}

						$engine_modes_used = array();
						foreach( $statement['statementVariants'] as $statement_variant ) {
							foreach( $statement_variant['engineModes'] as $engineMode ) {
								$engine_modes_used[$engineMode['mode']] = $engineMode['modeName'];
							}
						}

						echo "\t    " . 'Statement Variants: ' . count($statement['statementVariants']) . ' Connections, ' .
							count($engine_modes_used) . ' Engine Modes' ."\n";

						if ( !empty($statement['statementVariants']) ) {
							foreach( $statement['statementVariants'] as $statement_variant_name => $statement_variant ) {
								$statement_variant_output = "\t      " . $statement_variant_name . ': ' . count($statement_variant['engineModes']) .
									' Engine Modes' . "\n";

								echo $statement_variant_output;

								foreach( $statement_variant['engineModes'] as $engineMode ) {
									$engine_mode_output = "\t        " . $engineMode['modeName'];
									$engine_mode_output .= "\n";

									echo $engine_mode_output;

									foreach( $engineMode['includePaths'] as $includePath ) {
										$include_path_output = "\t          " . $includePath['argumentList'];
									}
								}

							}
						}

						echo "\n";
					}
				}

				$retVal = true;
			}
		}

		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( eGlooConfiguration::getApplicationPath(), eGlooConfiguration::getUIBundleName() );

		$requestDefinitions = null;

		try {
			$requestDefinitions = $requestValidator->getParsedDefinitionsArrayFromXML();
		} catch ( Exception $e ) {
			// TODO better error handling.  For now this probably means the Forms.xml
			// file was not found locally.  Just print message and move on, since this is
			// just a listing command.
			echo $e->getMessage() . "\n";
		}

		if ( $requestDefinitions !== null ) {
			$requestClasses = $requestDefinitions['requestClasses'];
			$requestAttributeSets = $requestDefinitions['requestAttributeSets'];
			$this->listAll( $requestDefinitions );
			$retVal = true;
		}

		return $retVal;
	}

	public function listAll( $requestDefinitions ) {
		$requestDefinitions['requestClasses'];
		$requestDefinitions['requestAttributeSets'];

		$requestClasses = $requestDefinitions['requestClasses'];
		$requestAttributeSets = $requestDefinitions['requestAttributeSets'];

		$this->listRequestClasses( $requestClasses );
		$this->listRequestAttributeSets( $requestAttributeSets );
	}

	public function listRequestClasses( $requestClasses ) {
		if ( !empty($requestClasses) ) {
			echo 'Request Classes: ' . count($requestClasses) . "\n";

			foreach( $requestClasses as $request_class_id => $request_class ) {
				echo "\t" . $request_class_id . "\n";
			}

			echo "\n";
		}
	}

	public function listRequestAttributeSets( $requestAttributeSets ) {
		if ( !empty($requestAttributeSets) ) {
			echo 'Request Attribute Sets: ' . count($requestAttributeSets) . "\n";

			foreach( $requestAttributeSets as $request_attribute_set_id => $request_attribute_set ) {
				echo "\t" . $request_attribute_set_id . "\n";
			}

			echo "\n";
		}
	}

	protected function rebuild() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( eGlooConfiguration::getApplicationPath(), eGlooConfiguration::getUIBundleName() );

		$requestDefinitions = null;

		try {
			$requestDefinitions = $requestValidator->getParsedDefinitionsArrayFromXML();
		} catch ( Exception $e ) {
			// TODO better error handling.  For now this probably means the Forms.xml
			// file was not found locally.  Just print message and move on, since this is
			// just a listing command.
			echo $e->getMessage() . "\n";
		}

		if ( $requestDefinitions !== null ) {
			// Do stuff
			echo 'Existing Requests.xml processed...' . "\n\n";

			ksort($requestDefinitions['requestClasses']);
			ksort($requestDefinitions['requestAttributeSets']);

			$requestClasses = $requestDefinitions['requestClasses'];
			$requestAttributeSets = $requestDefinitions['requestAttributeSets'];
			$this->listAll( $requestDefinitions );

			// Write out
			$requestValidator->writeDefinitionsXMLFromArray( $requestDefinitions );

			$rebuilt = $requestValidator->getParsedDefinitionsArrayFromXML( './XML/Requests.generated.xml' );

			$diff = eGlooDiff::diff( $requestDefinitions, $rebuilt );

			if ( isset($diff[2]) && isset($diff[2]['d']) && empty($diff[2]['d']) && isset($diff[2]) && isset($diff[2]['i']) && empty($diff[2]['i']) ) {
				echo "\n" . 'Rebuild completed successfully.' . "\n";
			} else if ( isset($diff[2]) && isset($diff[2]['d']) && isset($diff[2]) && isset($diff[2]['i']) ) {
				$diff_output_original = $diff[2]['d'];
				$diff_output_generated = $diff[2]['i'];
				
				echo "\n" . 'Discrepancies found in rebuild: ' . "\n\n";

				echo 'Items found in original but not found in rebuild: ' . "\n\n";
				print_r($diff_output_original);
				echo "\n\n";

				echo 'Items found in rebuild but not found in original: ' . "\n\n";
				print_r($diff_output_generated);
				echo "\n\n";
			}

			$retVal = true;
		}

		return $retVal;
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
			case 'rebuild' :
				$retVal = $this->rebuildCommandRequirementsSatisfied();
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

	protected function rebuildCommandRequirementsSatisfied() {
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
		return 'eGloo Request Help';
	}

	public static function getRequestClass( $requestClass ) {
		
	}

	public static function getRequestID( $requestID ) {
		
	}

	public static function getRequest( $requestClass, $requestID ) {
		
	}

	public static function getRequestURLArrayByRequestProcessorName( $requestProcessorName ) {
		
	}

	public static function getRequestURLArrayByRequestClassName( $requestClassName ) {
		
	}

	public static function getRequestURLArrayByRequestIDName( $requestIDName ) {
		
	}

	public static function getRequestURLArray( $absolute = false, $includeRewriteBase = true ) {
		$retVal = array();

		$cacheGateway = CacheGateway::getCacheGateway();

		$requestNodes = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes', 'RequestValidation' );

		if ( $requestNodes == null ) {
			
		}

		foreach($requestNodes as $requestNode) {
			$url = '';

			if ( $absolute ) {
				// one day this will be a great feature
				$url .= '';
			}
			
			if ( $includeRewriteBase ) {
				$url .= eGlooConfiguration::getRewriteBase();
			}

			$retVal[] = $url . $requestNode['requestClass'] . '/' . $requestNode['requestID'];
		}

		sort($retVal);

		return $retVal;
	}

	public static function getRequestProcessorNameByURL( $url ) {
		
	}

	public static function getRequestProcessorInstanceByUrl( $url ) {
		
	}

}

