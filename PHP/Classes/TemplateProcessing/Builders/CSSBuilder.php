<?php
/**
 * CSSBuilder Class File
 *
 * Contains the class definition for the CSSBuilder, a concrete subclass 
 * that inherits from the TemplateBuilder class.
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
 * CSSBuilder
 * 
 * Provides a definition for a CSSBuilder subclass of the TemplateBuilder
 * abstract class.
 *
 * @package Template
 * @subpackage Template Building
 */
class CSSBuilder extends TemplateBuilder {
    
    protected $cacheID = null;
    protected $hardCacheID = null;
    protected $deployment = null;
    protected $requestInfoBean = null;
    protected $templateVariables = null;
	protected $output = null;
	protected $isHardCached = false;

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
			StyleSheetXML2ArrayDispatcher::getInstance( $this->requestInfoBean->getApplication(), $this->requestInfoBean->getInterfaceBundle() );

        $this->dispatchPath = $templateDispatcher->dispatch( $this->requestInfoBean, $this->userRequestID );
    }
    
    public function setTemplateEngine() {
		$this->templateEngine = new CSSDefaultTemplateEngine( $this->requestInfoBean->getInterfaceBundle(), 'US', 'en' );
    }
    
    public function run() {
        $output = $this->__fetch( $this->dispatchPath, $this->cacheID );

        return $output;
    }

	protected function __fetch($dispatchPath, $cacheID) {
		$retVal = null;

		try {
			$retVal = $this->templateEngine->fetch( $dispatchPath, $cacheID );
		} catch ( Exception $e ) {
			$this->processEngineFetchException( $e );
		}

		return $retVal;
	}

}

?>
