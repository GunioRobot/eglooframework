<?php
/**
 * StyleSheetRequestProcessor Class File
 *
 * Contains the class definition for the StyleSheetRequestProcessor, a
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
 * StyleSheet Request Processor
 * 
 * Handles client requests to retrieve CSS files from the server
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class StyleSheetRequestProcessor extends RequestProcessor {

	/**
	 * Concrete implementation of the abstract RequestProcessor method
	 * processRequest().
	 * 
	 * This method handles processing of the incoming client request.  Its
	 * primary function is to establish the deployment environment (dev, test,
	 * production) and the current localization, and to then parse the correct
	 * template(s) in order to construct and output the requested CSS file.
	 * 
	 * CSS files are maintained as a ports tree to compensate for the varying
	 * degrees of support and bugs across different browser engines, platforms
	 * and revisions or versions of either.	 This tree is defined in the
	 * StyleSheetDispatch.xml and parsed via the StyleSheetDispatcher. The
	 * StyleSheetDispatcher will use the mappings in the XML and the supplied
	 * user-agent to determine the appropriate template to parse and return for
	 * this request.
	 * 
	 * Caching headers are formed to indicate the length of time the cache of
	 * the CSS is valid, as well as setting the vary on user-agent in
	 * order to inform Squid of how it should be issuing cached output to
	 * clients when short-circuiting Apache and the eGloo PHP framework.
	 * 
	 * @access public
	 */
	public function processRequest() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetRequestProcessor: Entered processRequest()" );

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateBuilder = new CSSBuilder();

		$templateDirector->setTemplateBuilder( $templateBuilder );

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'StyleSheetRequestProcessor: Template requested but not found: "' .
					$this->requestInfoBean->getRequestID() . '" from user-agent "' . eGlooHTTPRequest::getUserAgent() . '"' );
				eGlooHTTPResponse::issueCustom404Response();
			}
		}

		$templateDirector->setTemplateVariables(array(), true);

		$output = $templateDirector->processTemplate();
		
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "StyleSheetRequestProcessor: Echoing Response" );
		
		// TODO move header declarations to a decorator
		// TODO find out if we need to set a charset here
		header('Content-type: text/css');		 
		
		// TODO buffer output
		echo $output;
	}

}
