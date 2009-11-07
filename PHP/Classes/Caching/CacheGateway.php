<?php
/**
 * CacheGateway Class File
 *
 * Contains the class definition for the CacheGateway, a wrapper class for
 * tiered caching access and use.
 * 
 * Copyright 2008 eGloo, LLC
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Performance
 * @version 1.0
 */

/**
 * Cache Gateway
 * 
 * Provides an interface for common caching functionality such as read, write,
 * lifetime, deletion and statistics.  Is capable of caching by storage tier
 * based on importance of data persistence and data properties.
 * 
 * @package Performance
 * @subpackage Caching
 * @TODO implement tiered storage; currently only supports memcache
 */
class CacheGateway {

	private $_active = false;
	private $_memcache = null;
	private $_filecache = null;
	private $_cache_file_path = '';
	private $_cache_tiers = 0x0;

	public static $USE_FILECACHE = 0x2;		// 0000 0010
	public static $USE_MEMCACHE = 0x1;		// 0000 0001

	private static $_singleton;

	const MEMCACHE_ADDR = '127.0.0.1';
	const MEMCACHE_PORT = 11211;

	private function __construct() {}

	private function loadMemCache() {
		if ($this->_active) {
			try {
				$this->_memcache = new Memcache();

				$this->_memcache->addServer( self::MEMCACHE_ADDR, self::MEMCACHE_PORT, true );
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11212, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11213, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11214, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11215, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11216, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11217, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11218, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11219, true);
				$this->_memcache->addServer( self::MEMCACHE_ADDR, 11220, true);
			} catch ( Exception $exception ) {
				eGlooLogger::writeLog( eGlooLogger::$ERROR, 
							   'Memcache Server Addition: ' . $exception->getMessage(), 'Memcache' );	 
			}
		} else {
			
		}
	}

	private function loadFileCache() {
		if ($this->_active) {
			$this->_cache_file_path = eGlooConfiguration::getCachePath() . '/_file.cache';
				if (file_exists($this->_cache_file_path)) {
					$this->_filecache = eval( 'return ' . file_get_contents($this->_cache_file_path) . ';' );
				} else {
					// eGlooLogger::writeLog( eGlooLogger::$NOTICE, 
					// 	'eGloo cache file not found: ' . $cache_file_path , 'Cache' );	 
					// eGlooLogger::writeLog( eGlooLogger::$NOTICE, 
					// 	'Creating eGloo cache file...', 'Cache' );
					$this->_filecache = array();
				}
				
				// $this->_filecache = var_export(, TRUE);
				// file_put_contents('ConfigCache.php', $config_dump);

		} else {
			
		}
	}

	public function deleteObject( $id ) {
		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::$USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->delete( $id );
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::$ERROR, 
								   'Memcache Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );				 
				}
			} else if ($this->_cache_tiers & self::$USE_FILECACHE) {
				$retVal = $this->_filecache[$id];
				unset($this->_filecache[$id]);
			}
		}
		
		return $retVal;
	}

	public function getObject( $id, $type ) {
		// TODO extensive error checking and input validation
		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::$USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->get( $id );
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::$ERROR, 
								   'Memcache Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );
				}
			} else if ($this->_cache_tiers & self::$USE_FILECACHE) {
				if (isset($this->_filecache[$id])) {
					$cache_pack = $this->_filecache[$id];
					$blob = $cache_pack['blob'];
					
					if ($cache_pack['base64']) {
						$blob = base64_decode($blob);
					}

					if ($cache_pack['serialized']) {
						$blob = unserialize($blob);
					}

					if ( (time() - $cache_pack['pack_time']) > $cache_pack['ttl'] ) {
						unset($this->_filecache[$id]);
					}

					$retVal = $blob;
				}
			}
		}
		
		return $retVal;
	} 

	public function getStats() {
		$retVal = null;
		
		if ($this->_cache_tiers & self::$USE_MEMCACHE) {
			$retVal = $this->_memcache->getStats();
		} else if ($this->_cache_tiers & self::$USE_FILECACHE) {
			
		}
		
		return $retVal;
	}

	public function storeObject( $id, $obj, $type, $ttl = 0 ) {
		// TODO extensive error checking and input validation
		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::$USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->set( $id, $obj, false, $ttl ); 
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::$ERROR, 
								   'Memcache Cache Write for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );							
				}
			} else if ($this->_cache_tiers & self::$USE_FILECACHE) {
				$cache_pack = array();
				
				$blob = $obj;
				
				if (is_array($obj) || is_object($obj)) {
					$cache_pack['serialized'] = true;
					$cache_pack['base64'] = false;
					$blob = serialize($obj);
				} else {
					$cache_pack['serialized'] = false;
					$cache_pack['base64'] = false;
				}

				// $cache_pack['base64'] = false; // TODO use this

				$cache_pack['pack_time'] = time();
				$cache_pack['ttl'] = $ttl;
				$cache_pack['blob'] = $blob;

				$this->_filecache[$id] = $cache_pack;
			}
		}

		return $retVal; 
	}

	public static function getCacheGateway() {
		if ( !isset(self::$_singleton) ) {
			self::$_singleton = new CacheGateway();

			if ($_SERVER['EG_CACHE'] === 'on') {
				self::$_singleton->_active = true;
			} else {
				self::$_singleton->_active = false;
			}

			if ($_SERVER['EG_CACHE_FILE'] === 'on') {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::$USE_FILECACHE;
				self::$_singleton->loadFileCache();
			} else {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | !self::$USE_FILECACHE;
			}

			if ($_SERVER['EG_CACHE_MEMCACHE'] === 'on') {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::$USE_MEMCACHE;
				self::$_singleton->loadMemCache();
			} else {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | !self::$USE_MEMCACHE;
			}

		}

		return self::$_singleton; 
	}

	public function __destruct() {
		if ($this->_cache_tiers & self::$USE_FILECACHE) {
			$cache_dump = var_export($this->_filecache, TRUE);
			file_put_contents($this->_cache_file_path, $cache_dump);
		}
	}

}

