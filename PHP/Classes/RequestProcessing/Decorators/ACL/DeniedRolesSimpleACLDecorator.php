<?php
/**
 * PermittedRolesSimpleACLDecorator Class File
 *
 * Needs to be commented
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
 * @version 1.0
 */

/**
 * PermittedRolesSimpleACLDecorator
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Decorators
 */
class DeniedRolesSimpleACLDecorator extends SimpleACLDecorator {

	protected $_deniedRoles;

   /**
    * do any pre processing here
    */
	protected function requestPreProcessing() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "PermittedRolesSimpleACLDecorator::requestPreProcessing", 'Decorators' );
		$retVal = true;

		$retVal = $this->__checkDeniedRoles();

		if (!$retVal) {
			$this->__actOnAccessDenied();
		}

		return $retVal;
	}

   /**
    * do any post processing here
    */
	protected function requestPostProcessing(){
		
	}

	protected function __checkDeniedRoles() {
		$retVal = true;

		if (isset($_SESSION['egACLRole']) && in_array($_SESSION['egACLRole'], $this->_deniedRoles)) {
			$retVal = false;
		}

		return $retVal;
	}

	protected function __actOnAccessDenied() {
		
	}

}

