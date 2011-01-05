<?php
/**
 * JavascriptExtendedRawFileRequestProcessor Class File
 *
 * Contains the class definition for the JavascriptExtendedRawFileRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * JavascriptExtendedRawFileRequestProcessor
 * 
 * Handles client requests to retrieve image files from the server
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class JavascriptExtendedRawFileRequestProcessor extends RequestProcessor {

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
	   eGlooLogger::writeLog( eGlooLogger::DEBUG, "JavascriptExtendedRawFileRequestProcessor: Entered processRequest()" );

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateBuilder = new JavascriptBuilder();

		$original_file_name = $file_name = $this->requestInfoBean->getGET( 'javascript_name' );

		$templateVariables = array();

		// Strip out all key/value pairs
		$matches = array();
		preg_match_all('~([0-9a-zA-Z_ -]+):([0-9a-zA-Z_ -]+)/~', $file_name, $matches, PREG_SET_ORDER);

		foreach($matches as $match_set) {
			if ( count($match_set) === 3 && isset($match_set[1]) && isset($match_set[2]) ) {
				$templateVariables[$match_set[1]] = $match_set[2];
			}
		}

		// Strip out all key/value pairs
		$matches = array();

		preg_match('~^[^:]+(/([0-9a-zA-Z_ -]+:[0-9a-zA-Z_ -]+/)+)[^:]+$~', $file_name, $matches);

		if ( count($matches) === 3 && isset($matches[1]) ) {
			$key_value_set_string = $matches[1];
		} else {
			$key_value_set_string = '/';
		}

		foreach($matches as $match_set) {
			if ( count($match_set) === 3 && isset($match_set[1]) && isset($match_set[2]) ) {
				$templateVariables[$match_set[1]] = $match_set[2];
			}
		}

		$file_name = preg_replace('~([0-9a-zA-Z_ -]+):([0-9a-zA-Z_ -]+)/~', '', $file_name);


		$matches = array();
		preg_match('~^([^/]*)?/?([^/]*)$~', $file_name, $matches);
		$cache_to_webroot = false;

		if ( isset($matches[1]) && isset($matches[2]) && trim($matches[2]) === '' ) {
			$file_name = $matches[1];
			$user_agent_hash = '';
			$cache_to_webroot = true;
		} else if ( isset($matches[1]) && isset($matches[2]) ) {
			$file_name = $matches[2];
			$user_agent_hash = $matches[1];

			if ( trim($user_agent_hash) === hash('sha256', $_SERVER['HTTP_USER_AGENT']) ) {
				$cache_to_webroot = true;
			}
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'JavascriptExtendedRawFileRequestProcessor: Looking up javascript ' . $file_name );

		$templateDirector->setTemplateBuilder( $templateBuilder, $file_name ); // <=-- custom request ID

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'JavascriptExtendedRawFileRequestProcessor: Template requested but not found: "' .
					$this->requestInfoBean->getGET( 'javascript_name' ) . '" from user-agent "' . $_SERVER['HTTP_USER_AGENT'] . '"' );
				eGlooHTTPResponse::issueCustom404Response();
			}
		}

		$templateDirector->setTemplateVariables($templateVariables, true);

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "JavascriptExtendedRawFileRequestProcessor: Echoing Response" );

		header('Content-type: text/javascript');

		// TODO buffer output
		echo $output;

		if ( $cache_to_webroot && (eGlooConfiguration::getDeploymentType() == eGlooConfiguration::PRODUCTION ||
			eGlooConfiguration::getUseHotFileJavascriptClustering()) ) {

			StaticContentCacheManager::buildStaticContentCache('xjs', $user_agent_hash . $key_value_set_string, $file_name . '.js', $output );
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'JavascriptExtendedRawFileRequestProcessor: Exiting processRequest()' );
	}

}

