<?php
/**
 * eGlooRequestDefinitionParser Class File
 *
 * Contains the class definition for the abstract eGloo request definition parser
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
	const REQUEST_ID_KEY = "id";
	const REQUEST_CLASS_KEY = "class";
	const PROCESSOR_ID_KEY = "processorID";

	// We DO NOT declare this so that child classes will define it and will be responsible
	// for containing their own singletons.  This is a performance boost
	// protected static $singleton;

	/**
	 * XML Variables
	 */
	protected $REQUESTS_XML_LOCATION = '';
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

			static::loadRequestNodes();
		}

		// $this injected; magic method invocation
		static::init();
	}

	/**
	 * See if we can do coolness here with late static binding
	 */
	final public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
		$calledDefinitionParser = get_called_class();

        // return isset(static::$INSTANCE) ? static::$INSTANCE : static::$INSTANCE = new static;
        if ( !isset(static::$singleton) ) {
			$cacheGateway = CacheGateway::getCacheGateway();

            if ( (static::$singleton = $cacheGateway->getObject( $calledDefinitionParser . 'Singleton', '<type>' ) ) == null ) {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, $calledDefinitionParser . ': Building Singleton', 'Security' );

				// Unset whatever memcache set this to
				static::$singleton = null;

				static::$singleton = new static( $webapp, $uibundle );

                $cacheGateway->storeObject( $calledDefinitionParser . 'Singleton', static::$singleton, '<type>' );
            } else {
                eGlooLogger::writeLog( eGlooLogger::DEBUG, $calledDefinitionParser . ': Singleton pulled from cache', 'Security' );
            }
        }
        
        return static::$singleton;
	}

	/**
	 * Only functional method available to the public.  
	 * This method ensures that this is valid request, by checking arguments 
	 * against the expectant values in the request definition object. if it is a valid 
	 * request, the request processor id needed process this request is populated
	 * in the request info bean.
	 * 
	 * @return true if this is a valid request, or false if it is not
	 */
	abstract public function validateAndProcess($requestInfoBean);

	abstract protected function loadRequestNodes();

	protected function init() {}

	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of ' . get_called_class() . ' already exists');
	}

}
