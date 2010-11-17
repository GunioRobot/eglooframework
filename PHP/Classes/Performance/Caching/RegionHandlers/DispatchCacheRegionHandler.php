<?php
/**
 * DispatchCacheRegionHandler Class File
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
 * DispatchCacheRegionHandler
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class DispatchCacheRegionHandler extends CacheRegionHandler {

	private static $_egCacheMetadataNamespace = 'Dispatches';

	public function getAllCacheEntries() {
		$retVal = array();

		$cacheGateway = CacheGateway::getCacheGateway();
		
		$metadata = $cacheGateway->getObject( 'egCacheMetadata', 'egCacheManagement' );

		if ($metadata == null) {
			$metadata = array();
		} else if (!isset($metadata['Regions'][self::$_egCacheMetadataNamespace])) {
			$metadata['Regions'][self::$_egCacheMetadataNamespace] = array('Entries' => array());
		}

		$retVal = $metadata['Regions'][self::$_egCacheMetadataNamespace]['Entries'];

		return $retVal;
	}

	public function getAllCacheEntriesByNamespace() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public function getObject( $key, $namespace = 'Dispatches' ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->getObject( $key, $namespace );

		return $retVal;
	}

	public function storeObject( $key, $value, $namespace = 'Dispatches', $ttl = 0 ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$metadata = $cacheGateway->getObject( 'egCacheMetadata', 'egCacheManagement' );

		if ($metadata == null) {
			$metadata = array('Regions' => array(self::$_egCacheMetadataNamespace => array('Entries' => array())));
		} else if (!isset($metadata['Regions'][self::$_egCacheMetadataNamespace])) {
			$metadata['Regions'][self::$_egCacheMetadataNamespace] = array('Entries' => array());
		}

		$metadata['Regions'][self::$_egCacheMetadataNamespace]['Entries'][$key] = 
			array('key' => $key, 'value' => $value, 'ttl' => $ttl, 'lastUpdated' => time());

		$retVal = $cacheGateway->storeObject( $key, $value, $namespace, $ttl );
		
		$cacheGateway->storeObject( 'egCacheMetadata', $metadata, 'egCacheManagement' );

		return $retVal;
	}

	public function deleteObject( $key, $namespace = 'Dispatches' ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->deleteObject( $key, $namespace );

		return $retVal;
	}

	public function getStats() {
		$cacheGateway = CacheGateway::getCacheGateway();

		return $cacheGateway->getStats();
	}

	public function getHistory() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public function getLogging() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public function initialize() {
		$cacheGateway = CacheGateway::getCacheGateway();
		
		return $cacheGateway->initialize();
	}

	public function flush() {
		$cacheGateway = CacheGateway::getCacheGateway();
		
		return $cacheGateway->flushAllCache();
	}

}
