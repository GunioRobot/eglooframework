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

	/**
	 * See if we can do coolness here with late static binding
	 */
    abstract public static function getInstance( $webapp = "Default", $uibundle = "Default" );

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

}
