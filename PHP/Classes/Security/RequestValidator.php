<?php
/**
 * RequestValidator Class File
 *
 * Contains the class definition for the request validator.
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

	// Parsers (this needs to be refactored)
	private static $eglooRequestDefinitionParser = null;
	private static $xmlRequestDefinitionParser = null;
	private static $arrayRequestDefinitionParser = null;
	
	private static $singletonRequestValidator;

	/**
	 * XML Variables
	 */

	private $webapp = null;
	private $uibundle = null;

	/**
	 * Private constructor because this class is a singleton
	 */
	private function __construct( $webapp, $uibundle ) {
		$this->webapp = $webapp;
		$this->uibundle = $uibundle;

		// We'll do a conditional check, but for now let's just build an XML parser
		// self::$xmlRequestDefinitionParser = XMLRequestDefinitionParser::getInstance($this->webapp, $this->uibundle);
		self::$xmlRequestDefinitionParser = XML2ArrayRequestDefinitionParser::getInstance($this->webapp, $this->uibundle);
	}

	/**
	 * Returns the singleton of this class
	 */
    public static function getInstance( $webapp = "Default", $uibundle = "Default" ) {
		if ( !isset(self::$singletonRequestValidator) ) {
			self::$singletonRequestValidator = new RequestValidator( $webapp, $uibundle );
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
		return self::$xmlRequestDefinitionParser->validateAndProcess($requestInfoBean);
	}

}
