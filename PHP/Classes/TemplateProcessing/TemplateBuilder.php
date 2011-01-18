<?php
/**
 * TemplateBuilder Class File
 *
 * Contains the class definition for the TemplateBuilder, an abstract 
 * builder class for template parsing.
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

	protected function processEngineFetchException( $e, $dispatchPath, $cacheID ) {
		$template_engine_class = get_class($this->templateEngine);
		$error_message = 'Template Engine of type "' . $template_engine_class . '" ';

		$matches = array();

		// TODO This should probably catch against the known engine messages in a different way.  Branching on type?
		// Right now this is assuming DefaultTemplateEngine like whoa
		if ( preg_match('~.*the \$compile_dir \'(.*)\' does not exist, or is not a directory.*~', $e->getMessage(), $matches ) ) {
			if (count($matches) >= 1) {
				try {
					$mode = 0777;
					$recursive = true;

					mkdir( $matches[1], $mode, $recursive );

					$retVal = $this->__fetch( $dispatchPath, $cacheID );
				} catch (Exception $e){
					throw new FailedWriteTemplateCompileDirectoryException( $e->getMessage() );
				}
			}
		} else if ( preg_match('~.*the \$cache_dir \'(.*)\' does not exist, or is not a directory.*~', $e->getMessage(), $matches ) ) {
			if (count($matches) >= 1) {
				try {
					$mode = 0777;
					$recursive = true;

					mkdir( $matches[1], $mode, $recursive );

					$retVal = $this->__fetch( $dispatchPath, $cacheID );
				} catch (Exception $e){
					throw new FailedWriteTemplateCacheDirectoryException( $e->getMessage() );
				}
			}
		} else if ( preg_match('~.*Unable to load template file.*~', $e->getMessage(), $matches ) ) {
			if (count($matches) >= 1) {
				$error_message .= 'failed to load template file with cache ID "' . $this->cacheID  .
					'" at path: ' . "\n" . $this->dispatchPath;

				throw new DefaultTemplateEngineFailedReadTemplateException( $error_message );
			}
		} else if ( preg_match('~.*Undefined Smarty variable.*~', $e->getMessage(), $matches ) ) {
			if (count($matches) >= 1) {
				$error_message .= 'failed processing template file with cache ID "' . $this->cacheID  . '" at path: ' . "\n" .
					$this->dispatchPath . "\n\n";
				$error_message .= $template_engine_class . ' error: ' . $e->getMessage();

				throw new DefaultTemplateEngineUndefinedVariableException( $error_message );
			}
		} else if ( preg_match('~.*Syntax Error in template.*~', $e->getMessage(), $matches ) ) {
			if (count($matches) >= 1) {
				$error_message .= 'failed processing template file with cache ID "' . $this->cacheID  . '" at path: ' . "\n" .
					$this->dispatchPath . "\n\n";
				$error_message .= $template_engine_class . ' error: ' . $e->getMessage();

				throw new DefaultTemplateEngineSyntaxErrorException( $error_message );
			}
		} else {
			$error_message .= 'failed processing template file with cache ID "' . $this->cacheID  . '" at path: ' . "\n" .
				$this->dispatchPath . "\n\n";
			$error_message .= $template_engine_class . ' error: ' . $e->getMessage();

			throw new DefaultTemplateEngineException( $error_message );
		}

	}

}
