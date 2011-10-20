<?php
namespace eGloo\IOStream;

use \eGloo;
use \eGloo\Configuration as Configuration;
use \eGloo\Utility\Logger as Logger;

use \eGloo\HTTP\Response as eGlooHTTPResponse;
use \RequestInfoBean as RequestInfoBean;
use \TemplateDirectorFactory as TemplateDirectorFactory;
use \XHTMLBuilder as XHTMLBuilder;

use \ErrorException as ErrorException;
use \Exception as Exception;

/**
 * eGloo\IOStream\Response Class File
 *
 * $file_block_description
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
 * @category System
 * @package IOStream
 * @version 1.0
 */

/**
 * eGloo\IOStream\Response
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package IOStream
 */
class Response {

	public static function getXHTML( $templateVariables = null, $dispatchClass = null, $dispatchID = null ) {
		Logger::writeLog( Logger::DEBUG, "eGloo\IOStream\Response: Entered getXHTML()" );

		$retVal = null;

		$requestInfoBean = RequestInfoBean::getInstance();
		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $requestInfoBean );

		if ( !$dispatchClass ) {
			$dispatchClass = $requestInfoBean->getRequestClass();
		}

		if ( !$dispatchID ) {
			$dispatchID = $requestInfoBean->getRequestID();
		}

		$templateDirector->setTemplateBuilder( new XHTMLBuilder(), $dispatchID, $dispatchClass );

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			if ( Configuration::getDeployment() === Configuration::DEVELOPMENT &&
				 Logger::getLoggingLevel() === Logger::DEVELOPMENT) {
				throw $e;
			} else {
				Logger::writeLog( Logger::WARN, 'eGloo\IOStream\Response: Template requested for RequestClass/RequestID "' .
					$dispatchClass . '/' . $dispatchID . '" but not found.' );
			}
		}

		if ( !$templateVariables || !is_array($templateVariables) ) {
			$templateVariables = array();
		}

		$templateDirector->setTemplateVariables( $templateVariables, true );            
		$output = $templateDirector->processTemplate();

		$retVal = $output;

		Logger::writeLog( Logger::DEBUG, "eGloo\IOStream\Response: Exiting getXHTML()" );

		return $retVal;
	}

	public static function outputCSS() {
		
	}

	public static function outputCSV() {
		
	}

	public static function outputJavascript() {
		
	}

	public static function outputXHTML( $templateVariables = null, $dispatchClass = null, $dispatchID = null ) {
		Logger::writeLog( Logger::DEBUG, "eGloo\IOStream\Response: Entered outputXHTML()" );

		$requestInfoBean = RequestInfoBean::getInstance();

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $requestInfoBean );
		
		if ( !$dispatchClass ) {
			$dispatchClass = $requestInfoBean->getRequestClass();
		}

		if ( !$dispatchID ) {
			$dispatchID = $requestInfoBean->getRequestID();
		}

		$templateDirector->setTemplateBuilder( new XHTMLBuilder(), $dispatchID, $dispatchClass );

		try {
			$templateDirector->preProcessTemplate();
		} catch ( ErrorException $e ) {
			if ( Configuration::getDeployment() === Configuration::DEVELOPMENT &&
				 Logger::getLoggingLevel() === Logger::DEVELOPMENT) {
				throw $e;
			} else {
				Logger::writeLog( Logger::WARN, 'eGloo\IOStream\Response: Template requested for RequestClass/RequestID "' .
					$dispatchClass . '/' . $dispatchID . '" but not found.' );
				eGlooHTTPResponse::issueCustom404Response();
			}
		}

		if ( !$templateVariables || !is_array($templateVariables) ) {
			$templateVariables = array();
		}

		$templateDirector->setTemplateVariables( $templateVariables, true );            

		$output = $templateDirector->processTemplate();

		Logger::writeLog( Logger::DEBUG, "eGloo\IOStream\Response: Echoing Response" );

		// TODO move header declarations to a decorator
		header("Content-type: text/html; charset=UTF-8");

		// TODO buffer output
		echo $output;        

        Logger::writeLog( Logger::DEBUG, "eGloo\IOStream\Response: Exiting outputXHTML()" );

	}

	public static function outputXML() {
		
	}

}

