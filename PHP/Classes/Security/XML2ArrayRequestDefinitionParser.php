<?php
/**
 * XML2ArrayRequestDefinitionParser Class File
 *
 * Contains the class definition for the xml request definition parser
 * 
 * Copyright 2010 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *	
 * @author George Cooper
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Security
 * @version 1.0
 */

/**
 * XML2ArrayRequestDefinitionParser
 * 
 * Validates requests against specification from requests definition file (Requests.xml)
 *
 * @package RequestProcessing
 * @subpackage Security
 */
final class XML2ArrayRequestDefinitionParser extends eGlooRequestDefinitionParser {

	/**
	 * Static Data Members
	 */

	// Singleton data member to enforce the singleton pattern for eGlooRequestDefinitionParser subclasses
	protected static $singleton;

	// Variables representing the reserved keywords for default RequestClass and RequestID wildcard lookups
	protected static $_requestClassWildcard = 'egDefault';
	protected static $_requestIDWildcard = 'egDefault';

	/**
	 * Method to load request nodes and request attribute sets from Requests.xml definitions file
	 * 
	 * Request node definitions and request attribute set definitions are defined in a Requests.xml
	 * file for every eGloo application.  This method is invoked in order to parse that definition file
	 * and structure information about valid requests, request attribute sets, their arguments, decorators,
	 * dependencies and associated RequestProcessors into a fast and cacheable hash map of node ID to
	 * node definition.
	 *
	 * This method will first parse request attribute sets, which are inheritable definitions for arguments,
	 * decorators and dependencies that a request definition can include rather than explicitly repeating
	 * commonly defined definition sets for multiple request nodes.  After the request attribute sets have
	 * been processed and stored, request definitions themselves are processed.  Once request node definitions
	 * are processed, their listed included request attribute sets are merged in to a flat format as a single
	 * definition for that request node.  Request attribute sets are a priority based logical include
	 * (with RequestAttributeSet includes specifying priority), but the end result is effectively a lexical
	 * XML node include with duplicate nodes ignored.
	 *
	 * Once all parsing has been completed, the generated request definition nodes are cached through the
	 * RequestProcessing cache region handler so that subsequent requests to the eGloo runtime can avoid
	 * parsing the Requests.xml file and building the definition structure.  The form of this caching is
	 * as follows:
	 *
	 * (1)	One entry that represents the entire processed request definition hash, including the "merged"
	 *		request attribute set components
	 * (2)	One entry that represents the parsed and processed request attribute set definitions.  This
	 *		data structure is not currently referenced again in the runtime, but is left available for
	 *		inspection.
	 * (3)	An entry per RequestClass/RequestID pair, using the concatenated pair name as the hash key. This
	 *		form of the structure exists so that a memcache lookup for the request node definition returns only
	 *		information critical to the specified request.
	 * (4)	A marker to indicate that nodes have been properly cached in the caching system.  The reason this
	 *		exists is so that if the lookup for a particular node combo doesn't exist, we can determine if
	 *		this occurred because the node does not exist or if nodes were never cached and need to be processed
	 *		with loadRequestNodes().  This is also used as a hint to branch on RequestClass/RequestID wildcards.
	 *		RequestClass/ID pairs are documented inline and are covered in supplementary examples.
	 *
	 * @throws ErrorException	if definition file cannot be read, has syntax errors, is missing
	 *							required values or provides invalid values
	 */
	protected function loadRequestNodes() {
		// Mark entrance into this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Entered loadRequestNodes()", 'Security' );

		// Grab the absolute file system path to the Requests.xml we're concerned with.  $this->webapp is set
		// during construction of this XML2ArrayRequestDefinitionParser singleton.  See eGlooReqeuestDefinitionParser
		// for details.
		$requests_xml_path = eGlooConfiguration::getApplicationsPath() . '/' . $this->webapp . "/XML/Requests.xml";

		// Mark that we are now attempting to load the specified Requests.xml
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Loading " . $requests_xml_path, 'Security' );

		// Attempt to load the specified Requests.xml file
		$requestXMLObject = simplexml_load_file( $requests_xml_path );

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for RequestProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the RequestProcessing system
		// and do more granulated inspection and cache clearing
		$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

		// If reading the Requests.xml file failed, log the error
		// TODO determine if we should throw an exception here...
		if ( !$requestXMLObject ) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'XML2ArrayRequestDefinitionParser: simplexml_load_file( "' . $requests_xml_path . '" ): ' . libxml_get_errors() );
		}

		$requestClasses = array();

		foreach( $requestXMLObject->xpath( '/tns:Requests/RequestAttributeSet' ) as $attributeSet ) {
			$attributeSetID = isset($attributeSet['id']) ? (string) $attributeSet['id'] : NULL;

			if ( !$attributeSetID || trim($attributeSetID) === '' ) {
				throw new ErrorException("No ID specified in request attribute set.	 Please review your Requests.xml");
			}

			$requestAttributeSets[$attributeSetID] = array('attributeSet' => $attributeSetID, 'attributes' => array());

			// Arguments
			$requestAttributeSets[$attributeSetID]['attributes']['boolArguments'] = array();

			foreach( $attributeSet->xpath( 'child::BoolArgument' ) as $boolArgument ) {
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

				$requestAttributeSets[$attributeSetID]['attributes']['boolArguments'][$newBoolArgument['id']] = $newBoolArgument;
			}

			$requestAttributeSets[$attributeSetID]['attributes']['selectArguments'] = array();

			foreach( $attributeSet->xpath( 'child::SelectArgument' ) as $selectArgument ) {
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

				$requestAttributeSets[$attributeSetID]['attributes']['selectArguments'][$newSelectArgument['id']] = $newSelectArgument;
			}

			$requestAttributeSets[$attributeSetID]['attributes']['variableArguments'] = array();

			foreach( $attributeSet->xpath( 'child::VariableArgument' ) as $variableArgument ) {
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

				$requestAttributeSets[$attributeSetID]['attributes']['variableArguments'][$newVariableArgument['id']] = $newVariableArgument;
			}

			$requestAttributeSets[$attributeSetID]['attributes']['complexArguments'] = array();

			foreach( $attributeSet->xpath( 'child::ComplexArgument' ) as $complexArgument ) {
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

				$requestAttributeSets[$attributeSetID]['attributes']['complexArguments'][$newComplexArgument['id']] = $newComplexArgument;
			}

			$requestAttributeSets[$attributeSetID]['attributes']['depends'] = array();

			foreach( $attributeSet->xpath( 'child::Depend' ) as $depend ) {
				$newDepend = array();

				$newDepend['id'] = (string) $depend['id'];
				$newDepend['type'] = (string) $depend['type'];
				$newDepend['children'] = array();

				foreach( $depend->xpath( 'child::Child' ) as $dependChild ) {
					$dependChildID = (string) $dependChild['id'];
					$dependChildType = strtolower( (string) $dependChild['type'] );

					$newDepend['children'][] = array('id' => $dependChildID, 'type' => $dependChildType);
				}

				$requestAttributeSets[$attributeSetID]['attributes']['depends'][$newDepend['id']] = $newDepend;
			}

			// Decorators
			$requestAttributeSets[$attributeSetID]['attributes']['decorators'] = array();

			foreach( $attributeSet->xpath( 'child::Decorator' ) as $decorator ) {
				$newDecorator = array();

				$newDecorator['decoratorID'] = (string) $decorator['decoratorID'];
				$newDecorator['order'] = (string) $decorator['order'];

				$requestAttributeSets[$attributeSetID]['attributes']['decorators'][$newDecorator['decoratorID']] = $newDecorator;
			}

			$uniqueKey = ((string) $attributeSet['id']);
			$this->attributeSets[ $uniqueKey ] = $requestAttributeSets[$attributeSetID];

			// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for RequestProcessing
			// we can also write some information to the caching system to better keep track of what is cached for the RequestProcessing system
			// and do more granulated inspection and cache clearing
			$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

			$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserAttributeNodes::' .
				$uniqueKey, $requestAttributeSets[$attributeSetID], 'RequestValidation', 0, true );
		}

		// We're done processing our request attribute sets, so let's store the structured array in cache for faster lookup
		// For cache properties, the ttl is forever (0) and we can keep the cache piping hot by storing a local copy (true)
		$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserAttributeSets',
			$this->attributeSets, 'RequestValidation', 0, true );

		// Iterate over the RequestClass nodes so that we can parse each request definition
		foreach( $requestXMLObject->xpath( '/tns:Requests/RequestClass' ) as $requestClass ) {
			// Grab the ID for this particular RequestClass
			$requestClassID = isset($requestClass['id']) ? (string) $requestClass['id'] : NULL;

			// If no ID is set for this RequestClass, this is not a valid Requests.xml and we should get out of here
			if ( !$requestClassID || trim($requestClassID) === '' ) {
				throw new ErrorException("No ID specified in request class.	 Please review your Requests.xml");
			}

			// Assign an array to hold this RequestClass node definition.  Associative key is the RequestClass ID
			$requestClasses[$requestClassID] = array('requestClass' => $requestClassID, 'requests' => array());

			// Iterate over the Request nodes for this RequestClass so that we can parse each request definition
			foreach( $requestClass->xpath( 'child::Request' ) as $request ) {
				// Grab the ID for this particular Request
				$requestID = isset($request['id']) ? (string) $request['id'] : NULL;

				// Grab the name of the RequestProcessor specified to handle this particular Request
				// Example:
				// $requestProcessor = new $processorID();
				// $requestProcessor->processRequest();
				$processorID = isset($request['processorID']) ? (string) $request['processorID'] : NULL;

				// Grab the name of the RequestProcessor specified to handle errors for this particular Request
				// Example:
				// $requestProcessor = new $processorID();
				// $requestProcessor->processErrorRequest();
				$errorProcessorID = isset($request['errorProcessorID']) ? (string) $request['errorProcessorID'] : NULL;

				// If no ID is set for this Request, this is not a valid Requests.xml and we should get out of here
				if ( !$requestID || trim($requestID) === '' ) {
					throw new ErrorException("No request ID specified in request class: '" . $requestClassID .
						"'.	 Please review your Requests.xml");
				}

				// If no RequestProcessor is specified, this is not a valid Requests.xml and we should get out of here
				if ( !$processorID || trim($processorID) === '' ) {
					throw new ErrorException("No processor ID specified in request ID: '" . $requestID .
					"'.	 Please review your Requests.xml");
				}

				// Request Properties
				$requestClasses[$requestClassID]['requests'][$requestID] =
					array('requestClass' => $requestClassID, 'requestID' => $requestID, 'processorID' => $processorID, 'errorProcessorID' => $errorProcessorID );

				// Arguments
				$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'] = array();

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

					$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][$newBoolArgument['id']] = $newBoolArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'] = array();

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

					$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][$newSelectArgument['id']] = $newSelectArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'] = array();

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

					$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][$newVariableArgument['id']] = $newVariableArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'] = array();

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

					$requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'][$newComplexArgument['id']] = $newComplexArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['depends'] = array();

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

					$requestClasses[$requestClassID]['requests'][$requestID]['depends'][$newDepend['id']] = $newDepend;
				}

				// Decorators
				$requestClasses[$requestClassID]['requests'][$requestID]['decorators'] = array();

				foreach( $request->xpath( 'child::Decorator' ) as $decorator ) {
					$newDecorator = array();

					$newDecorator['decoratorID'] = (string) $decorator['decoratorID'];
					$newDecorator['order'] = (string) $decorator['order'];

					$requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$newDecorator['decoratorID']] = $newDecorator;
				}

				// Request Attribute Set Includes
				$requestClasses[$requestClassID]['requests'][$requestID]['requestAttributeSetIncludes'] = array();

				foreach( $request->xpath( 'child::RequestAttributeSetInclude' ) as $requestAttributeSetInclude ) {
					$newRequestAttributeSetInclude = array();

					$newRequestAttributeSetInclude['requestAttributeSetID'] = (string) $requestAttributeSetInclude['requestAttributeSetID'];
					$newRequestAttributeSetInclude['priority'] = (string) $requestAttributeSetInclude['priority'];

					$newInsertArrayID = $newRequestAttributeSetInclude['requestAttributeSetID'];

					$requestClasses[$requestClassID]['requests'][$requestID]['requestAttributeSetIncludes'][$newInsertArrayID] = $newRequestAttributeSetInclude;
				}

				foreach( $requestClasses[$requestClassID]['requests'][$requestID]['requestAttributeSetIncludes'] as $requestAttributeSetInclude ) {
					$requestAttributeSetID = $requestAttributeSetInclude['requestAttributeSetID'];
					$priority = $requestAttributeSetInclude['priority'];

					$requestAttributeSet = $this->attributeSets[$requestAttributeSetID];

					$boolArguments = $requestAttributeSet['attributes']['boolArguments'];
					$selectArguments = $requestAttributeSet['attributes']['selectArguments'];
					$variableArguments = $requestAttributeSet['attributes']['variableArguments'];
					$complexArguments = $requestAttributeSet['attributes']['complexArguments'];
					$depends = $requestAttributeSet['attributes']['depends'];
					$decorators = $requestAttributeSet['attributes']['decorators'];

					foreach( $boolArguments as $boolArgument ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][$boolArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting bool argument ' . $boolArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][$boolArgument['id']] = $boolArgument;
						} else if ( isset($requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][$boolArgument['id']]['priority']) &&
								$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][$boolArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence bool argument ' . $boolArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][$boolArgument['id']] = $boolArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence bool argument ' . $boolArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'Security' );
						}
					}

					foreach( $selectArguments as $selectArgument ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][$selectArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting select argument ' . $selectArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][$selectArgument['id']] = $selectArgument;
						} else if ( isset($requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][$selectArgument['id']]['priority']) &&
								$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][$selectArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence select argument ' . $selectArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][$selectArgument['id']] = $selectArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence select argument ' . $selectArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'Security' );
						}
					}

					foreach( $variableArguments as $variableArgument ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][$variableArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting variable argument ' . $variableArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][$variableArgument['id']] = $variableArgument;
						} else if ( isset($requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][$variableArgument['id']]['priority']) &&
								$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][$variableArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence variable argument ' . $variableArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][$variableArgument['id']] = $variableArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence variable argument ' . $variableArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'Security' );
						}
					}

					foreach( $complexArguments as $complexArgument ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'][$complexArgument['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting complex argument ' . $complexArgument['id'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'][$complexArgument['id']] = $complexArgument;
						} else if ( isset($requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'][$complexArgument['id']]['priority']) &&
								$requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'][$complexArgument['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence complex argument ' . $complexArgument['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['complexArguments'][$complexArgument['id']] = $complexArgument;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence complex argument ' . $complexArgument['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'Security' );
						}
					}

					foreach( $depends as $depend ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['depends'][$depend['id']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting depend ' . $depend['id'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['depends'][$depend['id']] = $depend;
						} else if ( isset($requestClasses[$requestClassID]['requests'][$requestID]['depends'][$depend['id']]['priority']) &&
								$requestClasses[$requestClassID]['requests'][$requestID]['depends'][$depend['id']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence depend ' . $depend['id'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['depends'][$depend['id']] = $depend;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence depend ' . $depend['id'] .
								' exists.  Skipping for ' . $requestAttributeSetID, 'Security' );
						}
					}

					$existingDecoratorCount = count($requestClasses[$requestClassID]['requests'][$requestID]['decorators']);

					foreach( $decorators as $decorator ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting decorator ' . $decorator['decoratorID'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );

							$decorator['order'] += $existingDecoratorCount;
							$requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$decorator['decoratorID']] = $decorator;
						} else if ( isset($requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]['priority']) &&
								$requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]['priority'] < $priority ) {

							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Replacing lower precedence decorator ' . $decorator['decoratorID'] .
								' with higher precedence from attribute set ' . $requestAttributeSetID , 'Security' );
							$requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$decorator['decoratorID']] = $decorator;
						} else {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Higher precedence decorator ' . $decorator['decoratorID'] .
							' exists.  Skipping for ' . $requestAttributeSetID, 'Security' );
						}
					}
				}

				$uniqueKey = ( (string) $requestClass['id'] ) . ( (string) $request['id'] );

				$this->requestNodes[ $uniqueKey ] = $requestClasses[$requestClassID]['requests'][$requestID];

				// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for RequestProcessing
				// we can also write some information to the caching system to better keep track of what is cached for the RequestProcessing system
				// and do more granulated inspection and cache clearing
				$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

				$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes::' .
					$uniqueKey, $requestClasses[$requestClassID]['requests'][$requestID], 'RequestValidation', 0, true );
			}
		}

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for RequestProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the RequestProcessing system
		// and do more granulated inspection and cache clearing
		$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

		$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes',
			$this->requestNodes, 'RequestValidation', 0, true );

		$requestProcessingCacheRegionHandler->storeObject( 
			eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParser::NodesCached', true, 'RequestValidation', 0, true );

		// Mark successful completion of this method so that when debugging we can more accurately trace control flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Requests.xml successfully processed", 'Security' );
	}

	/**
	 * Empty init method invoked in constructor
	 *
	 * The eGlooRequestDefinitionParser parent class requires us to implement this method in case we
	 * want to do something useful during construction of our singleton instance.  For now, do nothing.
	 */
	protected function init() {}

	/**
	 * Method to validate and process a request, and populate the RequestInfoBean for the runtime.
	 *
	 * This method ensures that this is valid request, by checking arguments against the expectant
	 * values in the loaded request and request attribute set definition nodes.  If it is a valid 
	 * request, the RequestProcessor ID needed to process this request is populated in the RequestInfoBean.
	 *
	 * All valid and invalid request parameters are marked in the RequestInfoBean for later inspection.
	 * For instance, SelectArguments are compared against the list of allowed values, VariableArguments
	 * are compared against the defined regular expression, BoolArguments are checked for a value of true
	 * or false, argument dependencies are checked and enforced.  If any arguments fail validation, the
	 * request is marked as being in error and the result of the validateAndProcess method is to return
	 * false after populating the RequestInfoBean accordingly.
	 *
	 * If ComplexArguments are supplied, the validators specified in the request definition are invoked
	 * to test the provided values and perform any additional processing required. ComplexArgumentValidators
	 * can be provided with a ComplexArgument class type as a hint from the Requests.xml for what sort of
	 * data is being provided and what should be returned.  After the ComplexArgumentValidator has completed
	 * validation, it returns the validated input in whatever format (array, object, string, etc) its
	 * validation method specifies.  Failure of ComplexArgument validation, like any other argument type,
	 * results in the request argument being set as invalid in the RequestInfoBean and the result of the
	 * validateAndProcess method returning as false.
	 *
	 * All decorators for the supplied request are built into an array and set in the RequestInfoBean
	 * to later by constructed by the RequestProcessorFactory and wrapped as an onion around the
	 * RequestProcess defined to handle the given request.
	 *
	 * Finally, if wildcard support is turned on and the supplied RequestClass/RequestID pair is not found
	 * in our request definitions, we check to see if a wildcard handler is available, first for the RequestID
	 * within the RequestClass, then for the RequestClass, then for egDefault/egDefault (default wildcard pair).
	 *
	 * @param $requestInfoBean	the RequestInfoBean singleton to be populated through the validation process
	 *
	 * @throws ErrorException	if the provided RequestClass or RequestID are not found in the $_GET array or
	 *							if the RequestClass/RequestID pair is not found in the loaded request nodes
	 *							AND the current deployment mode is DEVELOPMENT.  Otherwise, the error is logged
	 *							and the method returns false
	 *
	 * @return					true if this is a valid request, or false if it is not
	 */
	public function validateAndProcess( $requestInfoBean ) {
		// Check if there is a RequestClass.  If there isn't, return not setting any RequestProcessor.
		 if( !isset( $_GET[ self::REQUEST_CLASS_KEY ] )){
			$errorMessage = 'Request class not set in request.	' . "\n" . 'Verify that mod_rewrite is active and its rules are correct in your .htaccess';
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, $errorMessage, 'Security' );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($errorMessage);
			}

			return false;
		 }

		// Check if there is a RequestID.  If there isn't, return not setting any RequestProcessor.
		if( !isset( $_GET[ self::REQUEST_ID_KEY ] ) ){
			$errorMessage = 'Request ID not set in request.	 ' . "\n\t" . 'Verify that mod_rewrite is active and its rules are correct in your .htaccess';
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, $errorMessage, 'Security' );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($errorMessage);
			}

			return false;
		}

		// We found a specified RequestClass and RequestID, so let's grab them for use in processing and validating this request
		$requestClass = $_GET[ self::REQUEST_CLASS_KEY ];
		$requestID = $_GET[ self::REQUEST_ID_KEY ];

		// Set the request lookup ID so that we can quickly grab the request node from the request nodes array ($this->requestNodes)
		$requestLookup = $requestClass . $requestID;

		// If we're in DEVELOPMENT mode, log what request we're getting so we can trace runtime flow
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Incoming Request Class and Request ID lookup is: "' . $requestLookup . '"', 'Security' );

		// Grab the cache handler specifically for this cache region.  We do this so that when we write to the cache for RequestProcessing
		// we can also write some information to the caching system to better keep track of what is cached for the RequestProcessing system
		// and do more granulated inspection and cache clearing
		$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');


		///////////////// TODO update comments below here


		$allNodesCached = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
			'XML2ArrayRequestDefinitionParser::NodesCached', 'RequestValidation', true );
		$requestNode = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes::' .
			$requestLookup, 'RequestValidation', true );

		if ( $allNodesCached && !$requestNode ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in cache, checking wildcards: ' . $requestLookup, 'Security' );
			$useRequestIDDefaultHandler = eGlooConfiguration::getUseDefaultRequestIDHandler();
			$useRequestClassDefaultHandler = eGlooConfiguration::getUseDefaultRequestClassHandler();

			// We have already parsed the XML once, so let's check down our wildcard options.  I want to refactor this later.  Maybe.
			if ( $allNodesCached && ($useRequestIDDefaultHandler || $useRequestClassDefaultHandler) ) {
				// We didn't find the request node and we are cached, so let's see if this request class has a request ID default cached
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in cache, but cache was populated: ' . $requestLookup, 'Security' );

				if ( $useRequestIDDefaultHandler) {
					eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Checking for requestID wildcard in cache: ' . $requestClass . self::$_requestIDWildcard, 'Security' );
					$requestNode = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes::' .
						$requestClass . self::$_requestIDWildcard, 'RequestValidation', true );

					if ( $requestNode != null && is_array($requestNode) ) {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RequestID wildcard found in cache: ' . $requestClass . self::$_requestIDWildcard, 'Security' );
						$requestInfoBean->setWildCardRequest( true );
						$requestInfoBean->setWildCardRequestID( $requestID );
						$requestInfoBean->setRequestID( self::$_requestIDWildcard );
					} else {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RequestID wildcard not found in cache: ' . $requestClass . self::$_requestIDWildcard, 'Security' );
					}
				}

				if ( $requestNode == null && $useRequestClassDefaultHandler ) {
					// Still no request node, let's see if there's a generic set in cache
					eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Checking for default request wildcard in cache: ' . self::$_requestClassWildcard . self::$_requestIDWildcard, 'Security' );
					$requestNode = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes::' .
						self::$_requestClassWildcard . self::$_requestIDWildcard, 'RequestValidation', true );

					if ( $requestNode != null && is_array($requestNode) ) {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Default request wildcard found in cache: ' . self::$_requestClassWildcard . self::$_requestIDWildcard, 'Security' );
						$requestInfoBean->setWildCardRequest( true );
						$requestInfoBean->setWildCardRequestClass( $requestClass );
						$requestInfoBean->setWildCardRequestID( $requestID );
						$requestInfoBean->setRequestClass( self::$_requestClassWildcard );
						$requestInfoBean->setRequestID( self::$_requestIDWildcard );
					}

				}
			}
		} else if ($allNodesCached == null) {
			// We haven't found anything in cache, so let's read in the XML and recheck for the request class/ID pair
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request nodes not cached, loading: ' . $requestLookup, 'Security' );
			$this->loadRequestNodes();

			// Same logic as above, except we're checking what we loaded from XML
			if ( isset($this->requestNodes[ $requestLookup ]) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node found in XML: ' . $requestLookup, 'Security' );
				$requestNode = $this->requestNodes[ $requestLookup ];
			} else if ( $useRequestIDDefaultHandler && isset($this->requestNodes[ $requestClass . self::$_requestIDWildcard ]) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in XML, using requestID wildcard: ' . $requestClass . self::$_requestIDWildcard, 'Security' );
				$requestNode = $this->requestNodes[ $requestClass . self::$_requestIDWildcard ];
				$requestInfoBean->setWildCardRequest( true );
				$requestInfoBean->setWildCardRequestID( $requestID );
				$requestInfoBean->setRequestID( self::$_requestIDWildcard );					
			} else if ( $useRequestClassDefaultHandler && isset($this->requestNodes[ self::$_requestClassWildcard . self::$_requestIDWildcard ]) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in XML, using wildcard default: ' . self::$_requestClassWildcard . self::$_requestIDWildcard, 'Security' );
				$requestNode = $this->requestNodes[ self::$_requestClassWildcard . self::$_requestIDWildcard ];
				$requestInfoBean->setWildCardRequest( true );
				$requestInfoBean->setWildCardRequestClass( $requestClass );
				$requestInfoBean->setWildCardRequestID( $requestID );
				$requestInfoBean->setRequestClass( self::$_requestClassWildcard );
				$requestInfoBean->setRequestID( self::$_requestIDWildcard );
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in XML, wildcards disabled: ' . $requestLookup, 'Security' );
				$requestNode = null;
			}

		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Invalid state: '" . $requestClass . "' and request ID '" . $requestID . "'", 'Security' );
		}

		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if( !isset( $requestNode ) || !is_array( $requestNode ) ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 
				"Request pairing not found for request class: '" . $requestClass . "' and request ID '" . $requestID . "'", 'Security' );
			
			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException("Request pairing not found for request class: '" . $requestClass . "' and request ID '" . $requestID . "'");
			}

			return false;
		}

		/**
		 * If this is a valid request class/id, get the request denoted 
		 * by this request class and id.
		 */
		$processorID = $requestNode[ self::PROCESSOR_ID_KEY ];
		$requestInfoBean->setRequestProcessorID( $processorID );

		$errorProcessorID = $requestNode[ self::ERROR_PROCESSOR_ID_KEY ];
		$requestInfoBean->setErrorRequestProcessorID( $errorProcessorID );

		/**
		 * Now verify the contents of the request before we hand this off 
		 * for further processing
		 */

		$retVal = true;

		//check boolean arguments
		if( !$this->validateBooleanArguments( $requestNode, $requestInfoBean ) ) {
			if ($errorProcessorID) {
				$retVal = false;
			} else {
				return false;
			}
		}

		//check variable arguments
		if( !$this->validateVariableArguments( $requestNode, $requestInfoBean ) ) {
			if ($errorProcessorID) {
				$retVal = false;
			} else {
				return false;
			}
		}

		//check complex arguments
		if( !$this->validateComplexArguments( $requestNode, $requestInfoBean ) ) {
			if ($errorProcessorID) {
				$retVal = false;
			} else {
				return false;
			}
		}

		//check select arguments
		if( !$this->validateSelectArguments( $requestNode, $requestInfoBean ) ) {
			if ($errorProcessorID) {
				$retVal = false;
			} else {
				return false;
			}
		}

		//check depend arguments
		if( !$this->validateDependArguments( $requestNode, $requestInfoBean ) ) {
			if ($errorProcessorID) {
				$retVal = false;
			} else {
				return false;
			}
		}

		//build decorator array and set it in the requestInfoBean
		$this->buildDecoratorArray( $requestNode, $requestInfoBean);

		/**
		 * If have gotten here with out returning... we're golden.
		 * unset post and get and return
		 */
		 $_POST = null;
		 $_GET = null;

		return $retVal;
	}

// TODO Change array access to references	
	/**
	 * validate boolean arguments
	 * 
	 * @return true if all boolean arguments pass the test, false otherwise
	 */
	private function validateBooleanArguments( &$requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$retVal = true;

		 foreach( $requestNode['boolArguments'] as $boolArg ) {
			
			if( $boolArg['type'] === "get" ){


				if( !isset( $_GET[ (string) $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] === "true") {
						$errorMessage = "Required boolean parameter: " . $boolArg['id'] .
							" is not set in GET request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredGET( $boolArg['id'] );
							$retVal = false;
						}
					} else if (isset($boolArg['default'])) {
						$requestInfoBean->setGET( $boolArg['id'],  $boolArg['default'] );
					}
				} else {
					//check if correctly formatted (true vs false)
					$boolVal = $_GET[ $boolArg['id'] ];
					if( $boolVal !== "false" && $boolVal !== "true" ){
						$errorMessage = "boolean parameter: " . $boolArg['id'] .
							" is not in correct 'true' or 'false' format in GET request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidGET( $boolArg['id'],  $boolVal );
							$retVal = false;
						}
					} else {
						//set argument in the request info bean
						$requestInfoBean->setGET( $boolArg['id'],  $boolVal );
					}
				}
			} else if ( $boolArg['type'] === "post" ) {

				if( !isset( $_POST[ $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] === "true") {
						$errorMessage = "Required boolean parameter: " . $boolArg['id'] . 
							" is not set in post request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredPOST( $boolArg['id'] );
							$retVal = false;
						}
					} else if (isset($boolArg['default'])) {
						$requestInfoBean->setPOST( $boolArg['id'],	$boolArg['default'] );
					}
				} else {
					
					//check if correctly formatted (true vs false)
					$boolVal = $_POST[ $boolArg['id'] ];
					if( $boolVal !== "false" && $boolVal !== "true" ){
						$errorMessage = "Boolean parameter: " . $boolArg['id'] . 
							" is not in correct 'true' or 'false' format in post request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidPOST( $boolArg['id'],  $boolVal );
							$retVal = false;
						}
					} else {
						//set argument in the request info bean
						$requestInfoBean->setPOST( $boolArg['id'],	$boolVal );
					}
				}
			}
		} 

		return $retVal;
	}

	/**
	 * validate variable parameters
	 * 
	 * @return true if all variable arguments pass the test, false otherwise
	 */
	private function validateVariableArguments( $requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$retVal = true;

		 foreach( $requestNode['variableArguments'] as $variableArg ) {
			if( $variableArg['type'] === 'get' ){

				if( !isset( $_GET[ $variableArg['id'] ] ) ){
					
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
							" is not set in GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredGET( $variableArg['id'] );
							$retVal = false;
						}
					} else if (isset($variableArg['default'])) {
						if ( isset($variableArg['scalarType']) ) {
							if ( $variableArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setGET( $variableArg['id'],  intval($variableArg['default']));
							} else if ( $variableArg['scalarType'] === 'float' ) {
								$requestInfoBean->setGET( $variableArg['id'],  floatval($variableArg['default']));
							}
						} else {
							$requestInfoBean->setGET( $variableArg['id'],  $variableArg['default'] );
						}

						// $requestInfoBean->setGET( $variableArg['id'],  $variableArg['default'] );
					}

				} else {
					//check if correctly formatted
					$variableValue = $_GET[ $variableArg['id'] ];
					$regexFormat = $variableArg['regex'];
					$match = preg_match ( $regexFormat, $variableValue );
					
					if( ! $match ){
						$errorMessage = "Variable parameter: " . $variableArg['id'] . 
							" with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
							" in GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidGET( $variableArg['id'], $variableValue );
							$retVal = false;
						}
					} else {
						//set argument in the request info bean
						if ( isset($variableArg['scalarType']) ) {
							if ( $variableArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setGET( $variableArg['id'],  intval($variableValue));
							} else if ( $variableArg['scalarType'] === 'float' ) {
								$requestInfoBean->setGET( $variableArg['id'],  floatval($variableValue));
							}
						} else {
							$requestInfoBean->setGET( $variableArg['id'],  $variableValue);
						}
					}
				}
			} else if ( $variableArg['type'] === 'getarray' ) {
				if( !isset( $_GET[ $variableArg['id'] ] ) ){
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredGET( $variableArg['id'] );
							$retVal = false;
						}
					}
				} else {
					//check if correctly formatted
					$variableValues = $_GET[ $variableArg['id'] ];

					// Throw an exception if we attempt to access a non-GET array variable as an array
					if (!is_array($variableValues)) {
						throw new eGlooRequestDefinitionParserException('GET Array Access Error: GET ID \'' . $variableArg['id'] . '\' is type \'' .
							gettype($variableValues) . '\', not type \'' . gettype(array()) . '\'');
					}

					$regexFormat = $variableArg['regex'];
					
					$sanitizedValues = array();

					foreach($variableValues as $key => $variableValue) {
						$match = preg_match ( $regexFormat, $variableValue );

						if( !$match ){
							eGlooLogger::writeLog( eGlooLogger::DEBUG, "variable parameter: " . $variableArg['id'] . 
								" with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
								" in post request with request ID: " . $requestID, 'Security' );
						} else {
							if ( isset($variableArg['scalarType']) ) {
								if ( $variableArg['scalarType'] === 'integer' ) {
									$sanitizedValues[$key] = intval($variableValue);
								} else if ( $variableArg['scalarType'] === 'float' ) {
									$sanitizedValues[$key] = floatval($variableValue);
								}
							} else {
								$sanitizedValues[$key] = $variableValue;
							}
						}

					}

					//set argument in the request info bean
					$requestInfoBean->setGET( $variableArg['id'],  $sanitizedValues );
				}

			} else if ( $variableArg['type'] === 'post' ) {
				if( !isset( $_POST[ $variableArg['id'] ] ) ){
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredPOST( $variableArg['id'] );
							$retVal = false;
						}
					} else if (isset($variableArg['default'])) {
						if ( isset($variableArg['scalarType']) ) {
							if ( $variableArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setPOST( $variableArg['id'],	intval($variableArg['default']));
							} else if ( $variableArg['scalarType'] === 'float' ) {
								$requestInfoBean->setPOST( $variableArg['id'],	floatval($variableArg['default']));
							}
						} else {
							$requestInfoBean->setPOST( $variableArg['id'],	$variableArg['default'] );
						}

						// $requestInfoBean->setPOST( $variableArg['id'],  $variableArg['default'] );
					}

				} else {
					//check if correctly formatted
					$variableValue = $_POST[ $variableArg['id'] ];
					$regexFormat = $variableArg['regex'];
					$match = preg_match ( $regexFormat, $variableValue );

					if( ! $match ){
						$errorMessage = "Variable parameter: " . $variableArg['id'] . 
							" with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
							" in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidPOST( $variableArg['id'], $variableValue );
							$retVal = false;
						}
					} else {
						//set argument in the request info bean
						if ( isset($variableArg['scalarType']) ) {
							if ( $variableArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setPOST( $variableArg['id'],	intval($variableValue));
							} else if ( $variableArg['scalarType'] === 'float' ) {
								$requestInfoBean->setPOST( $variableArg['id'],	floatval($variableValue));
							}
						} else {
							$requestInfoBean->setPOST( $variableArg['id'],	$variableValue);
						}
					}
				}
			} else if ( $variableArg['type'] === 'postarray') {
				if( !isset( $_POST[ $variableArg['id'] ] ) ){
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredPOST( $variableArg['id'] );
							$retVal = false;
						}
					}
				} else {
					//check if correctly formatted
					$variableValues = $_POST[ $variableArg['id'] ];

					// Throw an exception if we attempt to access a non-post array variable as an array
					if (!is_array($variableValues)) {
						throw new eGlooRequestDefinitionParserException('POST Array Access Error: POST ID \'' . $variableArg['id'] . '\' is type \'' .
							gettype($variableValues) . '\', not type \'' . gettype(array()) . '\'');
					}

					$regexFormat = $variableArg['regex'];
					$sanitizedValues = array();

					foreach($variableValues as $key => $variableValue) {
						$match = preg_match ( $regexFormat, $variableValue );

						if( !$match ){
							eGlooLogger::writeLog( eGlooLogger::DEBUG, "variable parameter: " . $variableArg['id'] . 
								" with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
								" in post request with request ID: " . $requestID, 'Security' );
						} else {
							if ( isset($variableArg['scalarType']) ) {
								if ( $variableArg['scalarType'] === 'integer' ) {
									$sanitizedValues[$key] = intval($variableValue);
								} else if ( $variableArg['scalarType'] === 'float' ) {
									$sanitizedValues[$key] = floatval($variableValue);
								}
							} else {
								$sanitizedValues[$key] = $variableValue;
							}
						}

					}

					//set argument in the request info bean
					$requestInfoBean->setPOST( $variableArg['id'],	$sanitizedValues );
				}
			}
		} 

		return $retVal;
	}

	/**
	 * validate complex parameters
	 * 
	 * @return true if all complex arguments pass the test, false otherwise
	 */
	private function validateComplexArguments( $requestNode, $requestInfoBean ) {
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$retVal = true;

		 foreach( $requestNode['complexArguments'] as $complexArg ) {
			// die_r($complexArg);
			if( $complexArg['type'] === 'get' ){

				if( !isset( $_GET[ $complexArg['id'] ] ) ){
					
					//check if required
					if( $complexArg['required'] === "true") {
						$errorMessage = "Required complex parameter: " . $complexArg['id'] . 
							" is not set in GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredGET( $complexArg['id'] );
							$retVal = false;
						}
					} else if (isset($complexArg['default'])) {
						if ( isset($complexArg['scalarType']) ) {
							if ( $complexArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setGET( $complexArg['id'],  intval($complexArg['default']));
							} else if ( $complexArg['scalarType'] === 'float' ) {
								$requestInfoBean->setGET( $complexArg['id'],  floatval($complexArg['default']));
							}
						} else {
							$requestInfoBean->setGET( $complexArg['id'],  $complexArg['default'] );
						}
					}
				} else {
					$complexValue = $_GET[ $complexArg['id'] ];

					$validator = $complexArg['validator'];
					$validatorObj = new $validator();

					if (isset($complexArg['complexType'])) {
						$complexType = $complexArg['complexType'];
					} else if ( isset($complexArg['scalarType']) ) {
						$scalarType = $complexArg['scalarType'];

						// TODO enforce scalar type

						// We don't use a complexType here
						$complexType = null;
					} else {
						$complexType = null;
					}

					$validatedInput = $validatorObj->validate( $complexValue, $complexType );

					if ( isset($validatedInput) && $validatedInput ) {
						$requestInfoBean->setGET( $complexArg['id'],  $validatedInput );
					} else {
						$errorMessage = "Complex parameter: " . $complexArg['id'] . 
							" with value '" . $complexValue . "' is not valid" .
							" in GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidGET( $complexArg['id'], $complexValue );
							$retVal = false;
						}
					}
				}
			} else if ( $complexArg['type'] === 'getarray' ) {
				if( !isset( $_GET[ $complexArg['id'] ] ) ){
					//check if required
					if( $complexArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $complexArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredGET( $complexArg['id'] );
							$retVal = false;
						}
					}
				} else {
					//check if correctly formatted
					$complexValues = $_GET[ $complexArg['id'] ];

					// Throw an exception if we attempt to access a non-GET array variable as an array
					if (!is_array($complexValues)) {
						throw new eGlooRequestDefinitionParserException('GET Array Access Error: GET ID \'' . $complexArg['id'] . '\' is type \'' .
							gettype($complexValues) . '\', not type \'' . gettype(array()) . '\'');
					}

					$validator = $complexArg['validator'];
					$validatorObj = new $validator();

					if (isset($complexArg['complexType'])) {
						$complexType = $complexArg['complexType'];
					} else if ( isset($complexArg['scalarType']) ) {
						$scalarType = $complexArg['scalarType'];

						// TODO enforce scalar type

						// We don't use a complexType here
						$complexType = null;
					} else {
						$complexType = null;
					}

					$validatedInput = $validatorObj->validate( $complexValues, $complexType );

					if ( isset($validatedInput) && $validatedInput ) {
						$requestInfoBean->setGET( $complexArg['id'],  $validatedInput );
					} else {
						$errorMessage = "Complex parameter: " . $complexArg['id'] . 
							" with value '" . $complexValues . "' is not valid" .
							" in GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidGET( $complexArg['id'], $complexValues );
							$retVal = false;
						}
					}
				}
			} else if ( $complexArg['type'] === 'post' ) {
				if( !isset( $_POST[ $complexArg['id'] ] ) ){
					//check if required
					if( $complexArg['required'] === "true") {
						$errorMessage = "Required complex parameter: " . $complexArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredPOST( $complexArg['id'] );
							$retVal = false;
						}
					} else if (isset($complexArg['default'])) {
						if ( isset($complexArg['scalarType']) ) {
							if ( $complexArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setPOST( $variableArg['id'],	intval($complexArg['default']));
							} else if ( $complexArg['scalarType'] === 'float' ) {
								$requestInfoBean->setPOST( $complexArg['id'],  floatval($complexArg['default']));
							}
						} else {
							$requestInfoBean->setPOST( $complexArg['id'],  $complexArg['default'] );
						}
					}
				} else {
					//check if correctly formatted
					$complexValue = $_POST[ $complexArg['id'] ];
					$validator = $complexArg['validator'];
					$validatorObj = new $validator();

					if (isset($complexArg['complexType'])) {
						$complexType = $complexArg['complexType'];
					} else if ( isset($complexArg['scalarType']) ) {
						$scalarType = $complexArg['scalarType'];

						// TODO enforce scalar type

						// We don't use a complexType here
						$complexType = null;
					} else {
						$complexType = null;
					}

					$validatedInput = $validatorObj->validate( $complexValue, $complexType );

					if ( isset($validatedInput) && $validatedInput ) {
						$requestInfoBean->setPOST( $complexArg['id'],  $validatedInput );
					} else {
						$errorMessage = "Complex parameter: " . $complexArg['id'] . 
							" with value '" . $complexValue . "' is not valid" .
							" in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidPOST( $complexArg['id'], $complexValue );
							$retVal = false;
						}
					}
				}
			} else if ( $complexArg['type'] === 'postarray') {
				if( !isset( $_POST[ $complexArg['id'] ] ) ){
					//check if required
					if( $complexArg['required'] === "true") {
						$errorMessage = "Required complex parameter: " . $complexArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredPOST( $complexArg['id'] );
							$retVal = false;
						}
					}
				} else {
					$complexValues = $_POST[ $complexArg['id'] ];

					// Throw an exception if we attempt to access a non-post array variable as an array
					if (!is_array($complexValues)) {
						throw new eGlooRequestDefinitionParserException('POST Array Access Error: POST ID \'' . $complexArg['id'] . '\' is type \'' .
							gettype($complexValues) . '\', not type \'' . gettype(array()) . '\'');
					}

					$validator = $complexArg['validator'];
					$validatorObj = new $validator();

					if (isset($complexArg['complexType'])) {
						$complexType = $complexArg['complexType'];
					} else if ( isset($complexArg['scalarType']) ) {
						$scalarType = $complexArg['scalarType'];

						// TODO enforce scalar type

						// We don't use a complexType here
						$complexType = null;
					} else {
						$complexType = null;
					}

					$validatedInput = $validatorObj->validate( $complexValues, $complexType );

					if ( isset($validatedInput) && $validatedInput ) {
						$requestInfoBean->setPOST( $complexArg['id'],  $validatedInput );
					} else {
						$errorMessage = "Complex parameter: " . $complexArg['id'] . 
							" with value '" . $complexValues . "' is not valid" .
							" in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidPOST( $complexArg['id'], $complexValues );
							$retVal = false;
						}
					}

				}
			}
		} 
		
		return $retVal;
	}

	/**
	 * validate select arguments
	 * 
	 * @return true if all select arguments pass the test, false otherwise
	 */
	private function validateSelectArguments( $requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$retVal = true;

		 foreach( $requestNode['selectArguments'] as $selectArg ) {
			if( $selectArg['type'] === "get" ){
				if( !isset( $_GET[ $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] === "true") {
						$errorMessage = "Required select argument: " . $selectArg['id'] . 
							" is not set in GET request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredGET( $selectArg['id'] );
							$retVal = false;
						}
					} else if (isset($selectArg['default'])) {
						if ( isset($selectArg['scalarType']) ) {
							if ( $selectArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setGET( $selectArg['id'],	 intval($selectArg['default']));
							} else if ( $selectArg['scalarType'] === 'float' ) {
								$requestInfoBean->setGET( $selectArg['id'],	 floatval($selectArg['default']));
							}
						} else {
							$requestInfoBean->setGET( $selectArg['id'],	 $selectArg['default'] );
						}

						// $requestInfoBean->setGET( $selectArg['id'],	$selectArg['default'] );
					}
				} else {
					//check if value is one of the allowable values
					$selectVal = $_GET[ $selectArg['id'] ];
					$match = false;

					foreach( $selectArg['values'] as $validValue ){
						if( $validValue === $selectVal ){
							$match = true;
						}
					}

					if( !$match ){
						$errorMessage = "Select argument: " . $selectArg['id'] . 
							" with specified value '" . $selectVal . "' does not match required set of variables in " . 
							"GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidGET( $selectArg['id'], $selectVal );
							$retVal = false;
						}
					} else if ( isset($selectArg['scalarType']) ) {
						if ( $selectArg['scalarType'] === 'integer' ) {
							$requestInfoBean->setGET( $selectArg['id'],	 intval($selectVal));
						} else if ( $selectArg['scalarType'] === 'float' ) {
							$requestInfoBean->setGET( $selectArg['id'],	 floatval($selectVal));
						}
					} else {
						$requestInfoBean->setGET( $selectArg['id'],	 $selectVal);
					}
				}

			} else if( $selectArg['type'] === "post" ) {

				if( !isset( $_POST[ $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] === "true") {
						$errorMessage = "Required select argument: " . $selectArg['id'] . 
							" is not set in POST request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setUnsetRequiredPOST( $selectArg['id'] );
							$retVal = false;
						}
					} else if (isset($selectArg['default'])) {
						if ( isset($selectArg['scalarType']) ) {
							if ( $selectArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setPOST( $selectArg['id'],  intval($selectArg['default']));
							} else if ( $selectArg['scalarType'] === 'float' ) {
								$requestInfoBean->setPOST( $selectArg['id'],  floatval($selectArg['default']));
							}
						} else {
							$requestInfoBean->setPOST( $selectArg['id'],  $selectArg['default'] );
						}

						// $requestInfoBean->setPOST( $selectArg['id'],	 $selectArg['default'] );
					}

				} else {
					
					//check if value is one of the allowable values
					$selectVal = $_POST[ $selectArg['id'] ];
					$match = false;

					foreach( $selectArg['values'] as $validValue ) {
						if( $validValue === $selectVal ) {
							$match = true;
						}
					}
					
					if( ! $match ){
						$errorMessage = "Select argument: " . $selectArg['id'] . 
							" with specified value '" . $selectVal . "' does not match required set of variables in " . 
							"POST request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if ( !isset($requestNode['errorProcessorID']) ) {
							if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
								throw new ErrorException($errorMessage);
							}

							return false;
						} else {
							//set invalid argument in the request info bean
							$requestInfoBean->setInvalidPOST( $selectArg['id'], $selectVal );
							$retVal = false;
						}
					} else if ( isset($selectArg['scalarType']) ) {
						if ( $selectArg['scalarType'] === 'integer' ) {
							$requestInfoBean->setPOST( $selectArg['id'],  intval($selectVal));
						} else if ( $selectArg['scalarType'] === 'float' ) {
							$requestInfoBean->setPOST( $selectArg['id'],  floatval($selectVal));
						}
					} else {
						$requestInfoBean->setPOST( $selectArg['id'],  $selectVal);
					}
				}
			}
		} 
		
		return $retVal;
	}

	/**
	 * validate depend arguments
	 * 
	 * @return true if all depend arguments pass the test, false otherwise
	 */
	private function validateDependArguments( $requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$retVal = true;

		foreach( $requestNode['depends'] as $dependArg ) {
			
			if( $dependArg['type'] === "get" ){
				
				//only continue if the depend id is actually part of this get request
				if( isset( $_GET[ $dependArg['id'] ] ) ){
					
					//get the children values of this depend node
					foreach( $dependArg['children'] as $childDependency ) {
						
						if( $childDependency['type'] === "get" ){
							if( !isset( $_GET[ $childDependency['id'] ] ) ){
								$errorMessage = "Argument '" . $dependArg['id'] . 
									"' in the Get request is dependent on an argument: '" . $childDependency['id'] . 
									"' on the GET request which is not set in request with id: " . $requestID;
								eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT && !isset($requestNode['errorProcessorID'])) {
									throw new ErrorException($errorMessage);
								}

								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_GET[ $childDependency['id'] ];	
							$requestInfoBean->setGET( $childDependency['id'],  $childVal);
						} else if( $childDependency['type'] === "post" ){
							if( !isset( $_POST[ $childDependency['id'] ] ) ){
								$errorMessage = "Argument '" . $dependArg['id'] . 
									"' in the Get request is dependent on an argument: '" . $childDependency['id'] . 
									"' on the POST request which is not set in request with request ID: " . $requestID;
								eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT && !isset($requestNode['errorProcessorID'])) {
									throw new ErrorException($errorMessage);
								}

								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_POST[ $childDependency['id'] ];	
							$requestInfoBean->setPOST( $childDependency['id'],	$childVal);
							
						}
					}
				}
			
			} else if( $dependArg['type'] === "post" ){
				
				//only continue if the depend id is actually part of this get request
				if( isset( $_POST[ $dependArg['id'] ] ) ){
					
					//get the children values of this depend node
					foreach( $dependArg['children'] as $childDependency ) {
						
						if( $childDependency['type'] === "get" ){
							if( !isset( $_GET[ $childDependency['id'] ] ) ){
								$errorMessage = "Argument '" . $dependArg['id'] . 
									"' in the POST request is dependent on an argument: '" . $childDependency['id'] . 
									"' on the GET request which is not set in request with request ID: " . $requestID;
								eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT && !isset($requestNode['errorProcessorID'])) {
									throw new ErrorException($errorMessage);
								}

								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_GET[ $childDependency['id'] ];	
							$requestInfoBean->setGET( $childDependency['id'],  $childVal);
							

						} else if( $childDependency['type'] === "post" ){
							if( !isset( $_POST[ $childDependency['id'] ] ) ){
								$errorMessage = "Argument '" . $dependArg['id'] . 
									"' in the POST request is dependent on an argument: '" . $childDependency['id'] . 
									"' on the POST request which is not set in request with request ID: " . $requestID;
								eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT && !isset($requestNode['errorProcessorID'])) {
									throw new ErrorException($errorMessage);
								}

								return false;
							}

							//set argument in the request info bean
							$childVal = $_POST[ $childDependency['id'] ];	
							$requestInfoBean->setPOST( $childDependency['id'],	$childVal);

						}
					}
				}
			}
		}

		return $retVal;
	}

	/**
	 * build the decorator array
	 */
	private function buildDecoratorArray( $requestNode, $requestInfoBean ){
		
		$decoratorArray = array();
		$requestNode['decorators'];
		foreach( $requestNode['decorators'] as $decoratorArg ) {
			$decoratorID = $decoratorArg['decoratorID'];
			$order = $decoratorArg['order'];
			$decoratorArray[ $order ] = $decoratorID; 
		}

		//sort the array based on the keys, to get the order correct
		ksort($decoratorArray);

		$requestInfoBean->setDecoratorArray( $decoratorArray );
		
	}

}
