<?php
/**
 * ExternalMainPageBaseRequestProcessor Class File
 *
 * Contains the class definition for the ExternalMainPageBaseRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @author George Cooper
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * External Main Page Request Processor
 * 
 * Handles client requests to retrieve the external main page (the domain root;
 * e.g. www.egloo.com).
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ExternalMainPageBaseRequestProcessor extends RequestProcessor {

    /**
     * Concrete implementation of the abstract RequestProcessor method
     * processRequest().
     * 
     * This method handles processing of the incoming client request.  Its
     * primary function is to establish the deployment environment (dev, test,
     * production) and the current localization, and to then parse the correct
     * template(s) in order to construct and output the appropriate external
     * main page (the domain root; e.g. www.egloo.com).
     * 
     * @access public
     */
    public function processRequest() {
        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "ExternalMainPageBaseRequestProcessor: Entered processRequest()" );

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateBuilder = new XHTMLBuilder();

		$templateDirector->setTemplateBuilder( $templateBuilder );

        // TODO Mark a difference between hard caching and soft caching
        // Soft caching is basically caching the dispatch mappings, but still requiring traversal to pull the correct template
        // Hard caching takes as little information as is needed in one go and shoots straight to the template cache,
        // avoiding the dispatch mapper entirely if possible.  The former is good for development work, the latter
        // is better suited for production

		$templateDirector->setHardCacheID();
		$templateDirector->useSmartCaching();

		// if ( !$templateDirector->isHardCached( $this->requestInfoBean->getRequestID(), $cacheID ) ) {
			$templateDirector->preProcessTemplate();

			$templateVariables['svnVersion'] = '∞';
			$templateVariables['app'] = eGlooConfiguration::getApplicationName();
			$templateVariables['bundle'] = eGlooConfiguration::getUIBundleName();

			$templateDirector->setTemplateVariables( $templateVariables );            
		// } else {
		// 	$templateDirector->setHardCacheID( $this->requestInfoBean->getRequestID(), $cacheID );
		// }

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::$DEBUG, "ExternalMainPageBaseRequestProcessor: Echoing Response" );

		// TODO move header declarations to a decorator
		header("Content-type: text/html; charset=UTF-8");

		// TODO buffer output
		echo $output;        

        eGlooLogger::writeLog( eGlooLogger::$DEBUG, "ExternalMainPageBaseRequestProcessor: Exiting processRequest()" );
    }

}
?>