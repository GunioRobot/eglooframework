<?php
namespace eGloo\HTTP;

use \eGloo;
use \eGloo\Configuration as Configuration;
use \eGloo\Utility\Logger as Logger;

use \RequestInfoBean as RequestInfoBean;
use \TemplateDirectorFactory as TemplateDirectorFactory;
use \XHTMLBuilder as XHTMLBuilder;

use \ErrorException as ErrorException;
use \Exception as Exception;

/**
 * eGloo\HTTP\Response Class File
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
 * @package HTTP
 * @subpackage REST
 * @version 1.0
 */

/**
 * eGloo\HTTP\Response
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package HTTP
 * @subpackage REST
 */
class Response {

	// Constants
	const DISPATCH_CLASS = 'egCustomHTTPResponse';

	// Response Code Constants
	const HTTP_RESPONSE_404 = '404';

	public static function issueRaw404Response( $output = null ) {
		self::resetHeaders();

		// TODO branch on different 404 type if using FastCGI: header("Status: 404 Not Found"); or header("HTTP/1.0 404 Not Found")
		header('HTTP/1.0 404 Not Found', true, 404);

		if ( $output !== null ) {
			echo $output;
		}

		exit;
	}

	public static function issueCustom404Response( $templateVariables = null, $dispatchClass = self::DISPATCH_CLASS, $dispatchID = self::HTTP_RESPONSE_404 ) {
		Logger::writeLog( Logger::DEBUG, "eGloo\HTTP\Response: Entered issueCustom404Response()" );

		// Don't call eGlooResponse::outputXHTML from this context because it might invoke this function itself and cause an infinite loop
		// This is considered, effectively, a more primitive path, thus it must use the fallback pattern
		$templateDirector = TemplateDirectorFactory::getTemplateDirector( RequestInfoBean::getInstance() );
		$templateDirector->setTemplateBuilder( new XHTMLBuilder(), $dispatchID, $dispatchClass );

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			if ( Configuration::getDeployment() === Configuration::DEVELOPMENT &&
				 Logger::getLoggingLevel() === Logger::DEVELOPMENT) {
				throw $e;
			} else {
				Logger::writeLog( Logger::WARN, 'eGloo\HTTP\Response: Template dispatch requested for ' .
					self::DISPATCH_CLASS . '/' . self::HTTP_RESPONSE_404 . ' but not found.' );
				self::issueRaw404Response();
			}
		}

		if ( !$templateVariables || !is_array($templateVariables) ) {
			$templateVariables = array();
		}

		$templateDirector->setTemplateVariables( $templateVariables, true );

		try {
			$output = $templateDirector->processTemplate();
		} catch (ErrorException $e) {
			$errorMessage = 'eGloo\HTTP\Response: Error processing template for ' . self::DISPATCH_CLASS . '/' .
				self::HTTP_RESPONSE_404 . ': ' . $e->getMessage();

			if ( Configuration::getDeployment() === Configuration::DEVELOPMENT &&
				 Logger::getLoggingLevel() === Logger::DEVELOPMENT) {
				throw new ErrorException( $errorMessage );
			} else {
				Logger::writeLog( Logger::WARN, $errorMessage );
				self::issueRaw404Response();
			}
		}

		// Reset headers on this request
		self::resetHeaders();

		header("Content-type: text/html; charset=UTF-8");
		header('HTTP/1.0 404 Not Found', true, 404);

		// TODO buffer output
		echo $output;        

        Logger::writeLog( Logger::DEBUG, "eGloo\HTTP\Response: Exiting issueCustom404Response()" );
	}

	public static function processCustom404Request( $requestProcessorID = null ) {
		Logger::writeLog( Logger::DEBUG, "eGloo\HTTP\Response: Entered processCustom404Request()" );

		// Reset headers on this request
		self::resetHeaders();

		header("Content-type: text/html; charset=UTF-8");
		header('HTTP/1.0 404 Not Found', true, 404);

		$requestProcessor = new $requestProcessorID;
		$requestProcessor->processRequest();

        Logger::writeLog( Logger::DEBUG, "eGloo\HTTP\Response: Exiting processCustom404Request()" );
	}

	public static function resetHeaders() {
		header_remove('Cache-Control');
		header_remove('Content-type');
		header_remove('Expires');
		header_remove('Pragma');
	}

}

