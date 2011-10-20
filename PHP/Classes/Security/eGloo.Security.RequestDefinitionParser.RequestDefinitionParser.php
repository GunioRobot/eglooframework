<?php
namespace eGloo\Security\RequestDefinitionParser;

use \eGloo\Configuration as Configuration;
use \eGloo\Utility\Logger as Logger;

use \eGloo\Performance\Caching\Gateway as CacheGateway;

use \ErrorException as ErrorException;
use \Exception as Exception;

/**
 * eGloo\Security\RequestDefinitionParser\RequestDefinitionParser Class File
 *
 * Contains the class definition for the abstract eGloo request definition parser
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
 * @package RequestProcessing
 * @subpackage Security
 * @version 1.0
 */

/**
 * eGloo\Security\RequestDefinitionParser\RequestDefinitionParser
 * 
 * Validates requests against eGloo requests definitions
 * 
 * In future can call multiple classes in the security package to validate the request.
 * Fills the RequestInfoBean with validated Request info or nothing if the request is invalid.
 * Returns true if request is valid, or false if there a problem has been detected.
 *
 * @package RequestProcessing
 * @subpackage Security
 */
abstract class RequestDefinitionParser {

	// Class Constants
	const REQUEST_ID_KEY = 'eg_requestID';
	const REQUEST_CLASS_KEY = 'eg_requestClass';
	const PROCESSOR_ID_KEY = 'processorID';
	const ERROR_PROCESSOR_ID_KEY = 'errorProcessorID';
	const REQUEST_CLASS_WILDCARD_KEY = 'egDefault';
	const REQUEST_ID_WILDCARD_KEY = 'egDefault';

	// We DO NOT declare this so that child classes will define it and will be responsible
	// for containing their own singletons.  This is a performance boost
	// protected static $singleton;

	// The location of the Requests.xml we're concerned with parsing
	protected $REQUESTS_XML_LOCATION = '';

	// An array representing the RequestAttributeSet definitions parsed out of the Requests.xml
	protected $attributeSets = array();

	// An array representing the Request node definitions parsed out of the Requests.xml
	protected $requestNodes = array();

	// Local data members for subclasses to reference which eGloo application and UI bundle we're working with
	protected $webapp;
	protected $uibundle;

	/**
	 * eGloo\Security\RequestDefinitionParser\RequestDefinitionParser Constructor
	 *
	 * We create a final private constructor so that no class that inherits from this abstract class
	 * can instantiate objects without implementing the singleton pattern.  We enforce this pattern
	 * because no eGloo\Security\RequestDefinitionParser\RequestDefinitionParser needs to have more than one object instantiated at any
	 * time.
	 * 
	 * The constructor takes two arguments: the eGloo app to parse requests for and the relevant UI bundle.
	 * The UI bundle is not used for validation purposes, but is stored in the RequestInfoBean for quick
	 * access in the invoked RequestProcessor later in the runtime.
	 *
	 * @param $webapp a string containing the name of the eGloo application whose Requests.xml we're parsing
	 * @param $uibundle a string containing the name of the UI bundle for the eGloo application in question
	 *
	 * @throws eGloo\Security\RequestDefinitionParser\Exception if attempting to construct a second instance of this class 
	 */
	final private function __construct( $webapp, $uibundle ) {
		// Make sure we haven't already constructed a singleton instance.  Throw exception if we have
		if ( isset(static::$singleton) ) {
			throw new Exception('Attempted __construct(): An instance of ' . get_called_class() . ' already exists');
		} else {
			$this->webapp = $webapp;
			$this->uibundle = $uibundle;
		}

		// $this is injected; magic method invocation
		static::init();
	}

	/**
	 * A final static method for getting a singleton instance of this class that all subclasses can reference
	 *
	 * To enforce the singleton pattern down the inheritance tree we provide the getInstance method for getting
	 * a singleton instance of the relevant subclass being invoked.  Because we can get away with using late
	 * static binding, this method is left as final and no subclass should override it.
	 *
	 * @param $webapp a string containing the name of the eGloo application whose Requests.xml we're parsing
	 * @param $uibundle a string containing the name of the UI bundle for the eGloo application in question
	 *
	 * @return the instantiated singleton of whichever subclass this method was invoked in using late static binding
	 */
	final public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
		// $calledDefinitionParser = get_called_class();

		if ( !isset(static::$singleton) ) {
			static::$singleton = new static( $webapp, $uibundle );
		}

        return static::$singleton;
	}

	/**
	 * A method to initialize the request info bean
	 *
	 * This method takes a fresh RequestInfoBean and sets its initial state before request validation occurs
	 *
	 * @param $requestInfoBean the info bean to initialize
	 *
	 * @return true on successful initialization, false otherwise
	 */
	public function initializeInfoBean( $requestInfoBean ) {
		$retVal = true;

		// Set the web application and UI bundle
		$requestInfoBean->setApplication( $this->webapp );
		$requestInfoBean->setInterfaceBundle( $this->uibundle );

		// Check if there is a request class.  If there isn't, log it and return not setting any request processor
		if ( !isset( $_GET[ self::REQUEST_CLASS_KEY ] ) ) {
			if ( Configuration::getUseDefaultRequestClassHandler() ) {
				$requestInfoBean->setRequestClass( self::REQUEST_CLASS_WILDCARD_KEY );
				$requestInfoBean->setRequestID( self::REQUEST_ID_WILDCARD_KEY );
			} else {
				Logger::writeLog( Logger::EMERGENCY, 'eGloo\Security\RequestDefinitionParser\RequestDefinitionParser: Request class not set in request.  ' . "\n" .
					'Verify that mod_rewrite is active and its rules are correct in your .htaccess', 'Security' );
				$retVal = false;
			}
		} else {
			$requestInfoBean->setRequestClass( $_GET[ self::REQUEST_CLASS_KEY ] );
		}

		// Check if there is a request id.  If there isn't, log it and return not setting any request processor
		if ( !isset( $_GET[ self::REQUEST_ID_KEY ] ) ) {
			if ( Configuration::getUseDefaultRequestIDHandler() ) {
				$requestInfoBean->setRequestID( self::REQUEST_ID_WILDCARD_KEY );
			} else {
				Logger::writeLog( Logger::EMERGENCY, 'eGloo\Security\RequestDefinitionParser\RequestDefinitionParser: Request ID not set in request.  ' . "\n" .
					'Verify that mod_rewrite is active and its rules are correct in your .htaccess', 'Security' );
				$retVal = false;
			}
		} else {
			$requestInfoBean->setRequestID( $_GET[ self::REQUEST_ID_KEY ] );
		}

		return $retVal;
	}

	/**
	 * This method ensures that this is valid request, by checking arguments 
	 * against the expectant values in the request definition object. if it is a valid 
	 * request, the request processor id needed process this request is populated
	 * in the request info bean.
	 * 
	 * @return true if this is a valid request, false otherwise
	 */
	abstract public function validateAndProcess($requestInfoBean);

	/**
	 * A definition parser must include this method to handle loading  and processing request
	 * nodes and requests attribute sets from the requests definition XML (Requests.xml)
	 */
	abstract public function loadRequestNodes();

	/**
	 * This method gets called when a definition parser is instantiated.  It allows
	 * subclasses to handle their initialization without overriding their parent's constructor
	 */
	protected function init() {
		static::loadRequestNodes();
	}

	/**
	 * We disallow object cloning to enforce the singleton pattern
	 *
	 * @throws Exception if this method is invoked
	 */
	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of ' . get_called_class() . ' already exists');
	}

}
