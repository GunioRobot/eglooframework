<?php
namespace eGloo\Combine;

use eGloo\Configuration as Configuration;
use eGloo\Logger as Logger;

use eGloo\Performance\Caching\Gateway as CacheGateway;
use eGloo\Security\RequestValidator\ExtendedRequestValidator as ExtendedRequestValidator;

use \Exception as Exception;

/**
 * eGloo\Combine\RequestLibrary Class File
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
 * @category System
 * @package CLI
 * @subpackage Combines
 * @version 1.0
 */

/**
 * eGloo\Combine\RequestLibrary
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package CLI
 * @subpackage Combines
 */
class RequestLibrary extends Combine {

	/**
	 * @var array List of supported commands and their options/required arguments
	 */
	protected static $_supported_commands = array(
		'add' => array(),
		'del' => array(),
		'delete' => array(),
		'info' => array(),
		'list' => array(),
		'mod' => array(),
		'modify' => array(),
		'rebuild' => array(),
		'remove' => array(),
	);

	public function execute() {
		$retVal = null;

		switch( $this->_command ) {
			case 'add' :
				$retVal = $this->add();
				break;
			case 'del' :
			case 'delete' :
			case 'remove' :
				$retVal = $this->remove();
				break;
			case 'info' :
				$retVal = $this->info();
				break;
			case 'list' :
				$retVal = $this->_list();
				break;
			case 'mod' :
			case 'modify' :
				$retVal = $this->modify();
				break;
			case 'rebuild' :
				$retVal = $this->rebuild();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function add() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( Configuration::getApplicationPath(), Configuration::getUIBundleName() );

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
			Logger::writeLog( Logger::INFO, 'Existing Requests.xml processed...' . "\n" );

			ksort($requestDefinitions['requestClasses']);
			ksort($requestDefinitions['requestAttributeSets']);

			$requestClasses = $requestDefinitions['requestClasses'];
			$requestAttributeSets = $requestDefinitions['requestAttributeSets'];

			$addition_id = '';
			$addition_type = '';

			$write_definitions = false;

			foreach( $this->_command_arguments as $command_argument_key => $command_argument ) {
				switch( strtolower($command_argument) ) {
					case 'rc' :
					case 'reqc' :
					case 'reqclass' :
					case 'requestclass' :
						$write_definitions = true;

						if ( isset($this->_command_arguments[$command_argument_key + 1]) ) {
							$addition_id = $this->_command_arguments[$command_argument_key + 1];
							$addition_type = 'Request Class';

							$requestDefinitions = $this->addRequestClass( $this->_command_arguments[$command_argument_key + 1], $requestDefinitions );
						} else {
							echo 'No Request Class ID provided' . "\n";
						}
						break;
					case 'ras' :
					case 'reqas' :
					case 'reqattrset' :
					case 'requestattributeset' :
						$write_definitions = true;

						if ( isset($this->_command_arguments[$command_argument_key + 1]) ) {
							$addition_id = $this->_command_arguments[$command_argument_key + 1];
							$addition_type = 'Request Attribute Set';

							$requestDefinitions = $this->addRequestAttributeSet( $this->_command_arguments[$command_argument_key + 1], $requestDefinitions );
						} else {
							echo 'No Request Attribute Set ID provided' . "\n";
						}
						break;
					default :
						break;
				}
			}

			if ( $write_definitions ) {
				if ( !empty($requestDefinitions) && $requestDefinitions !== null ) {
					// Figure out where to write to
					if ( isset($this->_parsed_options['w']) ) {
						$output_location = './XML/Requests.xml';
					} else {
						$output_location = './XML/Requests.generated.xml';
					}

					// Write out
					$requestValidator->writeDefinitionsXMLFromArray( $requestDefinitions, true, $output_location );
					$rebuilt = $requestValidator->getParsedDefinitionsArrayFromXML( $output_location );

					$diff = \eGlooDiff::diff( $requestDefinitions, $rebuilt );

					// echo_r($rebuilt['requestAttributeSets']);
					echo_r(array_keys($diff));
					
					// SO....... the diff method stops when the diff hits... a diff.  I need to rethink this below
die;
					if ( isset($diff[2]) && isset($diff[2]['d']) && empty($diff[2]['d']) && isset($diff[2]) && isset($diff[2]['i']) && empty($diff[2]['i']) ) {
						echo $addition_type . ' "' . $addition_id . '" added successfully.' . "\n";
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
				} else {
					$retVal = false;
				}
			} else {
				echo 'No valid addition type specified.  Try "egloo requests help"' . "\n";
			}

		}

		return $retVal;
	}

	protected function info() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( Configuration::getApplicationPath(), Configuration::getUIBundleName() );

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
			Logger::writeLog( Logger::INFO, 'Existing Requests.xml processed...' . "\n" );

			ksort($requestDefinitions['requestClasses']);
			ksort($requestDefinitions['requestAttributeSets']);

			$requestClasses = $requestDefinitions['requestClasses'];
			$requestAttributeSets = $requestDefinitions['requestAttributeSets'];

			$addition_id = '';
			$addition_type = '';

			foreach( $this->_command_arguments as $command_argument_key => $command_argument ) {
				switch( strtolower($command_argument) ) {
					case 'rc' :
					case 'reqc' :
					case 'reqclass' :
					case 'requestclass' :
						if ( isset($this->_command_arguments[$command_argument_key + 1]) ) {
							$addition_id = $this->_command_arguments[$command_argument_key + 1];
							$addition_type = 'Request Class';

							$requestDefinitions = $this->addRequestClass( $requestDefinitions, $this->_command_arguments[$command_argument_key + 1] );
						} else {
							echo 'No Request Class ID provided' . "\n";
						}
						break;
					default :
						break;
				}
			}

			if ( !empty($requestDefinitions) && $requestDefinitions !== null ) {
				// Figure out where to write to
				if ( isset($this->_parsed_options['w']) ) {
					$output_location = './XML/Requests.xml';
				} else {
					$output_location = './XML/Requests.generated.xml';
				}

				// Write out
				$requestValidator->writeDefinitionsXMLFromArray( $requestDefinitions, true, $output_location );
				$rebuilt = $requestValidator->getParsedDefinitionsArrayFromXML( $output_location );

				$diff = \eGlooDiff::diff( $requestDefinitions, $rebuilt );

				if ( isset($diff[2]) && isset($diff[2]['d']) && empty($diff[2]['d']) && isset($diff[2]) && isset($diff[2]['i']) && empty($diff[2]['i']) ) {
					echo $addition_type . ' "' . $addition_id . '" added successfully.' . "\n";
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
			} else {
				$retVal = false;
			}
		}

		return $retVal;
	}

	// PHP is dumb - 'list' should be a valid method name
	protected function _list() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( Configuration::getApplicationPath(), Configuration::getUIBundleName() );

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

	protected function modify() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( Configuration::getApplicationPath(), Configuration::getUIBundleName() );

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
			Logger::writeLog( Logger::INFO, 'Existing Requests.xml processed...' . "\n" );

			ksort($requestDefinitions['requestClasses']);
			ksort($requestDefinitions['requestAttributeSets']);

			$requestClasses = $requestDefinitions['requestClasses'];
			$requestAttributeSets = $requestDefinitions['requestAttributeSets'];

			$addition_id = '';
			$addition_type = '';

			foreach( $this->_command_arguments as $command_argument_key => $command_argument ) {
				switch( strtolower($command_argument) ) {
					case 'rc' :
					case 'reqc' :
					case 'reqclass' :
					case 'requestclass' :
						if ( isset($this->_command_arguments[$command_argument_key + 1]) ) {
							$addition_id = $this->_command_arguments[$command_argument_key + 1];
							$addition_type = 'Request Class';

							$requestDefinitions = $this->addRequestClass( $requestDefinitions, $this->_command_arguments[$command_argument_key + 1] );
						} else {
							echo 'No Request Class ID provided' . "\n";
						}
						break;
					default :
						break;
				}
			}

			if ( !empty($requestDefinitions) && $requestDefinitions !== null ) {
				// Figure out where to write to
				if ( isset($this->_parsed_options['w']) ) {
					$output_location = './XML/Requests.xml';
				} else {
					$output_location = './XML/Requests.generated.xml';
				}

				// Write out
				$requestValidator->writeDefinitionsXMLFromArray( $requestDefinitions, true, $output_location );
				$rebuilt = $requestValidator->getParsedDefinitionsArrayFromXML( $output_location );

				$diff = \eGlooDiff::diff( $requestDefinitions, $rebuilt );

				if ( isset($diff[2]) && isset($diff[2]['d']) && empty($diff[2]['d']) && isset($diff[2]) && isset($diff[2]['i']) && empty($diff[2]['i']) ) {
					echo $addition_type . ' "' . $addition_id . '" added successfully.' . "\n";
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
			} else {
				$retVal = false;
			}
		}

		return $retVal;
	}

	protected function rebuild() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( Configuration::getApplicationPath(), Configuration::getUIBundleName() );

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

			$diff = \eGlooDiff::diff( $requestDefinitions, $rebuilt );

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

	protected function remove() {
		$retVal = false;

		// Get a request validator based on the current application and UI bundle
		$requestValidator =
			ExtendedRequestValidator::getInstance( Configuration::getApplicationPath(), Configuration::getUIBundleName() );

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
			Logger::writeLog( Logger::INFO, 'Existing Requests.xml processed...' . "\n" );

			ksort($requestDefinitions['requestClasses']);
			ksort($requestDefinitions['requestAttributeSets']);

			$requestClasses = $requestDefinitions['requestClasses'];
			$requestAttributeSets = $requestDefinitions['requestAttributeSets'];

			$removal_id = '';
			$removal_type = '';

			foreach( $this->_command_arguments as $command_argument_key => $command_argument ) {
				switch( strtolower($command_argument) ) {
					case 'rc' :
					case 'reqc' :
					case 'reqclass' :
					case 'requestclass' :
						if ( isset($this->_command_arguments[$command_argument_key + 1]) ) {
							$removal_id = $this->_command_arguments[$command_argument_key + 1];
							$removal_type = 'Request Class';

							$requestDefinitions = $this->removeRequestClass( $this->_command_arguments[$command_argument_key + 1], $requestDefinitions );
						} else {
							echo 'No Request Class ID provided' . "\n";
						}
						break;
					default :
						break;
				}
			}

			if ( !empty($requestDefinitions) && $requestDefinitions !== null ) {
				// Figure out where to write to
				if ( isset($this->_parsed_options['w']) ) {
					$output_location = './XML/Requests.xml';
				} else {
					$output_location = './XML/Requests.generated.xml';
				}

				// Write out
				$requestValidator->writeDefinitionsXMLFromArray( $requestDefinitions, true, $output_location );
				$rebuilt = $requestValidator->getParsedDefinitionsArrayFromXML( $output_location );

				$diff = \eGlooDiff::diff( $requestDefinitions, $rebuilt );

				if ( isset($diff[2]) && isset($diff[2]['d']) && empty($diff[2]['d']) && isset($diff[2]) && isset($diff[2]['i']) && empty($diff[2]['i']) ) {
					echo $removal_type . ' "' . $removal_id . '" removed successfully.' . "\n";
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
			} else {
				$retVal = false;
			}
		}

		return $retVal;
	}

	public function commandRequirementsSatisfied() {
		$retVal = false;

		switch( $this->_command ) {
			case 'add' :
				$retVal = $this->addCommandRequirementsSatisfied();
				break;
			case 'del' :
			case 'delete' :
			case 'remove' :
				$retVal = $this->removeCommandRequirementsSatisfied();
				break;
			case 'info' :
				$retVal = $this->infoCommandRequirementsSatisfied();
				break;
			case 'list' :
				$retVal = $this->listCommandRequirementsSatisfied();
				break;
			case 'mod' :
			case 'modify' :
				$retVal = $this->modifyCommandRequirementsSatisfied();
				break;
			case 'rebuild' :
				$retVal = $this->rebuildCommandRequirementsSatisfied();
				break;
			default :
				break;
		}

		return $retVal;
	}

	protected function addCommandRequirementsSatisfied() {
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


	protected function modifyCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	protected function rebuildCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	protected function removeCommandRequirementsSatisfied() {
		$retVal = false;

		$retVal = true;

		return $retVal;
	}

	protected function addRequestClass( $request_class, $request_definitions = null ) { 
		$retVal = null;

		if ( !$this->issetRequestClass( $request_class, $request_definitions ) ) {
			if ( is_string($request_class) ) {
				$new_request_class = array( 'requestClass' => $request_class, 'requests' => array() );
				echo_r($request_definitions);
				$request_definitions['requestClasses'][$request_class] = $new_request_class;
				ksort($request_definitions['requestClasses']);
				$retVal = $request_definitions;
			} else if ( is_array($request_class) ) {
				
			}
		} else {
			// TODO this isn't right
			if ( is_string($request_class) ) {
				echo 'Request Class "' . $request_class . '" already exists.' . "\n";
			} else if ( is_array($request_class) ) {
				echo 'Request Class "' . $request_class['requestClass'] . '" already exists.' . "\n";
			}
		}

		return $retVal;
	}

	protected function getRequestClass( $request_class_id, $request_definitions = null ) { 
		$retVal = null;

		if ( $this->issetRequestClass( $request_class_id, $request_definitions ) ) {
			
		} else {
			echo 'Request Class "' . $request_class_id . '" not found.' . "\n";
		}

		return $retVal;
	}

	protected function issetRequestClass( $request_class, $request_definitions = null ) { 
		$retVal = null;

		if ( is_string($request_class) ) {
			$retVal = isset($request_definitions['requestClasses'][$request_class]);
		} else if ( is_array($request_class) ) {
			
		}

		return $retVal;
	}

	protected function removeRequestClass( $request_class_id, $request_definitions = null ) { 
		$retVal = null;

		if ( $this->issetRequestClass( $request_class_id, $request_definitions ) ) {
			unset($request_definitions['requestClasses'][$request_class_id]);
			ksort($request_definitions['requestClasses']);
			$retVal = $request_definitions;
		} else {
			echo 'Request Class "' . $request_class_id . '" not found.' . "\n";
		}

		return $retVal;
	}

	protected function updateRequestClass( $request_class_id, $request_definitions = null ) { 
		$retVal = null;

		if ( $this->issetRequestClass( $request_class_id, $request_definitions ) ) {
			
		} else {
			echo 'Request Class "' . $request_class_id . '" not found.' . "\n";
		}

		return $retVal;
	}

	protected function addRequestAttributeSet( $request_attribute_set, $request_definitions = null ) { 
		$retVal = null;

		if ( !$this->issetRequestClass( $request_attribute_set, $request_definitions ) ) {
			if ( is_string($request_attribute_set) ) {
				$new_request_attribute_set =
					array(
						'attributeSet' => $request_attribute_set,
						'attributes' =>
							array(
								'boolArguments' => array(),
								'complexArguments' => array(),
								'decorators' => array(),
								'depends' => array(),
								'formArguments' => array(),
								'initRoutines' => array(),
								'selectArguments' => array(),
								'variableArguments' => array(),
							),
					);

				// Because we need to guarantee order for the diff later.  
				ksort($new_request_attribute_set['attributes']);

				$request_definitions['requestAttributeSets'][$request_attribute_set] = $new_request_attribute_set;
				ksort($request_definitions['requestAttributeSets']);
				$retVal = $request_definitions;
			} else if ( is_array($request_attribute_set) ) {
				
			}
		} else {
			// TODO this isn't right
			echo 'Request Attribute Set "' . $request_attribute_set['requestAttributeSet'] . '" already exists.' . "\n";
		}

		return $retVal;
	}

	protected function getRequestAttributeSet( $request_attribute_set_id, $request_definitions = null ) { 
		$retVal = null;

		return $retVal;
	}

	protected function issetRequestAttributeSet( $request_attribute_set_id, $request_definitions = null ) { 
		$retVal = null;

		return $retVal;
	}

	protected function removeRequestAttributeSet( $request_attribute_set_id, $request_definitions = null ) { 
		$retVal = null;

		return $retVal;
	}

	protected function updateRequestAttributeSet( $request_attribute_set_id, $request_definitions = null ) { 
		$retVal = null;

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

	// public static function getRequestClass( $requestClass ) {
	// 	
	// }

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

		$requestNodes = $cacheGateway->getObject( Configuration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes', 'RequestValidation' );

		if ( $requestNodes == null ) {
			
		}

		foreach($requestNodes as $requestNode) {
			$url = '';

			if ( $absolute ) {
				// one day this will be a great feature
				$url .= '';
			}
			
			if ( $includeRewriteBase ) {
				$url .= Configuration::getRewriteBase();
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

