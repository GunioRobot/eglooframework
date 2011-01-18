<?php
/**
 * JavascriptBuilder Class File
 *
 * Contains the class definition for the JavascriptBuilder, a concrete
 * subclass that inherits from the TemplateBuilder class. 
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
 * JavascriptBuilder
 * 
 * Provides a definition for a JavascriptBuilder subclass of the
 * TemplateBuilder abstract class.
 *
 * @package Template
 * @subpackage Template Building
 */
class JavascriptBuilder extends TemplateBuilder {
	
	protected $cacheID = null;
	protected $hardCacheID = null;
	protected $deployment = null;
	protected $requestInfoBean = null;
	protected $templateVariables = null;
	protected $output = null;
	protected $isHardCached = false;

	public function __construct() {
		$this->processTemplate = false;
	}
		
	public function setRequestInfoBean( $requestInfoBean ) {
		$this->requestInfoBean = $requestInfoBean;
	}
	
	public function setTemplateVariables( $templateVariables ) {
		$this->templateVariables = $templateVariables;
		foreach( $templateVariables as $key => $value) $this->templateEngine->assign( $key, $value );
	}
	
	public function setCacheID( $cacheID ) {
		$this->templateEngine->cache_handler_func = 'smarty_cache_memcache';
		$this->templateEngine->caching = true;
		
		$this->cacheID = $cacheID;
	}

	public function setHardCacheID( $requestClass, $requestID, $cacheID, $ttl = 3600 ) {
		$this->hardCacheID = '|' . $requestClass . '|' . $requestID . '|' . $cacheID . '|';
		$this->ttl;
	}
	
	public function isCached() {
		return $this->templateEngine->is_cached( $this->dispatchPath, $this->cacheID );
	}

	public function isHardCached( $requestClass, $requestID, $cacheID ) {
		$retVal = false;

		if ($this->isHardCached && $this->output != null) {
			$retVal = true;
		} else {
			$cacheGateway = CacheGateway::getCacheGateway();

			$retVal = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $this->hardCacheID, 'HardCache');

			if ( $retVal != null ) {
				$this->output = $retVal;
				$this->isHardCached = true;

				$retVal = $this->isHardCached;
			} else {
				$this->output = null;
				$retVal = false;
				$this->isHardCached = false;
			}
		}

		return $retVal;
	}
	
	public function setDispatchPath() {
		$templateDispatcher =
			JavascriptXML2ArrayDispatcher::getInstance( $this->requestInfoBean->getApplication(), $this->requestInfoBean->getInterfaceBundle() );


		$this->dispatchPath = $templateDispatcher->dispatch( $this->requestInfoBean, $this->userRequestID );
		$this->processTemplate = $templateDispatcher->getProcessTemplate();
	}
	
	public function setTemplateEngine() {
		$this->templateEngine = new JavascriptDefaultTemplateEngine( $this->requestInfoBean->getInterfaceBundle(), 'US', 'en' );
	}
	
	public function run() {
		$retVal = null;

		if (isset($this->hardCacheID) && $this->isHardCached) {
			$retVal = $this->output;
		} else if (isset($this->hardCacheID) && !$this->isHardCached){
			$retVal = $this->__fetch( $this->dispatchPath, $this->cacheID );

			$cacheGateway = CacheGateway::getCacheGateway();
			$cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $this->hardCacheID, $retVal, 'HardCache', $this->ttl);
		} else {
			$retVal = $this->__fetch( $this->dispatchPath, $this->cacheID );
		}

		return $retVal;
	}

	protected function __fetch($dispatchPath, $cacheID) {
		$retVal = null;

		try {
			if ($this->processTemplate) {
				$retVal = $this->templateEngine->fetch( $dispatchPath, $cacheID );
			} else {
				foreach($this->templateEngine->getTemplatePaths() as $path) {
					if (file_exists($path . $dispatchPath)) {
						$retVal = file_get_contents($path . $dispatchPath);
						break;
					}
				}

				if ( !$retVal ) {
					throw new JavascriptBuilderException( 'Javascript template not found at path: ' . $dispatchPath );
				}
			}
		} catch (Exception $e) {
			$this->processEngineFetchException( $e, $dispatchPath, $cacheID );
		}

		return $retVal;
	}

}

?>
