<?php
/**
 * SessionDecorator Class File
 *
 * Needs to be commented
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
 * @copyright 2009 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * Session
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Decorators
 */
class Redirect2ProfileOnLoggedInDecorator extends RequestProcessorDecorator {

   /**
    * do any pre processing here
    */
	protected function requestPreProcessing(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG,
			"Redirect2ProfileOnLoggedInDecorator::requestPreProcessing - Checking for redirect on login", 'Decorators' );

        if ( isset( $_SESSION['LOGGED_IN'] ) && $_SESSION['LOGGED_IN'] === true ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG,
				"Redirect2ProfileOnLoggedInDecorator::requestPreProcessing' .
				' - Logged in: Redirecting to login page", 'Decorators' );

			header( 'Location: /profileID=' . $_SESSION['MAIN_PROFILE_ID'] );
			return false;
        } else {
			// Not logged in, do not redirect
			return true;
		}
   }

   /**
    * do any post processing here
    */
	protected function requestPostProcessing(){
		
	}

  }

?>
