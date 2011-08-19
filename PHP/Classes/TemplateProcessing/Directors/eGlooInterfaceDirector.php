<?php
/**
 * eGlooInterfaceDirector Class File
 *
 * Contains the class definition for the eGlooInterfaceDirector, a subclass 
 * of the TemplateDirector class.
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
 * @package Template
 * @version 1.0
 */

/**
 * eGloo Interface Director
 * 
 * Provides a definition for an eGloo interface director subclass of
 * the TemplateDirector abstract class.
 *
 * @package Template
 */
class eGlooInterfaceDirector extends TemplateDirector {

	const NO_CACHE		= 0x00;
	const HARD_CACHE	= 0x01;
	const SOFT_CACHE	= 0x02;
	const DYNAMIC_CACHE = 0x04;

	const NO_MODIFIERS	= 0x00;

	private $cacheID = null;
	private $requestInfoBean = null;
	private $templateBuilder = null;
	private $useSmartCaching = false;

	public function __construct( $requestInfoBean ) {
		$this->requestInfoBean = $requestInfoBean;
	}

	public function useSmartCaching( $useSmartCaching = true ) {
		$this->useSmartCaching = $useSmartCaching;
	}

	public function setCachePolicy( $level = self::SOFT_CACHE, $modifiers = self::NO_MODIFIERS ) {
		$this->useSmartCaching = false;

	}

	public function preProcessTemplate() {
		if ($this->useSmartCaching) {
			if (eGlooConfiguration::getDeployment() == eGlooConfiguration::PRODUCTION) {
				$requestClass = $this->requestInfoBean->getRequestClass();
				$requestID = $this->requestInfoBean->getRequestID();
				$cacheID = isset($this->cacheID) ? $this->cacheID : '';

				$this->setHardCacheID($requestClass, $requestID, $cacheID);

				if (!$this->isHardCached()) {
					$this->templateBuilder->setDispatchPath();
					$this->templateBuilder->resolveTemplateRoot();

					if ( $this->cacheID !== null ) {
						$this->templateBuilder->setCacheID( $this->cacheID, $this->ttl );
					}
				}
			} else if (eGlooConfiguration::getDeployment() == eGlooConfiguration::STAGING) {
			} else if (eGlooConfiguration::getDeployment() == eGlooConfiguration::DEVELOPMENT) {
				$this->templateBuilder->setDispatchPath();
				$this->templateBuilder->resolveTemplateRoot();

				if ( $this->cacheID !== null ) {
					$this->templateBuilder->setCacheID( $this->cacheID, $this->ttl );
				}
			}
		} else {
			$this->templateBuilder->setDispatchPath();
			$this->templateBuilder->resolveTemplateRoot();

			if ( $this->cacheID !== null ) {
				$this->templateBuilder->setCacheID( $this->cacheID, $this->ttl );
			}
		}
	}

	public function processTemplate() {
		$retVal = null;

		if ($this->useSmartCaching) {
			if (eGlooConfiguration::getDeployment() == eGlooConfiguration::PRODUCTION) {
				$retVal = $this->templateBuilder->run();
			} else if (eGlooConfiguration::getDeployment() == eGlooConfiguration::STAGING) {
				// TODO
				$retVal = $this->templateBuilder->run();
			} else if (eGlooConfiguration::getDeployment() == eGlooConfiguration::DEVELOPMENT) {
				$retVal = $this->templateBuilder->run();
			}
		} else {
			$retVal = $this->templateBuilder->run();
		}

		return $retVal;
	}

	public function postProcessTemplate() {
		
	}

	public function setTemplateVariables( $tokenArray, $expose_system_variables = false, $system_variable_whitelist = null ) {
		if ($expose_system_variables) {
			// Do an automated thing for this with a foreach loop.	For now, just rewrite base
			if (!isset($tokenArray['rewriteBase'])) {
				if (!$system_variable_whitelist || ($system_variable_whitelist !== null && isset($system_variable_whitelist['rewriteBase']))) {
					$tokenArray['rewriteBase'] = eGlooConfiguration::getRewriteBase();
					$tokenArray['userAgentHash'] = eGlooHTTPRequest::getUserAgentHash();
					$tokenArray['eGlooCSS'] = eGlooConfiguration::getRewriteBase() . 'css/' . eGlooHTTPRequest::getUserAgentHash() . '/';
					$tokenArray['eGlooJS'] = eGlooConfiguration::getRewriteBase() . 'js/' . eGlooHTTPRequest::getUserAgentHash() . '/';
				}
			}
		}

		$this->templateBuilder->setTemplateVariables( $tokenArray );
	}

	public function setContentProcessors( $contentProcessorArray ) {
		$this->templateBuilder->setContentProcessors( $contentProcessorArray );
	}

	public function getTemplateBuilder() {
		
	}
	
	public function isCached() {
		return $this->templateBuilder->isCached();
	}

	public function isHardCached( $requestClass = null, $requestID = null, $cacheID = null ) {
		$requestClass = isset($requestClass) ? $requestClass : $this->requestInfoBean->getRequestClass();
		$requestID = isset($requestID) ? $requestID : $this->requestInfoBean->getRequestID();

		if (!isset($cacheID)) {
			if (!isset($this->cacheID)) {
				// TODO specify caching parameters for the smarty templates
				// This needs to be base64_encoded because the cacheID is sued to create directories
				$userAgentToken = substr( base64_encode( eGlooHTTPRequest::getUserAgent() ), 0, 64 );
				$this->cacheID = $userAgentToken . '|' . $this->requestInfoBean->getRequestID();		
			}

			$cacheID = $this->cacheID;
		}

		return $this->templateBuilder->isHardCached( $requestClass, $requestID, $cacheID );
	}
	
	public function setCacheID( $cacheID = null, $ttl = 3600 ) {
		if ($cacheID === null) {
			// TODO specify caching parameters for the smarty templates
			// This needs to be base64_encoded because the cacheID is sued to create directories
			$userAgentToken = substr( base64_encode( eGlooHTTPRequest::getUserAgent() ), 0, 64 );
			$this->cacheID = $userAgentToken . '|' . $this->requestInfoBean->getRequestID();		
		}

		$this->ttl = $ttl;
	}
	
	public function setHardCacheID( $requestClass = null, $requestID = null, $cacheID = null, $ttl = 3600 ) {
		$requestClass = isset($requestClass) ? $requestClass : $this->requestInfoBean->getRequestClass();
		$requestID = isset($requestID) ? $requestID : $this->requestInfoBean->getRequestID();

		if (!isset($cacheID)) {
			if (!isset($this->cacheID)) {
				$this->setCacheID();
			}

			$cacheID = $this->cacheID;
		}

		$this->templateBuilder->setHardCacheID( $requestClass, $requestID, $cacheID, $ttl );
	}
	
	public function setTemplateBuilder( TemplateBuilder $templateBuilder, $userRequestID = null, $userRequestClass = null ) {
		$this->templateBuilder = $templateBuilder;
		// TODO uncomment this as part of refactoring for hard caching
		$this->templateBuilder->setRequestInfoBean( $this->requestInfoBean );

		if ($userRequestID !== null) {
			$this->templateBuilder->setUserRequestID($userRequestID);
		}

		if ($userRequestClass !== null) {
			$this->templateBuilder->setUserRequestClass($userRequestClass);
		}

		$this->templateBuilder->setTemplateEngine();
	}
	
}
