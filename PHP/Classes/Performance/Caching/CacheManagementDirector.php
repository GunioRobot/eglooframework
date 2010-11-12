<?php
/**
 * CacheManagementDirector Class File
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
 * CacheManagementDirector
 *
 * This class is meant to serve as the director interface for all child cache managers
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CacheManagementDirector {

	private static $_cacheRegions = array(
		// 'Configuration',
		// 'Content',
		// 'DataProcessing',
		// 'Dispatches',
		// 'History',
		// 'Logging',
		// 'RequestProcessing',
		'Runtime',
		// 'Session',
		// 'Static',
		// 'Templating',
	);

	public static function getCacheRegionHandler( $region ) {
		$retVal = null;

		switch($region) {
			case 'Configuration' :
				$retVal = new ConfigurationCacheRegionHandler();
				break;
			case 'Content' :
				$retVal = new ContentCacheRegionHandler();
				break;
			case 'DataProcessing' :
				$retVal = new DataProcessingCacheRegionHandler();
				break;
			case 'Dispatches' :
				$retVal = new DispatchesCacheRegionHandler();
				break;
			case 'History' :
				$retVal = new HistoryCacheRegionHandler();
				break;
			case 'Logging' :
				$retVal = new LoggingCacheRegionHandler();
				break;
			case 'RequestProcessing' :
				$retVal = new RequestProcessingCacheRegionHandler();
				break;
			case 'Runtime' :
				$retVal = new RuntimeCacheRegionHandler();
				break;
			case 'Session' :
				$retVal = new SessionCacheRegionHandler();
				break;
			case 'Static' :
				$retVal = new StaticCacheRegionHandler();
				break;
			case 'Templating' :
				$retVal = new TemplatingCacheRegionHandler();
				break;
			default :
				break;
		}

		return $retVal;
	}

	public static function getCacheGatewayNamespaces() {
		
	}

	public static function getCacheGatewayKeysByNamespace() {
		
	}

	public static function getAllCacheGatewayKeys() {
		
	}

	public static function getAllCacheRegionLabels() {
		return self::$_cacheRegions;
	}

	public static function getAllCacheRegionMetadata() {
		
	}

	public static function getAllCacheEntriesByRegion() {

	}

	public static function getAllCacheEntriesByRegionAndNamespace() {

	}

	public static function getAllCacheEntries() {
		$retVal = array();

		foreach(self::$_cacheRegions as $cacheRegion) {
			$handler = self::getCacheRegionHandler($cacheRegion);
			
			$retVal[$cacheRegion] = $handler->getAllCacheEntries();
			// $retVal[$cacheRegion] = array(array('key' => 'foo', 'value' => 'bar', 'ttl' => 99, 'lastUpdated' => 971918));
		}

		return $retVal;
	}

	// Not sure why you'd ever need this, but if you do, we'll let you eventually
	public static function getAllCacheRegions() {
		
	}

}

