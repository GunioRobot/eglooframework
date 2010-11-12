<?php
/**
 * RuntimeCacheRegionHandler Class File
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

	public static function getAllCacheEntries() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public static function getAllCacheEntriesByNamespace() {
		$cacheGateway = CacheGateway::getCacheGateway();
	}

	public static function getObject( $key, $namespace = 'Runtime' ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->getObject( $key, $namespace );

		return $retVal;
	}

	public static function storeObject( $key, $value, $namespace = 'Runtime', $ttl = 0 ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();


		$retVal = $cacheGateway->storeObject( $key, $value, $namespace, $ttl );

		return $retVal;
	}

	public static function deleteObject( $key, $namespace = 'Runtime' ) {
		$retVal = null;

		$cacheGateway = CacheGateway::getCacheGateway();

		$retVal = $cacheGateway->deleteObject( $key, $namespace );

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

