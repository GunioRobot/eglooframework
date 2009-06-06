<?php
/**
 * VerifyLoggedInDecorator Class File
 *
 * Needs to be commented
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
 * @version 1.0
 */

/**
 * VerifyLoggedInDecorator
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Decorators
 */
class VerifyLoggedInDecorator extends RequestProcessorDecorator {

   /**
    * do any pre processing here
    */
	protected function requestPreProcessing(){
   	
		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "VerifyLoggedInDecorator::requestPreProcessing - Verifying logged in status", 'Decorators' );
		
		if ( isset( $_SESSION['LOGGED_IN'] ) && $_SESSION['LOGGED_IN'] === true ) {
	   		return true;
        } else {
            header( 'Location: /' );
            return false;
        }
		
   	
   }

   /**
    * do any post processing here
    */
	protected function requestPostProcessing(){
		
	}
   
    
  }
 
 
?>