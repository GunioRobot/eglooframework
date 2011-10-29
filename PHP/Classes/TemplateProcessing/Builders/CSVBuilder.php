<?php
/**
 * CSVBuilder Class File
 *
 * $file_block_description
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * CSVBuilder
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CSVBuilder extends TemplateBuilder {

	protected $cacheID = null;
	protected $hardCacheID = null;
	protected $contentProcessors = null;
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

	public function setContentProcessors( $contentProcessors ) {
		$this->contentProcessors = $contentProcessors;

		foreach( $this->contentProcessors as $contentProcessor ) {
			$contentProcessor->setTemplateEngine( $this->templateEngine );
			$contentProcessor->prepareContent();
		}
	}

	public function setCacheID( $cacheID, $ttl = 3600 ) {
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
		$this->dispatchPath = eGlooConfiguration::getFrameworkRootPath() . '/Templates/Core/Generic/CSV/GenericCSV.tpl';
	}

	public function setTemplateEngine() {
		$this->templateEngine = new CSVDefaultTemplateEngine( $this->requestInfoBean->getInterfaceBundle(), 'US', 'en' );
	}

	public function run() {
		$retVal = null;

		if (isset($this->hardCacheID) && $this->isHardCached) {
			$retVal = $this->output;
		} else if (isset($this->hardCacheID) && !$this->isHardCached) {
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
			$retVal = $this->templateEngine->fetch( $dispatchPath, $cacheID );
		} catch ( Exception $e ) {
			$retVal = $this->processEngineFetchException( $e, $dispatchPath, $cacheID );
		}

		return $retVal;
	}

}
