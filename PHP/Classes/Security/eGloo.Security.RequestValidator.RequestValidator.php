<?php
namespace eGloo\Security\RequestValidator;

use eGloo\Configuration as Configuration;
use eGloo\Logger as Logger;

use eGloo\Performance\Caching\Gateway as CacheGateway;

use \DOMDocument as DOMDocument;
use \ErrorException as ErrorException;
use \Exception as Exception;
use \SimpleXMLElement as SimpleXMLElement;

/**
 * eGloo\Security\RequestValidator\RequestValidator Class File
 *
 * Contains the class definition for the request validator.
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
 * eGloo\Security\RequestValidator\RequestValidator
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
class RequestValidator {

	/**
	 * Static Constants
	 */

	// Parsers (this needs to be refactored)
	protected static $requestDefinitionParser;
	protected static $singleton;

	/**
	 * XML Variables
	 */
	private $webapp = null;
	private $uibundle = null;

	/**
	 * Private constructor because this class is a singleton
	 */
	protected function __construct( $webapp, $uibundle ) {
		if ( isset(static::$singleton) ) {
			throw new Exception('Attempted __construct(): An instance of eGloo\Security\RequestValidator\RequestValidator already exists');
		} else {
			$this->webapp = $webapp;
			$this->uibundle = $uibundle;

			// We'll do a conditional check, but for now let's just build an XML parser
			self::$requestDefinitionParser = \XML2ArrayRequestDefinitionParser::getInstance( $this->webapp, $this->uibundle );
		}
	}

	/**
	 * Returns the singleton of this class
	 */
    public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
		if ( !isset(self::$singleton) ) {
			self::$singleton = new self( $webapp, $uibundle );
		}

		return self::$singleton;
    }

	/**
	 * A method to initialize the request info bean
	 *
	 * @param $requestInfoBean the info bean to initialize
	 */
	public function initializeInfoBean($requestInfoBean) {
		return self::$requestDefinitionParser->initializeInfoBean($requestInfoBean);
	}

	/**
	 * This method ensures that this is valid request, by checking arguments 
	 * against the expectant values in the request XML object. if it is a valid 
	 * request, the request processor id needed process this request is populated
	 * in the request info bean.
	 * 
	 * @return true if this is a valid request, or false if it is not
	 */
	public function validateAndProcess($requestInfoBean) {
		return self::$requestDefinitionParser->validateAndProcess($requestInfoBean);
	}

	final private function __clone() {
		throw new Exception('Attempted __clone(): An instance of eGloo\Security\RequestValidator\RequestValidator already exists');
	}

}
