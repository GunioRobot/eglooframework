<?php
namespace eGloo\Plugin\Form\Builder;

use \eGloo\Configuration as Configuration;
use \eGloo\Utility\Logger as Logger;

use \eGloo\Performance\Caching\Gateway as CacheGateway;

use \TemplateBuilder as TemplateBuilder;
use \XHTMLDefaultTemplateEngine as XHTMLDefaultTemplateEngine;
use \XHTMLXML2ArrayDispatcher as XHTMLXML2ArrayDispatcher;

use \ErrorException as ErrorException;
use \Exception as Exception;

/**
 * eGloo\Plugin\Form\Builder\FormBuilder Class File
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
 * @category Plugins
 * @package Forms
 * @subpackage Builders
 * @version 1.0
 */

/**
 * eGloo\Plugin\Form\Builder\FormBuilder
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Builders
 */
class FormBuilder extends TemplateBuilder {

    private $cacheID = null;
    private $deployment = null;
    private $requestInfoBean = null;
    private $templateVariables = null;
	private $output = null;
	private $isHardCached = false;

    public function setRequestInfoBean( $requestInfoBean ) {
        $this->requestInfoBean = $requestInfoBean;
    }

    public function setTemplateVariables( $templateVariables ) {
        $this->templateVariables = $templateVariables;
        foreach( $templateVariables as $key => $value) $this->templateEngine->assign( $key, $value );        
    }

    public function setContentProcessors( $contentProcessors ) {
        $this->contentProcessors = $contentProcessors;
        
        foreach( $this->contentProcessors as $contentProcessor ) {
            $contentProcessor->setTemplateEngine( $this->templateEngine );
            $contentProcessor->prepareContent();
        }
    }

    public function setCacheID( $cacheID, $ttl = 3600 ) {
        // $this->templateEngine->cache_handler_func = 'smarty_cache_memcache';
        $this->templateEngine->caching = 2; // lifetime is per cache
        
        $this->templateEngine->cache_lifetime = $ttl;
        
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

			$retVal = $cacheGateway->getObject( Configuration::getUniqueInstanceIdentifier() . '::' . $this->hardCacheID, 'HardCache');

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
			XHTMLXML2ArrayDispatcher::getInstance( $this->requestInfoBean->getApplication(), $this->requestInfoBean->getInterfaceBundle() );

		$this->dispatchPath = $templateDispatcher->dispatch( $this->requestInfoBean, $this->userRequestID, $this->userRequestClass );
    }

    public function setTemplateEngine() {
        $this->templateEngine = new XHTMLDefaultTemplateEngine( $this->requestInfoBean->getInterfaceBundle(), 'US', 'en' );    
    }

    public function run() {
		$retVal = null;

		if (isset($this->hardCacheID) && $this->isHardCached) {
			$retVal = $this->output;
		} else if (isset($this->hardCacheID) && !$this->isHardCached) {
			try {
				$retVal = $this->__fetch( $this->dispatchPath, $this->cacheID );
				$cacheGateway = CacheGateway::getCacheGateway();
				$cacheGateway->storeObject( Configuration::getUniqueInstanceIdentifier() . '::' . $this->hardCacheID, $retVal, 'HardCache', $this->ttl);
			} catch (Exception $e) {
				echo_r($e->getMessage());
				die;
			}
		} else {
			try {
				$retVal = $this->__fetch( $this->dispatchPath, $this->cacheID );
			} catch (Exception $e) {
				echo_r($e->getMessage());
				die;
			}
		}

        return $retVal;
    }

	private function __fetch($dispatchPath, $cacheID) {
		$retVal = null;

		try {
			$retVal = $this->templateEngine->fetch( $dispatchPath, $cacheID );
		} catch (ErrorException $e) {
			$matches = array();

			if ( preg_match('~.*the \$compile_dir \'(.*)\' does not exist, or is not a directory.*~', $e->getMessage(), $matches ) ) {
				if (count($matches) > 1) {
					try {
						$mode = 0777;
						$recursive = true;

						mkdir( $matches[1], $mode, $recursive );

						$retVal = $this->__fetch( $dispatchPath, $cacheID );
					} catch (Exception $e){
						throw $e;
					}
				}
			} else if ( preg_match('~.*the \$cache_dir \'(.*)\' does not exist, or is not a directory.*~', $e->getMessage(), $matches ) ) {
				if (count($matches) > 1) {
					try {
						$mode = 0777;
						$recursive = true;

						mkdir( $matches[1], $mode, $recursive );

						$retVal = $this->__fetch( $dispatchPath, $cacheID );
					} catch (Exception $e){
						throw $e;
					}
				}
			} else {
				Logger::writeLog( Logger::EMERGENCY, 'Exception thrown on XHTML engine fetch(): ' . $e->getMessage(), 'TemplateProcessing' );
			}
		}

		return $retVal;
	}

}

