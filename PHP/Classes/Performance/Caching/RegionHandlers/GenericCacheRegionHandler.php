<?php
/**
 * GenericCacheRegionHandler Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * GenericCacheRegionHandler
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class GenericCacheRegionHandler extends CacheRegionHandler {

	public static function getAllCacheEntries() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public static function getAllCacheEntriesByNamespace() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public static function getObject( $key, $namespace = 'egDefault', $keep_hot = false ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->getObject( $key, $namespace, $keep_hot );

		return $retVal;
	}

	public static function storeObject( $key, $value, $namespace = 'egDefault', $ttl = 0, $keep_hot = false ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();


		$retVal = $cacheGateway->storeObject( $key, $value, $namespace, $ttl, $keep_hot );

		return $retVal;
	}

	public static function deleteObject( $key, $namespace = 'egDefault', $kept_hot = false ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->deleteObject( $key, $namespace, $kept_hot );

		return $retVal;
	}

	public static function getStats() {
		$cacheGateway = CacheGateway::getCacheGateway();

		return $cacheGateway->getStats();
	}

	public static function getHistory() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public static function getLogging() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public static function initialize() {
		$cacheGateway = CacheGateway::getCacheGateway();
		
		return $cacheGateway->initialize();
	}

	public static function flush() {
		$cacheGateway = CacheGateway::getCacheGateway();
		
		return $cacheGateway->flushAllCache();
	}

}

