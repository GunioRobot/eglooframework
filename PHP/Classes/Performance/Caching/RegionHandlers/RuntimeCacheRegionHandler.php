<?php
/**
 * RuntimeCacheRegionHandler Class File
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
 * RuntimeCacheRegionHandler
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class RuntimeCacheRegionHandler extends CacheRegionHandler {

	private static $_egCacheMetadataNamespace = 'Runtime';

	public function getAllCacheEntries() {
		$retVal = array();

		$cacheGateway = CacheGateway::getCacheGateway();
		
		$metadata = $cacheGateway->getObject( 'egCacheMetadata', 'egCacheManagement' );

		if ($metadata == null) {
			$metadata = array();
		} else if (!isset($metadata['Regions'][self::$_egCacheMetadataNamespace])) {
			$metadata['Regions'][self::$_egCacheMetadataNamespace] = 
				array('Extents' => 
					array(
						array('key' => 'egCacheMetadata::' . self::$_egCacheMetadataNamespace, 'namespace' => 'egCacheManagement', 'Entries' => array())
						)
					);
		}

		$cacheGateway->storeObject( 'egCacheMetadata', $metadata, 'egCacheManagement' );

		$extents = $metadata['Regions'][self::$_egCacheMetadataNamespace]['Extents'];

		foreach($extents as $extent) {
			$extentMetadata = $cacheGateway->getObject( $extent['key'], $extent['namespace'] );

			if ($extentMetadata && is_array($extentMetadata) && isset($extentMetadata['Entries'])) {
				$retVal = array_merge($retVal, $extentMetadata['Entries']);
			}
		}

		return $retVal;
	}

	public function getAllCacheEntriesByNamespace() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public function getObject( $key, $namespace = 'Runtime', $keep_hot = false) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->getObject( $key, $namespace, $keep_hot );

		return $retVal;
	}

	public function storeObject( $key, $value, $namespace = 'Runtime', $ttl = 0, $keep_hot = false ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->storeObject( $key, $value, $namespace, $ttl, $keep_hot );

		if ($retVal) {
			$metadata = $cacheGateway->getObject( 'egCacheMetadata', 'egCacheManagement' );

			if ($metadata == null) {
				$metadata = array('Regions' => array(self::$_egCacheMetadataNamespace => 
					array('Extents' => 
						array(
							array('key' => 'egCacheMetadata::' . self::$_egCacheMetadataNamespace, 'namespace' => 'egCacheManagement', 'Entries' => array())
							)
						)));
			} else if (!isset($metadata['Regions'][self::$_egCacheMetadataNamespace])) {
				$metadata['Regions'][self::$_egCacheMetadataNamespace] = 
					array('Extents' => 
						array(
							array('key' => 'egCacheMetadata::' . self::$_egCacheMetadataNamespace, 'namespace' => 'egCacheManagement', 'Entries' => array())
							)
						);
			}

			$extents = $metadata['Regions'][self::$_egCacheMetadataNamespace]['Extents'];

			foreach($extents as $extentID => $extent) {
				$extentMetadata = $cacheGateway->getObject( $extent['key'], $extent['namespace'] );

				if ($extentMetadata && is_array($extentMetadata) && isset($extentMetadata['Entries'])) {
					$extentMetadata['Entries'][$key] = array('key' => $key, 'value' => $value, 'ttl' => $ttl, 'lastUpdated' => time());
				} else {
					$extentMetadata = array('Entries' => array($key => array('key' => $key, 'value' => $value, 'ttl' => $ttl, 'lastUpdated' => time())));
				}

				$cacheGateway->storeObject( $extent['key'], $extentMetadata, $extent['namespace'] );
			}
		}

		return $retVal;
	}

	public function deleteObject( $key, $namespace = 'Runtime', $kept_hot = false ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->deleteObject( $key, $namespace, $kept_hot );

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

