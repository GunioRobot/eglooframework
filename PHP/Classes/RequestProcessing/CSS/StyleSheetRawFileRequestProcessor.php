<?php
/**
 * StyleSheetRawFileRequestProcessor Class File
 *
 * Contains the class definition for the StyleSheetRawFileRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * StyleSheetRawFileRequestProcessor
 * 
 * Handles client requests to retrieve image files from the server
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class StyleSheetRawFileRequestProcessor extends RequestProcessor {

	/**
	 * Concrete implementation of the abstract RequestProcessor method
	 * processRequest().
	 * 
	 * This method handles processing of the incoming client request.  Its
	 * primary function is to establish the deployment environment (dev, test,
	 * production) and the current localization, and to then parse the correct
	 * template(s) in order to output the requested image file.
	 * 
	 * Caching headers are formed to indicate the length of time the cache of
	 * the image file is valid, as well as setting the vary on user-agent in
	 * order to inform Squid of how it should be issuing cached output to
	 * clients when short-circuiting Apache and the eGloo PHP framework.
	 * 
	 * @access public
	 */
	public function processRequest() {
	   eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetRawFileRequestProcessor: Entered processRequest()" );

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateBuilder = new CSSBuilder();

		$file_name = $this->requestInfoBean->getGET( 'css_name' );

		$matches = array();
		preg_match('~^([^/]*)?/?([^/]*)$~', $file_name, $matches);

		$cache_to_webroot = false;

		// TODO fix this so it can handle multi-level deep
		if ( isset($matches[1]) && isset($matches[2]) && trim($matches[2]) === '') {
			$file_name = $matches[1];
			$user_agent_hash = '';
			$cache_to_webroot = true;
		} else if ( isset($matches[1]) && isset($matches[2]) ){
			$file_name = $matches[2];
			$user_agent_hash = $matches[1];

			if ( trim($user_agent_hash) === eGlooHTTPRequest::getUserAgentHash() ) {
				$cache_to_webroot = true;
			}
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'StyleSheetRawFileRequestProcessor: Looking up stylesheet ' . $file_name );

		$templateDirector->setTemplateBuilder( $templateBuilder, $file_name ); // <=-- custom request ID

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'StyleSheetRawFileRequestProcessor: Template requested but not found: "' .
				 	$this->requestInfoBean->getGET( 'css_name' ) . '" from user-agent "' . eGlooHTTPRequest::getUserAgent() . '"' );
				eGlooHTTPResponse::issueRaw404Response();
			}
		}

		$templateDirector->setTemplateVariables(array(), true);

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetRawFileRequestProcessor: Echoing Response" );

		header('Content-type: text/css');

		// TODO buffer output
		echo $output;

		if ( $cache_to_webroot && (eGlooConfiguration::getDeployment() == eGlooConfiguration::PRODUCTION ||
			eGlooConfiguration::getUseHotFileCSSClustering()) ) {

			StaticContentCacheManager::buildStaticContentCache('css', $user_agent_hash, $file_name . '.css', $output );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'StyleSheetRawFileRequestProcessor: Exiting processRequest()' );
	}

}

