<?php
/**
 * ForceNonSSLDecorator Class File
 *
 * Contains the class definition for the ForceNonSSLDecorator
 * 
 * Copyright 2011 eGloo, LLC
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *		  http://www.apache.org/licenses/LICENSE-2.0
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * ForceNonSSLDecorator
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class ForceNonSSLDecorator extends RequestProcessorDecorator {

   /**
	* do any pre processing here
	*/
	protected function requestPreProcessing(){
	
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "ForceNonSSLDecorator::requestPreProcessing - Verifying Non-SSL", 'Decorators' );
		
		if ( !eGlooHTTPRequest::isSSL() ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "ForceNonSSLDecorator::requestPreProcessing - Non-SSL Confirmed", 'Decorators' );
			return true;
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "ForceNonSSLDecorator::requestPreProcessing - SSL Active, Redirecting", 'Decorators' );
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'https://' . eGlooHTTPRequest::getServerName() . eGlooHTTPRequest::getRequestURI(), 'Decorators' );
			header( 'Location: ' . 'http://' . eGlooHTTPRequest::getServerName() . eGlooHTTPRequest::getRequestURI() );
			return false;
		}
		
	
   }

   /**
	* do any post processing here
	*/
	protected function requestPostProcessing(){
		
	}
   
	
}
 
