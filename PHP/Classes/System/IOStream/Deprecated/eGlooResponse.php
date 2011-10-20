<?php
/**
 * eGlooResponse Class File
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
 * eGlooResponse
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package IOStream
 */
class eGlooResponse {

	public static function getXHTML( $templateVariables = null, $dispatchClass = null, $dispatchID = null ) {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "eGlooResponse: Entered getXHTML()" );

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
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'eGlooResponse: Template requested for RequestClass/RequestID "' .
					$dispatchClass . '/' . $dispatchID . '" but not found.' );
			}
		}

		if ( !$templateVariables || !is_array($templateVariables) ) {
			$templateVariables = array();
		}

		$templateDirector->setTemplateVariables( $templateVariables, true );            
		$output = $templateDirector->processTemplate();

		$retVal = $output;

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "eGlooResponse: Exiting getXHTML()" );

		return $retVal;
	}

	public static function outputCSS() {
		
	}

	public static function outputCSV() {
		
	}

	public static function outputJavascript() {
		
	}

	public static function outputXHTML( $templateVariables = null, $dispatchClass = null, $dispatchID = null ) {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "eGlooResponse: Entered outputXHTML()" );

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
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'eGlooResponse: Template requested for RequestClass/RequestID "' .
					$dispatchClass . '/' . $dispatchID . '" but not found.' );
				eGlooHTTPResponse::issueCustom404Response();
			}
		}

		if ( !$templateVariables || !is_array($templateVariables) ) {
			$templateVariables = array();
		}

		$templateDirector->setTemplateVariables( $templateVariables, true );            

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "eGlooResponse: Echoing Response" );

		// TODO move header declarations to a decorator
		header("Content-type: text/html; charset=UTF-8");

		// TODO buffer output
		echo $output;        

        eGlooLogger::writeLog( eGlooLogger::DEBUG, "eGlooResponse: Exiting outputXHTML()" );

	}

	public static function outputXML() {
		
	}

}

deprecate( __FILE__, '\eGloo\IOStream\Response' );
