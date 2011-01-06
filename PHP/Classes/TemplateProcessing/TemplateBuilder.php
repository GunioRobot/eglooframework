<?php
/**
 * TemplateBuilder Class File
 *
 * Contains the class definition for the TemplateBuilder, an abstract 
 * builder class for template parsing.
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Template
 * @version 1.0
 */

/**
 * Template Builder
 * 
 * Provides an abstract class definition for template builder classes to
 * inherit from.
 *
 * @package Template
 * @subpackage Template Building
 */
abstract class TemplateBuilder {

	protected $application = '';
	protected $dispatchPath = null;
	protected $interfaceBundle = '';
	protected $templateEngine = null;
	protected $ttl = null;
	protected $processTemplate = true;
	protected $userRequestID = null;
	protected $userRequestClass = null;

	public function setApplication( $application ) {
		$this->application = $application;
	}
	
	public function setInterfaceBundle( $interfaceBundle ) {
		$this->interfaceBundle = $interfaceBundle;
	}

	public function setUserRequestID( $userRequestID ) {
		$this->userRequestID = $userRequestID;
	}

	public function getUserRequestID() {
		return $this->userRequestID;
	}

	public function setUserRequestClass( $userRequestClass ) {
		$this->userRequestClass = $userRequestClass;
	}

	public function getUserRequestClass() {
		return $this->userRequestClass;
	}

	abstract public function setDispatchPath();
	
	public function getDispatchPath() {
		return $this->dispatchPath;
	}
	
	abstract public function setTemplateEngine();

	abstract public function isHardCached( $requestClass, $requestID, $cacheID );

	abstract public function setHardCacheID( $requestClass, $requestID, $cacheID, $ttl = 3600 );

	public function resolveTemplateRoot() {
		$matches = array();

		if (preg_match('~^Core/([a-zA-Z0-9]+)/~', $this->dispatchPath, $matches)) {
			$this->templateEngine->useApplicationTemplates(false);
			$this->templateEngine->useFrameworkTemplates(true, 'Core', $matches[1]);
			$this->dispatchPath = preg_replace('~^Core/([a-zA-Z0-9]+)/~', '', $this->dispatchPath);
		} else if (preg_match('~^Common/([a-zA-Z0-9]+)/~', $this->dispatchPath, $matches)) {
			$this->templateEngine->useApplicationTemplates(false);
			$this->templateEngine->useFrameworkTemplates(true, 'Common', $matches[1]);
			$this->dispatchPath = preg_replace('~^Common/([a-zA-Z0-9]+)/~', '', $this->dispatchPath);
		} else if (preg_match('~^Local/([a-zA-Z0-9]+)/~', $this->dispatchPath, $matches)) {
			$this->templateEngine->useApplicationTemplates(false);
			$this->templateEngine->useFrameworkTemplates(true, 'Local', $matches[1]);
			$this->dispatchPath = preg_replace('~^Local/([a-zA-Z0-9]+)/~', '', $this->dispatchPath);
		} else if (preg_match('~^Application/([a-zA-Z0-9]+)/~', $this->dispatchPath, $matches)) {
			$this->templateEngine->useApplicationTemplates(false);
			$this->templateEngine->useApplicationCommonTemplates(true);
			$this->templateEngine->useFrameworkTemplates(true);
			$this->dispatchPath = preg_replace('~^Application/([a-zA-Z0-9]+)/~', '', $this->dispatchPath);
		} else {
			// $this->templateEngine->useApplicationTemplates(true, eGlooConfiguration::getUIBundleName());
			// $this->templateEngine->useApplicationCommonTemplates(true);
			// $this->templateEngine->useFrameworkTemplates(true);
		}

	}

}
