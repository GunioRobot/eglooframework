<?php
/**
 * SessionDecorator Class File
 *
 * Needs to be commented
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
class SessionDecorator extends RequestProcessorDecorator {

   /**
    * do any pre processing here
    */
	protected function requestPreProcessing(){
		eGlooLogger::writeLog( eGlooLogger::DEBUG,
			"SessionDecorator::requestPreProcessing - Starting session", 'Decorators' );

	    // Initialize the session object
	    $sessionHandler = new SessionHandler();

		// Start the session
	    session_start();

	    //This is to get IE to accept cookies with its new security model
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

		// Set the cookie
		setcookie(session_name(), session_id(), time()+60*60*24*30, '/');

		// No problems...
		return true;
   }

   /**
    * do any post processing here
    */
	protected function requestPostProcessing(){
		
	}

  }

?>
