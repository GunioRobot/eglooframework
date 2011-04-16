<?php
/**
 * XML2ArrayDPDefinitionParser Class File
 *
 * Contains the class definition for the XML2ArrayDPDefinitionParser
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
 * XML2ArrayDPDefinitionParser
 * 
 * Validates requests against specification from requests definition file (DataProcessing.xml)
 * This is a specific subclass implementation of the eGlooDPDefinitionParser.
 *
 * @package DataProcessing
 * @subpackage Security
 */
final class XML2ArrayDPDefinitionParser extends eGlooDPDefinitionParser {

	/**
	 * Static Data Members
	 */

	// Singleton data member to enforce the singleton pattern for eGlooDPDefinitionParser subclasses
	protected static $singleton;

	/**
	 * Method to load data processing nodes from DataProcessing.xml definitions file
	 * 
	 * @throws ErrorException	if definition file cannot be read, has syntax errors, is missing
	 *							required values or provides invalid values
	 */
	protected function loadRequestNodes() {
		// Mark entrance into this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: Entered loadRequestNodes()", 'DataProcessing' );

		// Grab the absolute file system path to the DataProcessing.xml we're concerned with.  $this->webapp is set
		// during construction of this XML2ArrayDPDefinitionParser singleton.  See eGlooDPDefinitionParser
		// for details.
		$dp_xml_path = eGlooConfiguration::getApplicationsPath() . '/' . eGlooConfiguration::getApplicationPath() . "/XML/DataProcessing.xml";

		// Mark that we are now attempting to load the specified DataProcessing.xml
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: Loading " . $dp_xml_path, 'DataProcessing' );

		// Attempt to load the specified DataProcessing.xml file
		$dataProcessingXMLObject = simplexml_load_file( $dp_xml_path );

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		// If reading the DataProcessing.xml file failed, log the error
		// TODO determine if we should throw an exception here...
		if ( !$dataProcessingXMLObject ) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'XML2ArrayDPDefinitionParser: simplexml_load_file( "' . $dp_xml_path . '" ): ' . libxml_get_errors() );
		}

		// Setup an array to hold all of our processed DPSequence definitions
		$dataProcessingSequences = array();

		// Iterate over the DPSequence nodes so that we can parse each DPSequence definition
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPSequence' ) as $dpSequence ) {
			// Grab the ID for this particular DPSequence
			$dpSequenceID = isset($dpSequence['id']) ? (string) $dpSequence['id'] : NULL;

			// If no ID is set for this DPSequence, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dpSequenceID || trim($dpSequenceID) === '' ) {
				throw new ErrorException("No ID specified in DPSequence. Please review your DataProcessing.xml");
			}

			// Assign an array to hold this DPSequence node definition.  Associative key is the DPSequence ID
			$dataProcessingSequences[$dpSequenceID] = array('dataProcessingSequence' => $dpSequenceID, 'attributes' => array());

			// Arguments
			$dataProcessingSequences[$dpSequenceID]['attributes']['boolArguments'] = array();

			foreach( $dpSequence->xpath( 'child::BoolArgument' ) as $boolArgument ) {
				$newBoolArgument = array();

				$newBoolArgument['id'] = (string) $boolArgument['id'];
				$newBoolArgument['type'] = strtolower( (string) $boolArgument['type'] );
				$newBoolArgument['required'] = strtolower( (string) $boolArgument['required'] );

				if ($newBoolArgument['required'] === 'false' && isset($boolArgument['default'])) {
					$defaultBoolValue = strtolower( (string) $boolArgument['default'] );
					
					if ($defaultBoolValue === 'true' || $defaultBoolValue === 'false') {
						$newBoolArgument['default'] = $defaultBoolValue;
					}
				}

				$dataProcessingSequences[$dpSequenceID]['attributes']['boolArguments'][$newBoolArgument['id']] = $newBoolArgument;
			}

			$dataProcessingSequences[$dpSequenceID]['attributes']['selectArguments'] = array();

			foreach( $dpSequence->xpath( 'child::SelectArgument' ) as $selectArgument ) {
				$newSelectArgument = array();

				$newSelectArgument['id'] = (string) $selectArgument['id'];
				$newSelectArgument['type'] = strtolower( (string) $selectArgument['type'] );
				$newSelectArgument['required'] = strtolower( (string) $selectArgument['required'] );

				if ( isset($selectArgument['scalarType']) ) {
					$newSelectArgument['scalarType'] =	strtolower( (string) $selectArgument['scalarType'] );
				}

				$newSelectArgument['values'] = array();

				foreach( $selectArgument->xpath( 'child::value' ) as $selectArgumentValue ) {
					$newSelectArgument['values'][] = (string) $selectArgumentValue;
				}

				if ($newSelectArgument['required'] === 'false' && isset($selectArgument['default'])) {
					$defaultSelectValue = (string) $selectArgument['default'];
					
					if (in_array($defaultSelectValue, $newSelectArgument['values'])) {
						$newSelectArgument['default'] = $defaultSelectValue;
					}
				}

				$dataProcessingSequences[$dpSequenceID]['attributes']['selectArguments'][$newSelectArgument['id']] = $newSelectArgument;
			}

			$dataProcessingSequences[$dpSequenceID]['attributes']['variableArguments'] = array();

			foreach( $dpSequence->xpath( 'child::VariableArgument' ) as $variableArgument ) {
				$newVariableArgument = array();

				$newVariableArgument['id'] = (string) $variableArgument['id'];
				$newVariableArgument['type'] = strtolower( (string) $variableArgument['type'] );
				$newVariableArgument['required'] = strtolower( (string) $variableArgument['required'] );
				$newVariableArgument['regex'] = (string) $variableArgument['regex'];

				if ( isset($variableArgument['scalarType']) ) {
					$newVariableArgument['scalarType'] =  (string) $variableArgument['scalarType'];
				}

				if ($newVariableArgument['required'] === 'false' && isset($variableArgument['default']) && $newVariableArgument['type'] !== 'postarray') {
					$defaultVariableValue = (string) $variableArgument['default'];

					if (preg_match( $newVariableArgument['regex'], $defaultVariableValue )) {
						$newVariableArgument['default'] = $defaultVariableValue;
					}
				}

				$dataProcessingSequences[$dpSequenceID]['attributes']['variableArguments'][$newVariableArgument['id']] = $newVariableArgument;
			}

			$dataProcessingSequences[$dpSequenceID]['attributes']['formArguments'] = array();

			foreach( $dpSequence->xpath( 'child::FormArgument' ) as $formArgument ) {
				$newFormArgument = array();

				$newFormArgument['id'] = (string) $formArgument['id'];
				$newFormArgument['type'] = strtolower( (string) $formArgument['type'] );
				$newFormArgument['required'] = strtolower( (string) $formArgument['required'] );
				$newFormArgument['formID'] = (string) $formArgument['formID'];

				$dataProcessingSequences[$dpSequenceID]['attributes']['formArguments'][$newFormArgument['id']] = $newFormArgument;
			}

			$dataProcessingSequences[$dpSequenceID]['attributes']['complexArguments'] = array();

			foreach( $dpSequence->xpath( 'child::ComplexArgument' ) as $complexArgument ) {
				$newComplexArgument = array();

				$newComplexArgument['id'] = (string) $complexArgument['id'];
				$newComplexArgument['type'] = strtolower( (string) $complexArgument['type'] );
				$newComplexArgument['required'] = strtolower( (string) $complexArgument['required'] );
				$newComplexArgument['validator'] = (string) $complexArgument['validator'];

				if ( isset($complexArgument['scalarType']) ) {
					$newComplexArgument['scalarType'] =	 (string) $complexArgument['scalarType'];
				} else if ( isset($complexArgument['complexType']) ) {
					$newComplexArgument['complexType'] =  (string) $complexArgument['complexType'];
				}

				$dataProcessingSequences[$dpSequenceID]['attributes']['complexArguments'][$newComplexArgument['id']] = $newComplexArgument;
			}

			$dataProcessingSequences[$dpSequenceID]['attributes']['depends'] = array();

			foreach( $dpSequence->xpath( 'child::Depend' ) as $depend ) {
				$newDepend = array();

				$newDepend['id'] = (string) $depend['id'];
				$newDepend['type'] = (string) $depend['type'];
				$newDepend['children'] = array();

				foreach( $depend->xpath( 'child::Child' ) as $dependChild ) {
					$dependChildID = (string) $dependChild['id'];
					$dependChildType = strtolower( (string) $dependChild['type'] );

					$newDepend['children'][] = array('id' => $dependChildID, 'type' => $dependChildType);
				}

				$dataProcessingSequences[$dpSequenceID]['attributes']['depends'][$newDepend['id']] = $newDepend;
			}

			// Decorators
			$dataProcessingSequences[$dpSequenceID]['attributes']['decorators'] = array();

			foreach( $dpSequence->xpath( 'child::Decorator' ) as $decorator ) {
				$newDecorator = array();

				$newDecorator['decoratorID'] = (string) $decorator['decoratorID'];
				$newDecorator['order'] = (string) $decorator['order'];

				$dataProcessingSequences[$dpSequenceID]['attributes']['decorators'][$newDecorator['decoratorID']] = $newDecorator;
			}

			// Init Routines
			$dataProcessingSequences[$dpSequenceID]['attributes']['initRoutines'] = array();

			foreach( $dpSequence->xpath( 'child::InitRoutine' ) as $initRoutine ) {
				$newInitRoutine = array();

				$newInitRoutine['initRoutineID'] = (string) $initRoutine['initRoutineID'];
				$newInitRoutine['order'] = (string) $initRoutine['order'];

				$dataProcessingSequences[$dpSequenceID]['attributes']['initRoutines'][$newInitRoutine['initRoutineID']] = $newInitRoutine;
			}

			$uniqueKey = ((string) $dpSequence['id']);
			$this->dataProcessingSequences[ $uniqueKey ] = $dataProcessingSequences[$dpSequenceID];

			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
			// and do more granulated inspection and cache clearing
			$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

			$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeNodes::' .
				$uniqueKey, $dataProcessingSequences[$dpSequenceID], 'DataProcessing', 0, true );
		}

		// We're done processing our DPSequences, so let's store the structured array in cache for faster lookup
		// For cache properties, the ttl is forever (0) and we can keep the cache piping hot by storing a local copy (true)
		$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserAttributeSets',
			$this->dataProcessingSequences, 'DataProcessing', 0, true );

		// Setup an array to hold all of our processed DPSequence definitions
		$dataProcessingStatements = array();

		// Iterate over the DPStatement nodes so that we can parse each request definition
		foreach( $dataProcessingXMLObject->xpath( '/tns:DataProcessing/DPStatement' ) as $dataProcessingStatement ) {
			// Grab the ID for this particular DPStatement
			$dataProcessingStatementID = isset($dataProcessingStatement['id']) ? (string) $dataProcessingStatement['id'] : NULL;

			// If no ID is set for this DPStatement, this is not a valid DataProcessing.xml and we should get out of here
			if ( !$dataProcessingStatementID || trim($dataProcessingStatementID) === '' ) {
				throw new ErrorException("No ID specified in request class.	 Please review your DataProcessing.xml");
			}

			// Assign an array to hold this DPStatement node definition.  Associative key is the DPStatement ID
			$dataProcessingStatements[$dataProcessingStatementID] = array('dataProcessingStatement' => $dataProcessingStatementID, 'requests' => array());

			// Iterate over the Request nodes for this DPStatement so that we can parse each request definition
			foreach( $dataProcessingStatement->xpath( 'child::Request' ) as $request ) {
				// Grab the ID for this particular Request
				$requestID = isset($request['id']) ? (string) $request['id'] : NULL;

				// Grab the name of the RequestProcessor specified to handle this particular Request
				// Example:
				// $requestProcessor = new $processorID();
				// $requestProcessor->processRequest();
				$processorID = isset($request['processorID']) ? (string) $request['processorID'] : NULL;

				// Grab the name of the RequestProcessor specified to handle errors for this particular Request
				// Example:
				// $errorRequestProcessor = new $errorProcessorID();
				// $errorRequestProcessor->processErrorRequest();
				$errorProcessorID = isset($request['errorProcessorID']) ? (string) $request['errorProcessorID'] : NULL;

				// If no ID is set for this Request, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$requestID || trim($requestID) === '' ) {
					throw new ErrorException("No request ID specified in request class: '" . $dataProcessingStatementID .
						"'.	 Please review your DataProcessing.xml");
				}

				// If no RequestProcessor is specified, this is not a valid DataProcessing.xml and we should get out of here
				if ( !$processorID || trim($processorID) === '' ) {
					throw new ErrorException("No processor ID specified in request ID: '" . $requestID .
					"'.	 Please review your DataProcessing.xml");
				}

				// Request Properties
				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID] =
					array('dataProcessingStatement' => $dataProcessingStatementID, 'requestID' => $requestID, 'processorID' => $processorID, 'errorProcessorID' => $errorProcessorID );

				// Arguments
				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'] = array();

				foreach( $request->xpath( 'child::BoolArgument' ) as $boolArgument ) {
					$newBoolArgument = array();

					$newBoolArgument['id'] = (string) $boolArgument['id'];
					$newBoolArgument['type'] = strtolower( (string) $boolArgument['type'] );
					$newBoolArgument['required'] = strtolower( (string) $boolArgument['required'] );

					if ($newBoolArgument['required'] === 'false' && isset($boolArgument['default'])) {
						$defaultBoolValue = strtolower( (string) $boolArgument['default'] );
						
						if ($defaultBoolValue === 'true' || $defaultBoolValue === 'false') {
							$newBoolArgument['default'] = $defaultBoolValue;
						}
					}

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'][$newBoolArgument['id']] = $newBoolArgument;
				}

				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'] = array();

				foreach( $request->xpath( 'child::SelectArgument' ) as $selectArgument ) {
					$newSelectArgument = array();

					$newSelectArgument['id'] = (string) $selectArgument['id'];
					$newSelectArgument['type'] = strtolower( (string) $selectArgument['type'] );
					$newSelectArgument['required'] = strtolower( (string) $selectArgument['required'] );

					if ( isset($selectArgument['scalarType']) ) {
						$newSelectArgument['scalarType'] = strtolower( (string) $selectArgument['scalarType'] );
					}

					$newSelectArgument['values'] = array();

					foreach( $selectArgument->xpath( 'child::value' ) as $selectArgumentValue ) {
						$newSelectArgument['values'][] = (string) $selectArgumentValue;
					}

					if ($newSelectArgument['required'] === 'false' && isset($selectArgument['default'])) {
						$defaultSelectValue = (string) $selectArgument['default'];
						
						if (in_array($defaultSelectValue, $newSelectArgument['values'])) {
							$newSelectArgument['default'] = $defaultSelectValue;
						}
					}

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'][$newSelectArgument['id']] = $newSelectArgument;
				}

				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'] = array();

				foreach( $request->xpath( 'child::VariableArgument' ) as $variableArgument ) {
					$newVariableArgument = array();

					$newVariableArgument['id'] = (string) $variableArgument['id'];
					$newVariableArgument['type'] = strtolower( (string) $variableArgument['type'] );
					$newVariableArgument['required'] = strtolower( (string) $variableArgument['required'] );
					$newVariableArgument['regex'] = (string) $variableArgument['regex'];

					if ( isset($variableArgument['scalarType']) ) {
						$newVariableArgument['scalarType'] = (string) $variableArgument['scalarType'];
					}

					if ($newVariableArgument['required'] === 'false' && isset($variableArgument['default']) && $newVariableArgument['type'] !== 'postarray') {
						$defaultVariableValue = (string) $variableArgument['default'];

						if (preg_match( $newVariableArgument['regex'], $defaultVariableValue )) {
							$newVariableArgument['default'] = $defaultVariableValue;
						}
					}

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'][$newVariableArgument['id']] = $newVariableArgument;
				}

				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['formArguments'] = array();

				foreach( $request->xpath( 'child::FormArgument' ) as $formArgument ) {
					$newFormArgument = array();

					$newFormArgument['id'] = (string) $formArgument['id'];
					$newFormArgument['type'] = strtolower( (string) $formArgument['type'] );
					$newFormArgument['required'] = strtolower( (string) $formArgument['required'] );
					$newFormArgument['formID'] = (string) $formArgument['formID'];

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['formArguments'][$newFormArgument['id']] = $newFormArgument;
				}

				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'] = array();

				foreach( $request->xpath( 'child::ComplexArgument' ) as $complexArgument ) {
					$newComplexArgument = array();

					$newComplexArgument['id'] = (string) $complexArgument['id'];
					$newComplexArgument['type'] = strtolower( (string) $complexArgument['type'] );
					$newComplexArgument['required'] = strtolower( (string) $complexArgument['required'] );
					$newComplexArgument['validator'] = (string) $complexArgument['validator'];

					if ( isset($complexArgument['scalarType']) ) {
						$newComplexArgument['scalarType'] =	 (string) $complexArgument['scalarType'];
					} else if ( isset($complexArgument['complexType']) ) {
						$newComplexArgument['complexType'] =  (string) $complexArgument['complexType'];
					}

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'][$newComplexArgument['id']] = $newComplexArgument;
				}

				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'] = array();

				foreach( $request->xpath( 'child::Depend' ) as $depend ) {
					$newDepend = array();

					$newDepend['id'] = (string) $depend['id'];
					$newDepend['type'] = (string) $depend['type'];
					$newDepend['children'] = array();

					foreach( $depend->xpath( 'child::Child' ) as $dependChild ) {
						$dependChildID = (string) $dependChild['id'];
						$dependChildType = strtolower( (string) $dependChild['type'] );

						$newDepend['children'][] = array('id' => $dependChildID, 'type' => $dependChildType);
					}

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'][$newDepend['id']] = $newDepend;
				}

				// Decorators
				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'] = array();

				foreach( $request->xpath( 'child::Decorator' ) as $decorator ) {
					$newDecorator = array();

					$newDecorator['decoratorID'] = (string) $decorator['decoratorID'];
					$newDecorator['order'] = (string) $decorator['order'];

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'][$newDecorator['decoratorID']] = $newDecorator;
				}

				// InitRoutines
				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['initRoutines'] = array();

				foreach( $request->xpath( 'child::InitRoutine' ) as $initRoutine ) {
					$newInitRoutine = array();

					$newInitRoutine['initRoutineID'] = (string) $initRoutine['initRoutineID'];
					$newInitRoutine['order'] = (string) $initRoutine['order'];

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['initRoutines'][$newInitRoutine['initRoutineID']] = $newInitRoutine;
				}

				// Request Attribute Set Includes
				$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['requestAttributeSetIncludes'] = array();

				foreach( $request->xpath( 'child::DPSequenceInclude' ) as $requestAttributeSetInclude ) {
					$newDPSequenceInclude = array();

					$newDPSequenceInclude['requestAttributeSetID'] = (string) $requestAttributeSetInclude['requestAttributeSetID'];
					$newDPSequenceInclude['priority'] = (string) $requestAttributeSetInclude['priority'];

					$newInsertArrayID = $newDPSequenceInclude['requestAttributeSetID'];

					$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['requestAttributeSetIncludes'][$newInsertArrayID] = $newDPSequenceInclude;
				}

				foreach( $dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['requestAttributeSetIncludes'] as $requestAttributeSetInclude ) {
					$requestAttributeSetID = $requestAttributeSetInclude['requestAttributeSetID'];
					$priority = $requestAttributeSetInclude['priority'];

					$requestAttributeSet = $this->dataProcessingSequences[$requestAttributeSetID];

					$boolArguments = $requestAttributeSet['attributes']['boolArguments'];
					$selectArguments = $requestAttributeSet['attributes']['selectArguments'];
					$variableArguments = $requestAttributeSet['attributes']['variableArguments'];
					$complexArguments = $requestAttributeSet['attributes']['complexArguments'];
					$depends = $requestAttributeSet['attributes']['depends'];
					$decorators = $requestAttributeSet['attributes']['decorators'];

					foreach( $boolArguments as $boolArgument ) {
						if ( !isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'][$boolArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting bool argument ' . $boolArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'][$boolArgument['id']] = $boolArgument;
						} else if ( isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'][$boolArgument['id']]['priority']) &&
								$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'][$boolArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence bool argument ' . $boolArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['boolArguments'][$boolArgument['id']] = $boolArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence bool argument ' . $boolArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'DataProcessing' );
						}
					}

					foreach( $selectArguments as $selectArgument ) {
						if ( !isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'][$selectArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting select argument ' . $selectArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'][$selectArgument['id']] = $selectArgument;
						} else if ( isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'][$selectArgument['id']]['priority']) &&
								$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'][$selectArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence select argument ' . $selectArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['selectArguments'][$selectArgument['id']] = $selectArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence select argument ' . $selectArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'DataProcessing' );
						}
					}

					foreach( $variableArguments as $variableArgument ) {
						if ( !isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'][$variableArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting variable argument ' . $variableArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'][$variableArgument['id']] = $variableArgument;
						} else if ( isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'][$variableArgument['id']]['priority']) &&
								$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'][$variableArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence variable argument ' . $variableArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['variableArguments'][$variableArgument['id']] = $variableArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence variable argument ' . $variableArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'DataProcessing' );
						}
					}

					foreach( $complexArguments as $complexArgument ) {
						if ( !isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'][$complexArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting complex argument ' . $complexArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'][$complexArgument['id']] = $complexArgument;
						} else if ( isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'][$complexArgument['id']]['priority']) &&
								$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'][$complexArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence complex argument ' . $complexArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['complexArguments'][$complexArgument['id']] = $complexArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence complex argument ' . $complexArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'DataProcessing' );
						}
					}

					foreach( $depends as $depend ) {
						if ( !isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'][$depend['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting depend ' . $depend['id'] .
								' from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'][$depend['id']] = $depend;
						} else if ( isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'][$depend['id']]['priority']) &&
								$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'][$depend['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence depend ' . $depend['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['depends'][$depend['id']] = $depend;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence depend ' . $depend['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'DataProcessing' );
						}
					}

					$existingDecoratorCount = count($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators']);

					foreach( $decorators as $decorator ) {
						if ( !isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting decorator ' . $decorator['decoratorID'] .
								' from attribute set ' . $requestAttributeSetID , 'DataProcessing' );

							$decorator['order'] += $existingDecoratorCount;
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'][$decorator['decoratorID']] = $decorator;
						} else if ( isset($dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]['priority']) &&
								$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence decorator ' . $decorator['decoratorID'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'DataProcessing' );
							$dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID]['decorators'][$decorator['decoratorID']] = $decorator;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence decorator ' . $decorator['decoratorID'] .
							' exists.  Skipping for ' . $requestAttributeSetID, 'DataProcessing' );
						}
					}
				}

				$uniqueKey = ( (string) $dataProcessingStatement['id'] ) . ( (string) $request['id'] );

				$this->dataProcessingStatements[ $uniqueKey ] = $dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID];

				// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
				// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
				// and do more granulated inspection and cache clearing
				$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

				$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserNodes::' .
					$uniqueKey, $dataProcessingStatements[$dataProcessingStatementID]['requests'][$requestID], 'DataProcessing', 0, true );
			}
		}

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for DataProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the DataProcessing system
		// and do more granulated inspection and cache clearing
		$dataProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('DataProcessing');

		$dataProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParserNodes',
			$this->dataProcessingStatements, 'DataProcessing', 0, true );

		$dataProcessingCacheRegionHandler->storeObject( 
			eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayDPDefinitionParser::NodesCached', true, 'DataProcessing', 0, true );

		// Mark successful completion of this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayDPDefinitionParser: DataProcessing.xml successfully processed", 'DataProcessing' );
	}

	/**
	 * Empty init method invoked in constructor
	 *
	 * The eGlooDPDefinitionParser parent class requires us to implement this method in case we
	 * want to do something useful during construction of our singleton instance.  For now, do nothing.
	 */
	protected function init() {}

}
