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

	const USE_APCCACHE		= 0x1;		// 0000 0001
	const USE_FILECACHE		= 0x2;		// 0000 0010
	const USE_MEMCACHE		= 0x4;		// 0000 0100

	private static $_singleton;

	const MEMCACHED_HOST = '127.0.0.1';
	const MEMCACHED_PORT = 11211;

	private function __construct() {}

	private function loadMemcache() {
		if ($this->_active) {
			try {
				$this->_memcache = new Memcache();

				$persist_connection = true;
				$weight = 1;
				$timeout = 1;
				$retry_interval = 15;
				$status = true; // Server is considered online
				$failure_callback = array('CacheGateway', 'serverFailure');

				for ($i = 0; $i <= 10; $i++) {
					$this->_memcache->addServer( 	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

			} catch ( Exception $exception ) {
				eGlooLogger::writeLog( eGlooLogger::ERROR, 
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
					// eGlooLogger::writeLog( eGlooLogger::NOTICE, 
					// 	'eGloo cache file not found: ' . $cache_file_path , 'Cache' );	 
					// eGlooLogger::writeLog( eGlooLogger::NOTICE, 
					// 	'Creating eGloo cache file...', 'Cache' );
					$this->_filecache = array();
				}
				
				// $this->_filecache = var_export(, TRUE);
				// file_put_contents('ConfigCache.php', $config_dump);

		} else {
			
		}
	}

	private function loadAPCCache() {
		// Do nothing
	}

	public function deleteObject( $id ) {
		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					apc_delete($id);
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'APC' );				 
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->delete( $id );
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'Memcache Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );				 
				}
			} else if ($this->_cache_tiers & self::USE_FILECACHE) {
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
			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					$retVal = apc_fetch($id);
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'APC' );				 
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->get( $id );
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'Memcache Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );
				}
			} else if ($this->_cache_tiers & self::USE_FILECACHE) {
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
		
		if ($this->_cache_tiers & self::USE_MEMCACHE) {
			$retVal = $this->_memcache->getStats();
		} else if ($this->_cache_tiers & self::USE_FILECACHE) {
			
		}
		
		return $retVal;
	}

	public function storeObject( $id, $obj, $type, $ttl = 0 ) {
		// TODO extensive error checking and input validation
		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					$retVal = apc_add( $id, $obj, $ttl );
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'APC' );				 
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->set( $id, $obj, false, $ttl ); 
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
							'Memcache Cache Write for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );							
				}
			} else if ($this->_cache_tiers & self::USE_FILECACHE) {
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

	public static function serverFailure( $host, $port ) {
		eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 
			'Memcache daemon on host ' . $host . ' and port ' . $port . ' has failed',
			'Memcache' );
		eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Attempting server failover... ', 'Memcache' );
	}

	public static function getCacheGateway() {
		if ( !isset(self::$_singleton) ) {
			self::$_singleton = new CacheGateway();

			if ($_SERVER['EG_CACHE'] === 'ON') {
				self::$_singleton->_active = true;
			} else {
				self::$_singleton->_active = false;
			}

			if ($_SERVER['EG_CACHE_APC'] === 'ON') {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_APCCACHE;
				self::$_singleton->loadAPCCache();
			} else {
				
			}

			if ($_SERVER['EG_CACHE_FILE'] === 'ON') {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_FILECACHE;
				self::$_singleton->loadFileCache();
			} else {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | !self::USE_FILECACHE;
			}

			if ($_SERVER['EG_CACHE_MEMCACHE'] === 'ON') {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_MEMCACHE;
				self::$_singleton->loadMemcache();
			} else {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | !self::USE_MEMCACHE;
			}

		}

		return self::$_singleton; 
	}

	public function __destruct() {
		if ($this->_cache_tiers & self::USE_FILECACHE) {
			$cache_dump = var_export($this->_filecache, TRUE);
			file_put_contents($this->_cache_file_path, $cache_dump);
		}
	}

}

