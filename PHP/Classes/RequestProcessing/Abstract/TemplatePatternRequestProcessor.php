<?php
/**
 * XHTMLTemplatePatternRequestProcessor Class File
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
	protected $_requestClassOverride = null;
	protected $_requestIDOverride = null;

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

		$this->preProcessing();
		$this->setTemplateBuilder();
		$this->setCustomDispatch();

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateDirector->setTemplateBuilder( $this->getTemplateBuilder(), $this->_requestIDOverride, $this->_requestClassOverride );

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			// TODO Error checking / branching should be more fine-tuned here
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {

				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'TemplatePatternRequestProcessor: Template requested for RequestClass/RequestID "' .
					$this->requestInfoBean->getRequestClass() . '/' . $this->requestInfoBean->getRequestID() . '" but not found.' );

				try {
					// Let's make sure we clean up the output buffer so the custom 404 comes out clean
					if ( ob_get_level() ) {
						ob_clean();
					}

					// Try to issue the custom 404
					eGlooHTTPResponse::issueCustom404Response();
				} catch (Exception $e) {
					eGlooLogger::writeLog( eGlooLogger::WARN, 'TemplatePatternRequestProcessor: Custom 404 template requested for RequestClass/RequestID "' .
						$this->requestInfoBean->getRequestClass() . '/' . $this->requestInfoBean->getRequestID() . '" but not found.' );

					// Let's make sure we clean up the output buffer so the raw 404 comes out clean
					if ( ob_get_level() ) {
						ob_clean();
					}

					// TODO this is rather primitive, even as a raw fallback.  Make this better
					eGlooHTTPResponse::issueRaw404Response( 'We\'re sorry, but the page you requested was not found' );
				}
			}
		}

		$this->populateTemplateVariables();

		$templateDirector->setTemplateVariables( $this->getTemplateVariables(), $this->useSystemVariables() );

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Echoing Response" );

		if ($this->decoratorInfoBean->issetNamespace('ManagedOutput')) {
			$this->decoratorInfoBean->setValue('Output', $output, 'ManagedOutput');
		} else {
			$this->setOutputHeaders();
			echo $output;
		}

		$this->postProcessing();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Exiting processRequest()" );
	}

	public function processErrorRequest() {
		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Entered processErrorRequest()" );

		$this->preProcessing();
		$this->setTemplateBuilder();
		$this->setCustomDispatch();

		$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
		$templateDirector->setTemplateBuilder( $this->getTemplateBuilder() );

		try {
			$templateDirector->preProcessTemplate();
		} catch (ErrorException $e) {
			if ( eGlooConfiguration::getDeployment() === eGlooConfiguration::DEVELOPMENT &&
				 eGlooLogger::getLoggingLevel() === eGlooLogger::DEVELOPMENT) {
				throw $e;
			} else {
				eGlooLogger::writeLog( eGlooLogger::WARN, 'TemplatePatternRequestProcessor: Template requested for RequestClass/RequestID "' .
					$this->requestInfoBean->getRequestClass() . '/' . $this->requestInfoBean->getRequestID() . '" but not found.' );
				eGlooHTTPResponse::issueCustom404Response();
			}
		}

		$this->populateErrorTemplateVariables();

		$templateDirector->setTemplateVariables( $this->getTemplateVariables(), $this->useSystemVariables() );

		$output = $templateDirector->processTemplate();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Echoing Response" );

		if ($this->decoratorInfoBean->issetNamespace('ManagedOutput')) {
			$this->decoratorInfoBean->setValue('Output', $output, 'ManagedOutput');
		} else {
			$this->setOutputHeaders();
			echo $output;
		}

		$this->postProcessing();

		eGlooLogger::writeLog( eGlooLogger::DEBUG, static::getClass() . ": Exiting processErrorRequest()" );
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
		if ($this->decoratorInfoBean->issetNamespace('ManagedOutput')) {
			$format = $this->decoratorInfoBean->getValue('Format', 'ManagedOutput');

			switch( $format ) {
				case 'csv' :
					$this->_templateBuilder = new CSVBuilder();
					break;
				case 'html' :
					$this->_templateBuilder = new XHTMLBuilder();
					break;
				case 'json' :
					$this->_templateBuilder = new JavascriptBuilder();
					break;
				case 'svg' :
					break;
				case 'xhtml' :
					$this->_templateBuilder = new XHTMLBuilder();
					break;
				case 'xml' :
					$this->_templateBuilder = new XMLBuilder();
					break;
				default :
					$this->_templateBuilder = new XHTMLBuilder();
					break;
			}
		} else {
			$this->_templateBuilder = new XHTMLBuilder();
		}

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

	protected function postProcessing() {
		// Override this to make changes before we work on template building
	}

	protected function preProcessing() {
		// Override this to make changes after we work on template building
	}

	protected function populateTemplateVariables() {
		// Override this to make changes to $this->_templateVariables
	}

	protected function populateErrorTemplateVariables() {
		// Override this to make changes to $this->_templateVariables
	}

	protected function setCustomDispatch() {
		// Override this to make changes to dispatch
	}

	protected function setRequestClassOverride( $requestClassOverride ) {
		$this->_requestClassOverride = $requestClassOverride;
	}

	protected function setRequestIDOverride( $requestIDOverride ) {
		$this->_requestIDOverride = $requestIDOverride;
	}

}

