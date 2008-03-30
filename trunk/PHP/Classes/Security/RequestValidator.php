<?php
/**
 * RequestValidator Class File
 *
 * Contains the class definition for the request validator.
 * 
 * Copyright 2008 eGloo, LLC
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Security
 * @version 1.0
 */

/**
 * RequestValidator
 * 
 * Validates requests against xml
 * 
 * In future can call multiple classes in the security package to validate the request.
 * Fills the RequestInfoBean with validated Request info or nothing if the request is invalid.
 * Returns true if request is valid, or false if there a problem has been detected.
 *
 * @package RequestProcessing
 * @subpackage Security
 */
final class RequestValidator {

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
	private static $singletonRequestValidator;
	
	/**
	 * XML Variables
	 */
	private $REQUESTS_XML_LOCATION = "../XML/Requests/eGloo/OverlayInterface/Requests.xml";
	private $requestNodes = array();


	/**
	 * Private constructor because this class is a singleton
	 */
	private function __construct() {
		$this->loadRequestNodes();	
	}
	
	/**
	 * This method reads the xml file from disk into a document object model.
	 * It then populates a hash of [requestClassID + RequestID] -> [ Request XML Object ]
	 */
	private function loadRequestNodes(){
        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "RequestValidator: Processing XML", 'Security' );

        //read the xml onces... global location to do this... it looks like it does this once per request.
        $requestXMLObject = simplexml_load_file( $this->REQUESTS_XML_LOCATION );
        foreach( $requestXMLObject->xpath( '/tns:Requests/RequestClass' ) as $requestClass ) {
            
            foreach( $requestClass->xpath( 'child::Request' ) as $request ){
                
                $uniqueKey = ( (string) $requestClass['id'] ) . ( (string) $request['id']  );
            //  eGlooLogger::writeLog( eGlooLogger::$DEBUG, "adding new unique requestKey: " . $uniqueKey );
                $this->requestNodes[ $uniqueKey  ] = $request->asXML();
            
            }   
        
        }        
	}

	/**
	 * returns the singleton of this class
	 */
    public static function getInstance() {
        if ( !isset(self::$singletonRequestValidator) ) {
            $cacheGateway = CacheGateway::getCacheGateway();
            
            if ( (self::$singletonRequestValidator = $cacheGateway->getObject( 'requestValidatorNodes', '<type>' ) ) == null ) {
                eGlooLogger::writeLog( eGlooLogger::$DEBUG, "RequestValidator: Building Singleton", 'Security' );
                self::$singletonRequestValidator = new RequestValidator();
                $cacheGateway->storeObject( 'requestValidatorNodes', self::$singletonRequestValidator, '<type>' );
            } else {
                eGlooLogger::writeLog( eGlooLogger::$DEBUG, "RequestValidator: Singleton pulled from cache", 'Security' );
            }
        }
        
        return self::$singletonRequestValidator;
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
		 * any request processor... this is ok, must be the main page.
		 */
		 if( !isset( $_GET[ self::$class] )){
			eGlooLogger::writeLog( eGlooLogger::$DEBUG, "request class not set in request", 'Security' );
            // TODO Should we set this here?
            $requestInfoBean->setRequestClass( 'externalMainPage' );
			return true;
		 }
		 
		
		/**
		 * Check if there is a request id.  If there isn't, return not setting
		 * any request processor... this is ok, must be the main page.
		 */
		if( !isset( $_GET[ self::$id ] ) ){
			eGlooLogger::writeLog( eGlooLogger::$DEBUG, "id not set in request", 'Security' );
            // TODO Should we set this here?
            $requestInfoBean->setRequestClass( 'externalMainPage' );
			return true;
		}
		
		$requestClass = $_GET[ self::$class];
		$requestID = $_GET[ self::$id ];
		$requestLookup = $requestClass . $requestID;
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Incoming request CLASS and ID is: $requestLookup", 'Security' );
		
		/**
		 * Ensure that there is a request that corresponds to this request class
		 * and id, if not, return false.
		 */
		if( !isset( $this->requestNodes[ $requestLookup ]) ){
 	       eGlooLogger::writeLog( eGlooLogger::$DEBUG, 
				"request class and id pairing not found for request class: '" . $requestClass . "' and id '" . $requestID . "'", 'Security' );
 	       return false;
		}
		
		/**
		 * If this is a valid request class/id, get the request denoted 
		 * by this request class and id.
		 */
        $requestNode = simplexml_load_string( $this->requestNodes[ $requestLookup ] );
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
		 
		 foreach( $requestNode->xpath( 'child::BoolArgument' ) as $boolArg ) {
			
			if( $boolArg['type'] == "get" ){


				if( !isset( $_GET[ (string) $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "required boolean parameter: " . $boolArg['id'] . 
                            " is not set in GET request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if correctly formatted (true vs false)
					$boolVal = $_GET[ (string) $boolArg['id'] ];
					if( $boolVal != "false" and $boolVal != "true" ){
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "boolean parameter: " . $boolArg['id'] . 
                            " is not in correct 'true' or 'false' format in GET request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setGET( (string) $boolArg['id'],  $boolVal );
					
				}

				
			} else if( $boolArg['type'] == "post" ) {

				if( !isset( $_POST[ (string) $boolArg['id'] ] ) ){
					
					//check if required
					if( $boolArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "required boolean parameter: " . $boolArg['id'] . 
                            " is not set in post request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if correctly formatted (true vs false)
					$boolVal = $_POST[ (string) $boolArg['id'] ];
					if( $boolVal != "false" and $boolVal != "true" ){
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "boolean parameter: " . $boolArg['id'] . 
                            " is not in correct 'true' or 'false' format in post request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setPOST( (string) $boolArg['id'],  $boolVal );
					
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
		 
		 
		 foreach( $requestNode->xpath( 'child::VariableArgument' ) as $variableArg ) {
			
			
			if( $variableArg['type'] == "get" ){

				if( !isset( $_GET[ (string) $variableArg['id'] ] ) ){
					
					//check if required
					if( $variableArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "required variable parameter: " . $variableArg['id'] . 
                            " is not set in GET request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if correctly formatted
					$variableValue = $_GET[ (string) $variableArg['id'] ];
					$regexFormat = (string) $variableArg['regex'];
					$match = preg_match ( $regexFormat, $variableValue );
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "variable parameter: " . $variableArg['id'] . 
                            " with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
                            " in GET request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setGET( (string) $variableArg['id'],  $variableValue );
				}


			} else if( $variableArg['type'] == "post" ) {

				if( !isset( $_POST[ (string) $variableArg['id'] ] ) ){
					
					//check if required
					if( $variableArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "required variable parameter: " . $variableArg['id'] . 
                            " is not set in post request with id: " . $requestID, 'Security' );
						return false;
					}

				} else {
					
					//check if correctly formatted
					$variableValue = $_POST[ (string) $variableArg['id'] ];
					$regexFormat = (string) $variableArg['regex'];
					$match = preg_match ( $regexFormat, $variableValue );
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "variable parameter: " . $variableArg['id'] . 
                            " with value '" . $variableValue . "' is not in a correct format of " . $regexFormat . 
                            " in post request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setPOST( (string) $variableArg['id'],  $variableValue );

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
		 
		 
		 foreach( $requestNode->xpath( 'child::SelectArgument' ) as $selectArg ) {
			
			if( $selectArg['type'] == "get" ){

				if( !isset( $_GET[ (string) $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "required select argument: " . $selectArg['id'] . 
                            " is not set in GET request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if value is one of the allowable values
					$selectVal = $_GET[ (string) $selectArg['id'] ];
					$match = false;
					
					foreach( $selectArg->xpath('child::value/text()') as $validValue ){
						if( $validValue == $selectVal ){
							$match = true;
						}
					}
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "select argument: " . $selectArg['id'] . 
                            " with specified value '" . $selectVal . "' does not match required set of variables in " . 
                            "GET request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setGET( (string) $selectArg['id'],  $selectVal);

				}

				
			} else if( $selectArg['type'] == "post" ) {


				if( !isset( $_POST[ (string) $selectArg['id'] ] ) ){
					
					//check if required
					if( $selectArg['required'] == "true") {
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "required select argument: " . $selectArg['id'] . 
                            " is not set in POST request with id: " . $requestID, 'Security' );
						return false;
					}
			
				} else {
					
					//check if value is one of the allowable values
					$selectVal = $_POST[ (string) $selectArg['id'] ];
					$match = false;
					
					foreach( $selectArg->xpath('child::value/text()') as $validValue ){
						if( $validValue == $selectVal ){
							$match = true;
						}
					}
					
					if( ! $match ){
						eGlooLogger::writeLog( eGlooLogger::$DEBUG, "select argument: " . $selectArg['id'] . 
                            " with specified value '" . $selectVal . "' does not match required set of variables in " . 
                            "POST request with id: " . $requestID, 'Security' );
						return false;
					}

					//set argument in the request info bean					
					$requestInfoBean->setPOST( (string) $selectArg['id'],  $selectVal);
					
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
	
		foreach( $requestNode->xpath( 'child::Depend' ) as $dependArg ) {
			
			if( $dependArg['type'] == "get" ){
				
				//only continue if the depend id is actually part of this get request
				if( isset( $_GET[ (string) $dependArg['id'] ] ) ){
					
					//get the children values of this depend node
					foreach( $dependArg->xpath( 'child::Child' ) as $childDependency ) {
						
						if( $childDependency['type'] == "get" ){
							if( !isset( $_GET[ (string) $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::$DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the Get request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the GET request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_GET[ (string) $childDependency['id'] ];	
							$requestInfoBean->setGET( (string) $childDependency['id'],  $childVal);
							
							
						} else if( $childDependency['type'] == "post" ){
							if( !isset( $_POST[ (string) $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::$DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the Get request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the POST request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_POST[ (string) $childDependency['id'] ];	
							$requestInfoBean->setPOST( (string) $childDependency['id'],  $childVal);
							
						}
					}
				}
			
			} else if( $dependArg['type'] == "post" ){
				
				//only continue if the depend id is actually part of this get request
				if( isset( $_POST[ (string) $dependArg['id'] ] ) ){
					
					//get the children values of this depend node
					foreach( $dependArg->xpath( 'child::Child' ) as $childDependency ) {
						
						if( $childDependency['type'] == "get" ){
							if( !isset( $_GET[ (string) $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::$DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the POST request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the GET request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}
							
							//set argument in the request info bean
							$childVal = $_GET[ (string) $childDependency['id'] ];	
							$requestInfoBean->setGET( (string) $childDependency['id'],  $childVal);
							

						} else if( $childDependency['type'] == "post" ){
							if( !isset( $_POST[ (string) $childDependency['id'] ] ) ){
								eGlooLogger::writeLog( eGlooLogger::$DEBUG, "argument '" . $dependArg['id'] . 
                                    "' in the POST request is dependent on an argument: '" . $childDependency['id'] . 
                                    "' on the POST request which is not set in request with id: " . $requestID, 'Security' );
								return false;
							}

							//set argument in the request info bean
							$childVal = $_POST[ (string) $childDependency['id'] ];	
							$requestInfoBean->setPOST( (string) $childDependency['id'],  $childVal);

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
		foreach( $requestNode->xpath( 'child::Decorator' ) as $decoratorArg ) {
			$decoratorID = (string) $decoratorArg['decoratorID'];
			$order = (string) $decoratorArg['order'];
			$decoratorArray[ $order ] = $decoratorID; 
		}
		
		//sort the array based on the keys, to get the order correct
		ksort($decoratorArray);
		
		$requestInfoBean->setDecoratorArray( $decoratorArray );
		
	}

}
?>
