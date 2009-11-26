<?php
/**
 * XML2ArrayRequestDefinitionParser Class File
 *
 * Contains the class definition for the xml request definition parser
 * 
 * Copyright 2009 eGloo, LLC
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
 * @copyright 2009 eGloo, LLC
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

	// TODO We need to link this against a shared memory management
	// system for all singletons in the application.  We should be
	// checking for the serialized object in memory and using any
	// pre-existing singleton.  If none is found, we create it.
	// The class for managing shared memory must be written (is not
	// as of this writing)

	/**
	 * Static Constants
	 */
	private static $id = "id";
	private static $class = "class";
	private static $PROCESSOR_ID = "processorID";
	private static $singletonXML2ArrayRequestDefinitionParser;
	
	/**
	 * XML Variables
	 */
	private $REQUESTS_XML_LOCATION = '';
	private $requestNodes = array();

	private $webapp = null;
	private $uibundle = null;

	/**
	 * Private constructor because this class is a singleton
	 */
	private function __construct( $webapp, $uibundle ) {
		$this->webapp = $webapp;
		$this->uibundle = $uibundle;

		$this->loadRequestNodes();
	}

	/**
	 * This method reads the xml file from disk into a document object model.
	 * It then populates a hash of [requestClassID + RequestID] -> [ Request XML Object ]
	 */
	private function loadRequestNodes(){
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Processing XML", 'Security' );

        //read the xml onces... global location to do this... it looks like it does this once per request.
		$this->REQUESTS_XML_LOCATION =
			eGlooConfiguration::getApplicationsPath() . '/' . $this->webapp . "/XML/Requests.xml";

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Loading "
			. $this->REQUESTS_XML_LOCATION, 'Security' );
		
		$requestXMLObject = simplexml_load_file( $this->REQUESTS_XML_LOCATION );

		$requestClasses = array();

		foreach( $requestXMLObject->xpath( '/tns:Requests/RequestClass' ) as $requestClass ) {
			$requestClassID = (string) $requestClass['id'];
			
			$requestClasses[$requestClassID] = array('requestClass' => $requestClassID, 'requests' => array());

            foreach( $requestClass->xpath( 'child::Request' ) as $request ) {
				$requestID = (string) $request['id'];
				$processorID = (string) $request['processorID'];

				// Request Properties
				$requestClasses[$requestClassID]['requests'][$requestID] =
					array('requestID' => $requestID, 'processorID' => $processorID );

				// Arguments
				$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'] = array();

				foreach( $request->xpath( 'child::BoolArgument' ) as $boolArgument ) {
					$newBoolArgument = array();

					$newBoolArgument['id'] = (string) $boolArgument['id'];
					$newBoolArgument['type'] = (string) $boolArgument['type'];
					$newBoolArgument['required'] = (string) $boolArgument['required'];

					$requestClasses[$requestClassID]['requests'][$requestID]['boolArguments'][] = $newBoolArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'] = array();

				foreach( $request->xpath( 'child::SelectArgument' ) as $selectArgument ) {
					$newSelectArgument = array();

					$newSelectArgument['id'] = (string) $selectArgument['id'];
					$newSelectArgument['type'] = (string) $selectArgument['type'];
					$newSelectArgument['required'] = (string) $selectArgument['required'];

					$newSelectArgument['values'] = array();

					foreach( $selectArgument->xpath( 'child::value' ) as $selectArgumentValue ) {
						$newSelectArgument['values'][] = (string) $selectArgumentValue;
					}

					$requestClasses[$requestClassID]['requests'][$requestID]['selectArguments'][] = $newSelectArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'] = array();

				foreach( $request->xpath( 'child::VariableArgument' ) as $variableArgument ) {
					$newVariableArgument = array();

					$newVariableArgument['id'] = (string) $variableArgument['id'];
					$newVariableArgument['type'] = (string) $variableArgument['type'];
					$newVariableArgument['required'] = (string) $variableArgument['required'];
					$newVariableArgument['regex'] = (string) $variableArgument['regex'];

					$requestClasses[$requestClassID]['requests'][$requestID]['variableArguments'][] = $newVariableArgument;
				}

				$requestClasses[$requestClassID]['requests'][$requestID]['depends'] = array();

				foreach( $request->xpath( 'child::Depend' ) as $depend ) {
					$newDepend = array();

					$newDepend['id'] = (string) $depend['id'];
					$newDepend['type'] = (string) $depend['type'];
					$newDepend['children'] = array();

					foreach( $depend->xpath( 'child::Child' ) as $dependChild ) {
						$dependChildID = (string) $dependChild['id'];
						$dependChildType = (string) $dependChild['type'];

						$newDepend['children'][] = array('id' => $dependChildID, 'type' => $dependChildType);
					}

					$requestClasses[$requestClassID]['requests'][$requestID]['depends'][] = $newDepend;
				}

				// Decorators
				$requestClasses[$requestClassID]['requests'][$requestID]['decorators'] = array();

				foreach( $request->xpath( 'child::Decorator' ) as $decorator ) {
					$newDecorator = array();

					$newDecorator['order'] = (string) $decorator['order'];
					$newDecorator['decoratorID'] = (string) $decorator['decoratorID'];

					$requestClasses[$requestClassID]['requests'][$requestID]['decorators'][] = $newDecorator;
				}

                $uniqueKey = ( (string) $requestClass['id'] ) . ( (string) $request['id'] );
                // $this->requestNodes[ $uniqueKey  ] = $request->asXML();
				$this->requestNodes[ $uniqueKey ] = $requestClasses[$requestClassID]['requests'][$requestID];

				$cacheGateway = CacheGateway::getCacheGateway();
				$cacheGateway->storeObject( 'XML2ArrayRequestDefinitionParserNodes::' .
					$uniqueKey, $requestClasses[$requestClassID]['requests'][$requestID], '<type>' );
            }
        }

		unset($this->requestNodes);
	}

	/**
	 * returns the singleton of this class
	 */
    public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
        if ( !isset(self::$singletonXML2ArrayRequestDefinitionParser) ) {
			$cacheGateway = CacheGateway::getCacheGateway();
            
            if ( (self::$singletonXML2ArrayRequestDefinitionParser = $cacheGateway->getObject( 'XML2ArrayRequestDefinitionParserNodes', '<type>' ) ) == null ) {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Building Singleton", 'Security' );
                self::$singletonXML2ArrayRequestDefinitionParser = new XML2ArrayRequestDefinitionParser( $webapp, $uibundle );
                $cacheGateway->storeObject( 'XML2ArrayRequestDefinitionParserNodes', self::$singletonXML2ArrayRequestDefinitionParser, '<type>' );
            } else {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, "XML2ArrayRequestDefinitionParser: Singleton pulled from cache", 'Security' );
            }
        }
        
        return self::$singletonXML2ArrayRequestDefinitionParser;
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
		 * Set the web application and UI bundle
		 */
		$requestInfoBean->setApplication($this->webapp);
		$requestInfoBean->setInterfaceBundle($this->uibundle);

		/**
		 * Check if there is a request class.  If there isn't, return not setting
		 * any request processor...
		 */
		 if( !isset( $_GET[ self::$class] )){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "request class not set in request", 'Security' );
            // TODO Should we set this here?
            // $requestInfoBean->setRequestClass( 'externalMainPage' );
			return false;
		 }
		 
		
		/**
		 * Check if there is a request id.  If there isn't, return not setting
		 * any request processor...
		 */
		if( !isset( $_GET[ self::$id ] ) ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "id not set in request", 'Security' );
            // TODO Should we set this here?
            // $requestInfoBean->setRequestClass( 'externalMainPage' );
			return false;
		}
		
		$requestClass = $_GET[ self::$class];
		$requestID = $_GET[ self::$id ];
		$requestLookup = $requestClass . $requestID;
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "Incoming request CLASS and ID is: $requestLookup", 'Security' );

		$cacheGateway = CacheGateway::getCacheGateway();
		$requestNode = $cacheGateway->getObject( 'XML2ArrayRequestDefinitionParserNodes::' .
			$requestLookup, '<type>' );

        if ( $requestNode == null ) {
			$this->loadRequestNodes();
			$requestNode = isset($this->requestNodes[ $requestLookup ]) ? $this->requestNodes[ $requestLookup ] : null;
		}

		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if( !isset( $requestNode ) ){
 	       eGlooLogger::writeLog( eGlooLogger::DEBUG, 
				"request class and id pairing not found for request class: '" . $requestClass . "' and id '" . $requestID . "'", 'Security' );
 	       return false;
		}

		/**
		 * If this is a valid request class/id, get the request denoted 
		 * by this request class and id.
		 */
        // $requestNode = simplexml_load_string( $this->requestNodes[ $requestLookup ] );
		// $this->requestNodes[ $uniqueKey ] = $requestClasses[$requestClassID]['requests'][$requestID];


		$processorID = $requestNode[ self::$PROCESSOR_ID ];
		$requestInfoBean->setRequestProcessorID( $processorID );
        $requestInfoBean->setRequestClass( $requestClass );
        $requestInfoBean->setRequestID( $requestID );

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
	private function validateBooleanArguments( $requestNode, $requestInfoBean ){
		$requestID = $_GET[ self::$id ];
		 
		 foreach( $requestNode['boolArguments'] as $boolArg ) {
			
			if( $boolArg['type'] == "get" ){


				if( !isset( $_GET[ (string) $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "required boolean parameter: " . $boolArg['id'] . 
                            " is not set in GET request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if correctly formatted (true vs false)
					$boolVal = $_GET[ $boolArg['id'] ];
					if( $boolVal != "false" and $boolVal != "true" ){
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "boolean parameter: " . $boolArg['id'] . 
                            " is not in correct 'true' or 'false' format in GET request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean
					$requestInfoBean->setGET( $boolArg['id'],  $boolVal );
					
				}

				
			} else if( $boolArg['type'] == "post" ) {

				if( !isset( $_POST[ $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "required boolean parameter: " . $boolArg['id'] . 
                            " is not set in post request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if correctly formatted (true vs false)
					$boolVal = $_POST[ $boolArg['id'] ];
					if( $boolVal != "false" and $boolVal != "true" ){
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "boolean parameter: " . $boolArg['id'] . 
                            " is not in correct 'true' or 'false' format in post request with id: " . $requestID, 'Security' );
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

		$requestID = $_GET[ self::$id ];
		 
		 
		 foreach( $requestNode['variableArguments'] as $variableArg ) {
			
			
			if( $variableArg['type'] == "get" ){

				if( !isset( $_GET[ $variableArg['id'] ] ) ){
					
					//check if required
					if( $variableArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "required variable parameter: " . $variableArg['id'] . 
                            " is not set in GET request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if correctly formatted
					$variableValue = $_GET[ $variableArg['id'] ];
					$regexFormat = $variableArg['regex'];
					$match = preg_match ( $regexFormat, $variableValue );
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "variable parameter: " . $variableArg['id'] . 
                            " with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
                            " in GET request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setGET( $variableArg['id'],  $variableValue );
				}


			} else if( $variableArg['type'] == "post" ) {

				if( !isset( $_POST[ $variableArg['id'] ] ) ){
					
					//check if required
					if( $variableArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "required variable parameter: " . $variableArg['id'] . 
                            " is not set in post request with id: " . $requestID, 'Security' );
						return false;
					}

				} else {
					
					//check if correctly formatted
					$variableValue = $_POST[ $variableArg['id'] ];
					$regexFormat = $variableArg['regex'];
					$match = preg_match ( $regexFormat, $variableValue );
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "variable parameter: " . $variableArg['id'] . 
                            " with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
                            " in post request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setPOST( $variableArg['id'],  $variableValue );

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

		$requestID = $_GET[ self::$id ];
		 
		 
		 foreach( $requestNode['selectArguments'] as $selectArg ) {
			
			if( $selectArg['type'] == "get" ){

				if( !isset( $_GET[ $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "required select argument: " . $selectArg['id'] . 
                            " is not set in GET request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if value is one of the allowable values
					$selectVal = $_GET[ $selectArg['id'] ];
					$match = false;
					
					foreach( $selectArg->xpath('child::value/text()') as $validValue ){
						if( $validValue == $selectVal ){
							$match = true;
						}
					}
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "select argument: " . $selectArg['id'] . 
                            " with specified value '" . $selectVal . "' does not match required set of variables in " . 
                            "GET request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setGET( $selectArg['id'],  $selectVal);

				}

				
			} else if( $selectArg['type'] == "post" ) {


				if( !isset( $_POST[ $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "required select argument: " . $selectArg['id'] . 
                            " is not set in POST request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if value is one of the allowable values
					$selectVal = $_POST[ $selectArg['id'] ];
					$match = false;
					
					foreach( $selectArg->xpath('child::value/text()') as $validValue ){
						if( $validValue == $selectVal ){
							$match = true;
						}
					}
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::DEBUG, "select argument: " . $selectArg['id'] . 
                            " with specified value '" . $selectVal . "' does not match required set of variables in " . 
                            "POST request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setPOST( $selectArg['id'],  $selectVal);
					
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
		
		
		
		$requestID = $_GET[ self::$id ]; 
	
		foreach( $requestNode['depends'] as $dependArg ) {
			
			if( $dependArg['type'] == "get" ){
				
				//only continue if the depend id is actually part of this get request
				if( isset( $_GET[ $dependArg['id'] ] ) ){
					
					//get the children values of this depend node
					foreach( $dependArg['children'] as $childDependency ) {
						
						if( $childDependency['type'] == "get" ){
							if( !isset( $_GET[ $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the Get request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the GET request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_GET[ $childDependency['id'] ];	
							$requestInfoBean->setGET( $childDependency['id'],  $childVal);
							
							
						} else if( $childDependency['type'] == "post" ){
							if( !isset( $_POST[ $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the Get request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the POST request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_POST[ $childDependency['id'] ];	
							$requestInfoBean->setPOST( $childDependency['id'],  $childVal);
							
						}
					}
				}
			
			} else if( $dependArg['type'] == "post" ){
				
				//only continue if the depend id is actually part of this get request
				if( isset( $_POST[ $dependArg['id'] ] ) ){
					
					//get the children values of this depend node
					foreach( $dependArg['children'] as $childDependency ) {
						
						if( $childDependency['type'] == "get" ){
							if( !isset( $_GET[ $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the POST request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the GET request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_GET[ $childDependency['id'] ];	
							$requestInfoBean->setGET( $childDependency['id'],  $childVal);
							

						} else if( $childDependency['type'] == "post" ){
							if( !isset( $_POST[ $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the POST request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the POST request which is not set in request with id: " . $requestID, 'Security' );
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
