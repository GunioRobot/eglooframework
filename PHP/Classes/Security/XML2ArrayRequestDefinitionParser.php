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
 * @package RequestProcessing
 * @subpackage Security
 * @version 1.0
 */

/**
 * XML2ArrayRequestDefinitionParser
 * 
 * Validates requests against xml requests definition
 * 
 * In future can call multiple classes in the security package to validate the request.
 * Fills the RequestInfoBean with validated Request info or nothing if the request is invalid.
 * Returns true if request is valid, or false if there a problem has been detected.
 *
 * @package RequestProcessing
 * @subpackage Security
 */
final class XML2ArrayRequestDefinitionParser extends eGlooRequestDefinitionParser {

	/**
	 * Static Data Members
	 */
	protected static $singleton;

	/**
	 * This method reads the xml file from disk into a document object model.
	 * It then populates a hash of [requestClassID + RequestID] -> [ Request XML Object ]
	 */
	protected function loadRequestNodes(){
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Processing XML", 'Security' );

        //read the xml onces... global location to do this... it looks like it does this once per request.
		$this->REQUESTS_XML_LOCATION =
			eGlooConfiguration::getApplicationsPath() . '/' . $this->webapp . "/XML/Requests.xml";

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Loading "
			. $this->REQUESTS_XML_LOCATION, 'Security' );

		$requestXMLObject = simplexml_load_file( $this->REQUESTS_XML_LOCATION );

		if (!$requestXMLObject) {
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY,
				'XML2ArrayRequestDefinitionParser: simplexml_load_file( $this->REQUESTS_XML_LOCATION ): ' . libxml_get_errors() );
		}

		$requestClasses = array();

		foreach( $requestXMLObject->xpath( '/tns:Requests/RequestAttributeSet' ) as $attributeSet ) {
			$attributeSetID = isset($attributeSet['id']) ? (string) $attributeSet['id'] : NULL;

			if ( !$attributeSetID || trim($attributeSetID) === '' ) {
				throw new ErrorException("No ID specified in request attribute set.  Please review your Requests.xml");
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
					$newSelectArgument['scalarType'] =  strtolower( (string) $selectArgument['scalarType'] );
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

			$cacheGateway = CacheGateway::getCacheGateway();
			$cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserAttributeNodes::' .
				$uniqueKey, $requestAttributeSets[$attributeSetID], '<type>' );
		}

		foreach( $requestXMLObject->xpath( '/tns:Requests/RequestClass' ) as $requestClass ) {
			$requestClassID = isset($requestClass['id']) ? (string) $requestClass['id'] : NULL;

			if ( !$requestClassID || trim($requestClassID) === '' ) {
				throw new ErrorException("No ID specified in request class.  Please review your Requests.xml");
			}

			$requestClasses[$requestClassID] = array('requestClass' => $requestClassID, 'requests' => array());

            foreach( $requestClass->xpath( 'child::Request' ) as $request ) {
				$requestID = isset($request['id']) ? (string) $request['id'] : NULL;
				$processorID = isset($request['processorID']) ? (string) $request['processorID'] : NULL;

				if ( !$requestID || trim($requestID) === '' ) {
					throw new ErrorException("No request ID specified in request class: '" . $requestClassID .
						"'.  Please review your Requests.xml");
				}

				if ( !$processorID || trim($processorID) === '' ) {
					throw new ErrorException("No processor ID specified in request ID: '" . $requestID .
					"'.  Please review your Requests.xml");
				}

				// Request Properties
				$requestClasses[$requestClassID]['requests'][$requestID] =
					array('requestID' => $requestID, 'processorID' => $processorID );

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

					foreach( $decorators as $decorator ) {
						if ( !isset($requestClasses[$requestClassID]['requests'][$requestID]['decorators'][$decorator['decoratorID']]) ) {
							eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Inserting decorator ' . $decorator['decoratorID'] .
								' from attribute set ' . $requestAttributeSetID , 'Security' );
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

				$cacheGateway = CacheGateway::getCacheGateway();
				$cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes::' .
					$uniqueKey, $requestClasses[$requestClassID]['requests'][$requestID], '<type>' );
            }
        }

		// unset($this->requestNodes);
	}

	protected function init() {
		// static::loadRequestNodes();
	}

	/**
	 * Only functional method available to the public.  
	 * This method ensures that this is valid request, by checking arguments 
	 * against the expectant values in the request XML object. if it is a valid 
	 * request, the request processor id needed process this request is populated
	 * in the request info bean.
	 * 
	 * @return true if this is a valid request, or false if it is not
	 */
	public function validateAndProcess($requestInfoBean) {
		/**
		 * TODO: figure out what really should be done if a request ID or 
		 * request Class is not set
		 */

		/**
		 * Check if there is a request class.  If there isn't, return not setting
		 * any request processor...
		 */
		 if( !isset( $_GET[ self::REQUEST_CLASS_KEY ] )){
			$errorMessage = 'Request class not set in request.  ' . "\n" . 'Verify that mod_rewrite is active and its rules are correct in your .htaccess';
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, $errorMessage, 'Security' );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($errorMessage);
			}

			return false;
		 }

		/**
		 * Check if there is a request id.  If there isn't, return not setting
		 * any request processor...
		 */
		if( !isset( $_GET[ self::REQUEST_ID_KEY ] ) ){
			$errorMessage = 'Request ID not set in request.  ' . "\n\t" . 'Verify that mod_rewrite is active and its rules are correct in your .htaccess';
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, $errorMessage, 'Security' );

			if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw new ErrorException($errorMessage);
			}

			return false;
		}

		$requestClass = $_GET[ self::REQUEST_CLASS_KEY ];
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		$requestLookup = $requestClass . $requestID;
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Incoming Request Class and Request ID lookup is: "' . $requestLookup . '"', 'Security' );

		$cacheGateway = CacheGateway::getCacheGateway();
		$requestNode = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'XML2ArrayRequestDefinitionParserNodes::' .
			$requestLookup, '<type>' );

        if ( $requestNode == null ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Request node not found in cache: ' . $requestLookup, 'Security' );
			$this->loadRequestNodes();
			$requestNode = isset($this->requestNodes[ $requestLookup ]) ? $this->requestNodes[ $requestLookup ] : null;
		}

		// FIX $this->requestNodes will NOT be set here if Memcache is off

		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if( !isset( $requestNode ) ){
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
        // $requestNode = simplexml_load_string( $this->requestNodes[ $requestLookup ] );
		// $this->requestNodes[ $uniqueKey ] = $requestClasses[$requestClassID]['requests'][$requestID];


		$processorID = $requestNode[ self::PROCESSOR_ID_KEY ];
		$requestInfoBean->setRequestProcessorID( $processorID );

		/**
		 * Now verify the contents of the request before we hand this off 
		 * for further processing
		 */

		//check boolean arguments
		if( !$this->validateBooleanArguments( $requestNode, $requestInfoBean ) ) return false;
		
		//check variable arguments
		if( !$this->validateVariableArguments( $requestNode, $requestInfoBean ) ) return false;
		
		//check select arguments
		if( !$this->validateSelectArguments( $requestNode, $requestInfoBean ) ) return false;
		
		//check depend arguments
		if( !$this->validateDependArguments( $requestNode, $requestInfoBean ) ) return false;

		//build decorator array and set it in the requestInfoBean
		$this->buildDecoratorArray( $requestNode, $requestInfoBean);

		/**
		 * If have gotten here with out returning... we're golden.
		 * unset post and get and return
		 */
		 $_POST = null;
		 $_GET = null;

        return true;
	}

// TODO Change array access to references	
	/**
	 * validate boolean arguments
	 * 
	 * @return true if all boolean arguments pass the test, false otherwise
	 */
	private function validateBooleanArguments( &$requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		 
		 foreach( $requestNode['boolArguments'] as $boolArg ) {
			
			if( $boolArg['type'] === "get" ){


				if( !isset( $_GET[ (string) $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] === "true") {
						$errorMessage = "Required boolean parameter: " . $boolArg['id'] .
							" is not set in GET request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
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

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					}

					//set argument in the request info bean
					$requestInfoBean->setGET( $boolArg['id'],  $boolVal );
					
				}

				
			} else if ( $boolArg['type'] === "post" ) {

				if( !isset( $_POST[ $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] === "true") {
						$errorMessage = "Required boolean parameter: " . $boolArg['id'] . 
                            " is not set in post request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					} else if (isset($boolArg['default'])) {
						$requestInfoBean->setPOST( $boolArg['id'],  $boolArg['default'] );
					}

				} else {
					
					//check if correctly formatted (true vs false)
					$boolVal = $_POST[ $boolArg['id'] ];
					if( $boolVal !== "false" && $boolVal !== "true" ){
						$errorMessage = "Boolean parameter: " . $boolArg['id'] . 
                            " is not in correct 'true' or 'false' format in post request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					}

					//set argument in the request info bean
					$requestInfoBean->setPOST( $boolArg['id'],  $boolVal );

				}
			}
		} 
		
		return true;
	}
	
	
	/**
	 * validate variable parameters
	 * 
	 * @return true if all variable arguments pass the test, false otherwise
	 */
	private function validateVariableArguments( $requestNode, $requestInfoBean ){

		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		 
		 foreach( $requestNode['variableArguments'] as $variableArg ) {
			if( $variableArg['type'] === 'get' ){

				if( !isset( $_GET[ $variableArg['id'] ] ) ){
					
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
                            " is not set in GET request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
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

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					}

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

					// $requestInfoBean->setGET( $variableArg['id'],  $variableValue );
				}
			} else if ( $variableArg['type'] === 'getarray' ) {
				if( !isset( $_GET[ $variableArg['id'] ] ) ){
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
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

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					} else if (isset($variableArg['default'])) {
						if ( isset($variableArg['scalarType']) ) {
							if ( $variableArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setPOST( $variableArg['id'],  intval($variableArg['default']));
							} else if ( $variableArg['scalarType'] === 'float' ) {
								$requestInfoBean->setPOST( $variableArg['id'],  floatval($variableArg['default']));
							}
						} else {
							$requestInfoBean->setPOST( $variableArg['id'],  $variableArg['default'] );
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

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					}

					//set argument in the request info bean
					if ( isset($variableArg['scalarType']) ) {
						if ( $variableArg['scalarType'] === 'integer' ) {
							$requestInfoBean->setPOST( $variableArg['id'],  intval($variableValue));
						} else if ( $variableArg['scalarType'] === 'float' ) {
							$requestInfoBean->setPOST( $variableArg['id'],  floatval($variableValue));
						}
					} else {
						$requestInfoBean->setPOST( $variableArg['id'],  $variableValue);
					}

					// $requestInfoBean->setPOST( $variableArg['id'],  $variableValue );
				}
			} else if ( $variableArg['type'] === 'postarray') {
				if( !isset( $_POST[ $variableArg['id'] ] ) ){
					//check if required
					if( $variableArg['required'] === "true") {
						$errorMessage = "Required variable parameter: " . $variableArg['id'] . 
							" is not set in post request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
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
					$requestInfoBean->setPOST( $variableArg['id'],  $sanitizedValues );
				}
			}
		} 
		
		return true;
	}
	


	/**
	 * validate select arguments
	 * 
	 * @return true if all select arguments pass the test, false otherwise
	 */
	private function validateSelectArguments( $requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ];
		 
		 
		 foreach( $requestNode['selectArguments'] as $selectArg ) {
			if( $selectArg['type'] === "get" ){
				if( !isset( $_GET[ $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] === "true") {
						$errorMessage = "Required select argument: " . $selectArg['id'] . 
                            " is not set in GET request with id: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					} else if (isset($selectArg['default'])) {
						if ( isset($selectArg['scalarType']) ) {
							if ( $selectArg['scalarType'] === 'integer' ) {
								$requestInfoBean->setGET( $selectArg['id'],  intval($selectArg['default']));
							} else if ( $selectArg['scalarType'] === 'float' ) {
								$requestInfoBean->setGET( $selectArg['id'],  floatval($selectArg['default']));
							}
						} else {
							$requestInfoBean->setGET( $selectArg['id'],  $selectArg['default'] );
						}

						// $requestInfoBean->setGET( $selectArg['id'],  $selectArg['default'] );
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

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					}

					//set argument in the request info bean
					if ( isset($selectArg['scalarType']) ) {
						if ( $selectArg['scalarType'] === 'integer' ) {
							$requestInfoBean->setGET( $selectArg['id'],  intval($selectVal));
						} else if ( $selectArg['scalarType'] === 'float' ) {
							$requestInfoBean->setGET( $selectArg['id'],  floatval($selectVal));
						}
					} else {
						$requestInfoBean->setGET( $selectArg['id'],  $selectVal);
					}
				}

			} else if( $selectArg['type'] === "post" ) {

				if( !isset( $_POST[ $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] === "true") {
						$errorMessage = "Required select argument: " . $selectArg['id'] . 
                            " is not set in POST request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
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

						// $requestInfoBean->setPOST( $selectArg['id'],  $selectArg['default'] );
					}

				} else {
					
					//check if value is one of the allowable values
					$selectVal = $_POST[ $selectArg['id'] ];
					$match = false;

					foreach( $selectArg['values'] as $validValue ){
						if( $validValue === $selectVal ){
							$match = true;
						}
					}
					
					if( ! $match ){
						$errorMessage = "Select argument: " . $selectArg['id'] . 
                            " with specified value '" . $selectVal . "' does not match required set of variables in " . 
                            "POST request with request ID: " . $requestID;
						eGlooLogger::writeLog( eGlooLogger::DEBUG, $errorMessage, 'Security' );

						if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
							throw new ErrorException($errorMessage);
						}

						return false;
					}

					//set argument in the request info bean
					if ( isset($selectArg['scalarType']) ) {
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
		
		return true;
	}

	/**
	 * validate depend arguments
	 * 
	 * @return true if all depend arguments pass the test, false otherwise
	 */
	private function validateDependArguments( $requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::REQUEST_ID_KEY ]; 
	
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

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
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

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
									throw new ErrorException($errorMessage);
								}

								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_POST[ $childDependency['id'] ];	
							$requestInfoBean->setPOST( $childDependency['id'],  $childVal);
							
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

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
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

								if (eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
									throw new ErrorException($errorMessage);
								}

								return false;
							}

							//set argument in the request info bean
							$childVal = $_POST[ $childDependency['id'] ];	
							$requestInfoBean->setPOST( $childDependency['id'],  $childVal);

						}
					}
				}
			}
		}
		return true;
	}
	
	/**
	 * build the decorator array
	 */
	private function buildDecoratorArray( $requestNode, $requestInfoBean ){
		
		$decoratorArray = array();
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
?>
