<?php
/**
 * ConfigureApplicationCoreeGlooRequestProcessor Class File
 *
 * Contains the class definition for the ConfigureApplicationCoreeGlooRequestProcessor, a
 * subclass of the RequestProcessor abstract class.
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * Configure Application Core eGloo Request Processor
 * 
 * Handles client requests to retrieve the external main page (the domain root;
 * e.g. www.egloo.com).
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ConfigureApplicationCoreeGlooRequestProcessor extends RequestProcessor {

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
        eGlooLogger::writeLog( eGlooLogger::DEBUG, "ConfigureApplicationCoreeGlooRequestProcessor: Entered processRequest()" );

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateBuilder = new XHTMLBuilder();

		$templateDirector->setTemplateBuilder( $templateBuilder );

		$templateDirector->preProcessTemplate();

		$applications = $this->getApplications(eGlooConfiguration::getApplicationsPath());

		// Sort these by name, case insensitive
		uksort($applications, 'strnatcasecmp');


		$templateVariables['app'] = eGlooConfiguration::getApplicationName();
		$templateVariables['bundle'] = eGlooConfiguration::getUIBundleName();
		$templateVariables['applications'] = $applications;

		$templateDirector->setTemplateVariables( $templateVariables );            
		// echo_r(file('.htaccess'));
		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "ConfigureApplicationCoreeGlooRequestProcessor: Echoing Response" );

		// TODO move header declarations to a decorator
		header("Content-type: text/html; charset=UTF-8");

		// TODO buffer output
		echo $output;

        eGlooLogger::writeLog( eGlooLogger::DEBUG, "ConfigureApplicationCoreeGlooRequestProcessor: Exiting processRequest()" );
    }

	private function getApplications( $applicationsPath ) {
		$paths = array();

		if ( file_exists( $applicationsPath ) && is_dir( $applicationsPath ) ) {
			$it = new RecursiveDirectoryIterator( $applicationsPath );

			foreach ($it as $i) {
				if ($i->isLink()) {
					$paths = array_merge($this->getApplications($i->getRealPath()), $paths);
				} else if ($i->isDir() && strpos($i->getFilename(), '.gloo')) {
					$application_name = preg_replace('~\.gloo~', '', $i->getFilename());
					$paths[$application_name] = array('application_name' => $application_name, 'application_path' => $i->getRealPath());
				} else if ($i->isDir()) {
					$paths += (array) $this->getApplications($i->getRealPath());
				}
			}
		}

		return $paths;
	}
}


