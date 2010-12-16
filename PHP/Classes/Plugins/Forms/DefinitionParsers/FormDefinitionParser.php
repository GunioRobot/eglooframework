<?php
/**
 * FormDefinitionParser Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * FormDefinitionParser
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
final class FormDefinitionParser {

	/**
	 * Static Data Members
	 */
	private static $_singleton = null;

	private static $_formNodes;

	private function __construct() {
		
	}

	public static function getInstance() {
		if (!self::$singleton) {
			self::$singleton = new FormDefinitionParser();
		}

		return self::$singleton;
	}

	/**
	 * This method reads the forms xml file from disk into a document object model.
	 */
	protected function loadFormDefinitions(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "FormDefinitionParser: Processing XML", 'Forms' );

		$forms_xml_location = eGlooConfiguration::getApplicationsPath() . '/' . $this->webapp . '/XML/Forms.xml';

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'FormDefinitionParser: Loading ' . $forms_xml_location, 'Forms' );

		$formsXMLObject = simplexml_load_file( $forms_xml_location );
		// $requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

		if (!$formsXMLObject) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'FormDefinitionParser: simplexml_load_file( "' . $forms_xml_location . '" ): ' . libxml_get_errors() );
		}

		$forms = array();

		foreach( $requestXMLObject->xpath( '/tns:Forms/Form' ) as $formNode ) {
			$formNodeID = isset($formNode['id']) ? (string) $formNode['id'] : NULL;

			if ( !$formNodeID || trim($formNodeID) === '' ) {
				throw new ErrorException("No ID specified in request class.	 Please review your Forms.xml");
			}

			$formNodes[$formNodeID] = array('form' => $formNodeID, 'requests' => array());

			foreach( $requestClass->xpath( 'child::Request' ) as $request ) {
				$requestID = isset($request['id']) ? (string) $request['id'] : NULL;
				$processorID = isset($request['processorID']) ? (string) $request['processorID'] : NULL;
				$errorProcessorID = isset($request['errorProcessorID']) ? (string) $request['errorProcessorID'] : NULL;

				if ( !$requestID || trim($requestID) === '' ) {
					throw new ErrorException("No request ID specified in request class: '" . $formNodeID .
						"'.	 Please review your Requests.xml");
				}

				if ( !$processorID || trim($processorID) === '' ) {
					throw new ErrorException("No processor ID specified in request ID: '" . $requestID .
					"'.	 Please review your Requests.xml");
				}

				// Request Properties
				$formNodes[$formNodeID]['requests'][$requestID] =
					array('requestClass' => $formNodeID, 'requestID' => $requestID, 'processorID' => $processorID, 'errorProcessorID' => $errorProcessorID );

				// Arguments
				$formNodes[$formNodeID]['requests'][$requestID]['boolArguments'] = array();

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

					$formNodes[$formNodeID]['requests'][$requestID]['boolArguments'][$newBoolArgument['id']] = $newBoolArgument;
				}

				$formNodes[$formNodeID]['requests'][$requestID]['selectArguments'] = array();

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

					$formNodes[$formNodeID]['requests'][$requestID]['selectArguments'][$newSelectArgument['id']] = $newSelectArgument;
				}

				$formNodes[$formNodeID]['requests'][$requestID]['variableArguments'] = array();

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

					$formNodes[$formNodeID]['requests'][$requestID]['variableArguments'][$newVariableArgument['id']] = $newVariableArgument;
				}

				$formNodes[$formNodeID]['requests'][$requestID]['complexArguments'] = array();

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

					$formNodes[$formNodeID]['requests'][$requestID]['complexArguments'][$newComplexArgument['id']] = $newComplexArgument;
				}

				$formNodes[$formNodeID]['requests'][$requestID]['depends'] = array();

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

					$formNodes[$formNodeID]['requests'][$requestID]['depends'][$newDepend['id']] = $newDepend;
				}


				$uniqueKey = ( (string) $requestClass['id'] ) . ( (string) $request['id'] );

				$this->requestNodes[ $uniqueKey ] = $formNodes[$formNodeID]['requests'][$requestID];

				$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

				$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDefinitionParserNodes::' .
					$uniqueKey, $formNodes[$formNodeID]['requests'][$requestID], 'RequestValidation', 0, true );
			}
		}

		$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

		$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDefinitionParserNodes',
			$this->requestNodes, 'RequestValidation', 0, true );

		$requestProcessingCacheRegionHandler->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDefinitionParser::NodesCached', true, 'RequestValidation', 0, true );
	}

	/**
	 * Only functional method available to the public.	
	 * 
	 * @return Fully built and validated form object or false on error
	 */
	public function processForm( $form_name, $parameter_method ) {
		/**
		 * Check if there is a request class.  If there isn't, return not setting
		 * any request processor...
		 */
		 // if( !isset( $_GET[ self::REQUEST_CLASS_KEY ] )){
		 // 			$errorMessage = 'Request class not set in request.	' . "\n" . 'Verify that mod_rewrite is active and its rules are correct in your .htaccess';
		 // 			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, $errorMessage, 'Forms' );
		 // 
		 // 			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
		 // 				throw new ErrorException($errorMessage);
		 // 			}
		 // 
		 // 			return false;
		 // }

		$requestClass = $_GET[ self::REQUEST_CLASS_KEY ];
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$requestLookup = $requestClass . $requestID;

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Incoming Request Class and Request ID lookup is: "' . $requestLookup . '"', 'Forms' );

		$requestProcessingCacheRegionHandler = CacheManagementDirector::getCacheRegionHandler('RequestProcessing');

		$allNodesCached = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
			'FormDefinitionParser::NodesCached', 'RequestValidation', true );
		$requestNode = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDefinitionParserNodes::' .
			$requestLookup, 'RequestValidation', true );

		if ( $allNodesCached && !$requestNode ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in cache, checking wildcards: ' . $requestLookup, 'Forms' );
			$useRequestIDDefaultHandler = eGlooConfiguration::getUseDefaultRequestIDHandler();
			$useRequestClassDefaultHandler = eGlooConfiguration::getUseDefaultRequestClassHandler();

			// $allNodesCached = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' .
			// 	'FormDefinitionParser::NodesCached', 'RequestValidation' );

			// We have already parsed the XML once, so let's check down our wildcard options.  I want to refactor thi
			if ( $allNodesCached && ($useRequestIDDefaultHandler || $useRequestClassDefaultHandler) ) {
				// We didn't find the request node and we are cached, so let's see if this request class has a request ID default cached
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in cache, but cache was populated: ' . $requestLookup, 'Forms' );

				if ( $useRequestIDDefaultHandler) {
					eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Checking for requestID wildcard in cache: ' . $requestClass . self::$_requestIDWildcard, 'Forms' );
					$requestNode = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDefinitionParserNodes::' .
						$requestClass . self::$_requestIDWildcard, 'RequestValidation', true );

					if ( $requestNode != null && is_array($requestNode) ) {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RequestID wildcard found in cache: ' . $requestClass . self::$_requestIDWildcard, 'Forms' );
						$requestInfoBean->setWildCardRequest( true );
						$requestInfoBean->setWildCardRequestID( $requestID );
						$requestInfoBean->setRequestID( self::$_requestIDWildcard );
					} else {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, 'RequestID wildcard not found in cache: ' . $requestClass . self::$_requestIDWildcard, 'Forms' );
					}
				}

				if ( $requestNode == null && $useRequestClassDefaultHandler ) {
					// Still no request node, let's see if there's a generic set in cache
					eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Checking for default request wildcard in cache: ' . self::$_requestClassWildcard . self::$_requestIDWildcard, 'Forms' );
					$requestNode = $requestProcessingCacheRegionHandler->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'FormDefinitionParserNodes::' .
						self::$_requestClassWildcard . self::$_requestIDWildcard, 'RequestValidation', true );

					if ( $requestNode != null && is_array($requestNode) ) {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Default request wildcard found in cache: ' . self::$_requestClassWildcard . self::$_requestIDWildcard, 'Forms' );
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
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request nodes not cached, loading: ' . $requestLookup, 'Forms' );
			$this->loadFormDefinitions();

			// Same logic as above, except we're checking what we loaded from XML
			if ( isset($this->requestNodes[ $requestLookup ]) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node found in XML: ' . $requestLookup, 'Forms' );
				$requestNode = $this->requestNodes[ $requestLookup ];
			} else if ( $useRequestIDDefaultHandler && isset($this->requestNodes[ $requestClass . self::$_requestIDWildcard ]) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in XML, using requestID wildcard: ' . $requestClass . self::$_requestIDWildcard, 'Forms' );
				$requestNode = $this->requestNodes[ $requestClass . self::$_requestIDWildcard ];
				$requestInfoBean->setWildCardRequest( true );
				$requestInfoBean->setWildCardRequestID( $requestID );
				$requestInfoBean->setRequestID( self::$_requestIDWildcard );					
			} else if ( $useRequestClassDefaultHandler && isset($this->requestNodes[ self::$_requestClassWildcard . self::$_requestIDWildcard ]) ) {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in XML, using wildcard default: ' . self::$_requestClassWildcard . self::$_requestIDWildcard, 'Forms' );
				$requestNode = $this->requestNodes[ self::$_requestClassWildcard . self::$_requestIDWildcard ];
				$requestInfoBean->setWildCardRequest( true );
				$requestInfoBean->setWildCardRequestClass( $requestClass );
				$requestInfoBean->setWildCardRequestID( $requestID );
				$requestInfoBean->setRequestClass( self::$_requestClassWildcard );
				$requestInfoBean->setRequestID( self::$_requestIDWildcard );
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in XML, wildcards disabled: ' . $requestLookup, 'Forms' );
				$requestNode = null;
			}

		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Invalid state: '" . $requestClass . "' and request ID '" . $requestID . "'", 'Forms' );
		}

		// FIX $this->requestNodes will NOT be set here if Memcache is off

		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if( !isset( $requestNode ) || !is_array( $requestNode ) ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 
				"Request pairing not found for request class: '" . $requestClass . "' and request ID '" . $requestID . "'", 'Forms' );
			
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

		return $retVal;
	}

}
