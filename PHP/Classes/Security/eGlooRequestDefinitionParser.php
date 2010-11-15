<?php
/**
 * eGlooRequestDefinitionParser Class File
 *
 * Contains the class definition for the abstract eGloo request definition parser
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
 * eGlooRequestDefinitionParser
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
abstract class eGlooRequestDefinitionParser {

	/* Static Data Members */
	const REQUEST_ID_KEY = "eg_requestID";
	const REQUEST_CLASS_KEY = "eg_requestClass";
	const PROCESSOR_ID_KEY = "processorID";
	const ERROR_PROCESSOR_ID_KEY = "processorID";

	// We DO NOT declare this so that child classes will define it and will be responsible
	// for containing their own singletons.  This is a performance boost
	// protected static $singleton;

	/**
	 * XML Variables
	 */
	protected $REQUESTS_XML_LOCATION = '';
	protected $attributeSets = array();
	protected $requestNodes = array();

	protected $webapp;
	protected $uibundle;

	final private function __construct( $webapp, $uibundle ) {
		$calledDefinitionParser = get_called_class();

		if ( isset(static::$singleton) ) {
			throw new Exception('Attempted __construct(): An instance of ' . get_called_class() . ' already exists');
		} else {
			$this->webapp = $webapp;
			$this->uibundle = $uibundle;
		}

		// $this injected; magic method invocation
		static::init();
	}

	/**
	 * See if we can do coolness here with late static binding
	 */
	final public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
		$calledDefinitionParser = get_called_class();

		if ( !isset(static::$singleton) ) {
			static::$singleton = new static( $webapp, $uibundle );
		}

        return static::$singleton;
	}

	/**
	 * A method to initialize the request info bean
	 *
	 * @param $requestInfoBean the info bean to initialize
	 */
	public function initializeInfoBean($requestInfoBean) {
		$retVal = true;

		/**
		 * Set the web application and UI bundle
		 */
		$requestInfoBean->setApplication($this->webapp);
		$requestInfoBean->setInterfaceBundle($this->uibundle);

		/**
		 * Check if there is a request class.  If there isn't, return not setting
		 * any request processor...
		 */
		 if( !isset( $_GET[ self::REQUEST_CLASS_KEY ] )){
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Request class not set in request.  ' . "\n" .
				'Verify that mod_rewrite is active and its rules are correct in your .htaccess', 'Security' );
			$retVal = false;
		 }

		/**
		 * Check if there is a request id.  If there isn't, return not setting
		 * any request processor...
		 */
		if( !isset( $_GET[ self::REQUEST_ID_KEY ] ) ){
			eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Request ID not set in request.  ' . "\n" .
				'Verify that mod_rewrite is active and its rules are correct in your .htaccess', 'Security' );
			$retVal = false;
		}

		// Grab the request class and request ID
        $requestInfoBean->setRequestClass( $_GET[ self::REQUEST_CLASS_KEY ] );
        $requestInfoBean->setRequestID( $_GET[ self::REQUEST_ID_KEY ] );

		return $retVal;
	}

	/**
	 * This method ensures that this is valid request, by checking arguments 
	 * against the expectant values in the request definition object. if it is a valid 
	 * request, the request processor id needed process this request is populated
	 * in the request info bean.
	 * 
	 * @return true if this is a valid request, or false if it is not
	 */
	abstract public function validateAndProcess($requestInfoBean);

	/**
	 * A definition parser must include this method to handle loading request
	 * nodes from the requests definition XML
	 */
	abstract protected function loadRequestNodes();

	/**
	 * This method gets called when a definition parser is instantiated.  It allows
	 * subclasses to handle their initialization without overriding their parent's constructor
	 */
	protected function init() {
		static::loadRequestNodes();
	}

	/**
	 * We disallow object cloning to enforce the singleton pattern
	 */
	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of ' . get_called_class() . ' already exists');
	}

}
