<?php
/**
 * CacheRegionHandler Class File
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
 * CacheRegionHandler
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class CacheRegionHandler {

	abstract public static function getAllCacheEntries();

	abstract public static function getAllCacheEntriesByNamespace();

	abstract public static function getObject( $key, $namespace );

	abstract public static function storeObject( $key, $value, $namespace, $ttl );

	abstract public static function deleteObject( $key, $namespace );

	abstract public static function getStats();

	abstract public static function getHistory();

	abstract public static function getLogging();

	abstract public static function initialize();

	abstract public static function flush();

}

