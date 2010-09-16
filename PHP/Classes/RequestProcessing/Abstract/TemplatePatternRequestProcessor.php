<?php
/**
 * XHTMLTemplatePatternRequestProcessor Class File
 *
 * $file_block_description
 * 
 * Copyright 2010 eGloo, LLC
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * XHTMLTemplatePatternRequestProcessor
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class TemplatePatternRequestProcessor extends RequestProcessor {

	/* Protected Data Members */
	protected $_templateBuilder = null;
	protected $_templateVariables = array();
	protected $_useSystemVariables = true;

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
		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Entered processRequest()" );

		$this->setTemplateBuilder();

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateDirector->setTemplateBuilder( $this->getTemplateBuilder() );
		$templateDirector->preProcessTemplate();

		$this->populateTemplateVariables();

		$templateDirector->setTemplateVariables( $this->getTemplateVariables(), $this->useSystemVariables() );            

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Echoing Response" );

		$this->setOutputHeaders();

		echo $output;        

		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Exiting processRequest()" );
	}

	// Defaults to text/html - override as needed
	protected function setOutputHeaders() {
		header("Content-type: text/html; charset=UTF-8");
	}

	protected function getTemplateBuilder() {
		return $this->_templateBuilder;
	}

	// Defaults to XHTMLBuilder - override as needed
	protected function setTemplateBuilder() {
		$this->_templateBuilder = new XHTMLBuilder();
	}

	protected function getTemplateVariable( $key ) {
		return $this->_templateVariables[$key];
	}

	protected function getTemplateVariables() {
		return $this->_templateVariables;
	}

	protected function setTemplateVariable( $key, $value ) {
		$this->_templateVariables[$key] = $value;
	}

	protected function setTemplateVariables( $templateVariables ) {
		$this->_templateVariables = $templateVariables;
	}

	protected function setTemplateVariablesByMerge( $templateVariables ) {
		$this->_templateVariables = array_merge($this->_templateVariables, $templateVariables);
	}

	protected function useSystemVariables() {
		return $this->_useSystemVariables;
	}

	protected function populateTemplateVariables() {
		// Override this to make changes to $this->_templateVariables
	}

}

