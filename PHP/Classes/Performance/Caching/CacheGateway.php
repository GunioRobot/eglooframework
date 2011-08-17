<?php
/**
 * CacheGateway Class File
 *
 * Contains the class definition for the CacheGateway, a wrapper class for
 * tiered caching access and use.
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

	private $_piping_hot_cache = array();

	const USE_APCCACHE		= 0x1;		// 0000 0001
	const USE_FILECACHE		= 0x2;		// 0000 0010
	const USE_MEMCACHE		= 0x4;		// 0000 0100

	private static $_singleton;

	private $_memcache_servers = array();

	const MEMCACHED_HOST = '127.0.0.1';
	const MEMCACHED_PORT = 11211;

	private function __construct() {}

	private function loadMemcache() {
		if ($this->_active) {
			try {
				$persist_connection = true;
				$weight = 1;
				$timeout = 1;
				$retry_interval = 15;
				$status = true; // Server is considered online
				$failure_callback = array('CacheGateway', 'serverFailure');

				$i = 0;

				// Runtime
				$newMemcacheServer = new Memcache();
				
				for ($i = 0; $i <= 0; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Runtime'] = $newMemcacheServer;

				// Configuration
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 1; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Configuration'] = $newMemcacheServer;

				// Request Validation / Request Processing
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 2; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['RequestValidation'] = $newMemcacheServer;

				// Dispatching
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 3; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Dispatching'] = $newMemcacheServer;

				// Session
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 4; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Session'] = $newMemcacheServer;

				// Data Processing
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 5; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['DataProcessing'] = $newMemcacheServer;

				// Content
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 6; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Content'] = $newMemcacheServer;

				// Static
				$this->_memcache = new Memcache();

				for ($i = $i; $i <= 7; $i++) {
					$this->_memcache->addServer( 	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				// Templating
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 8; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Templating'] = $newMemcacheServer;

				// Other
				$newMemcacheServer = new Memcache();
				
				for ($i = $i; $i <= 9; $i++) {
					$newMemcacheServer->addServer(	self::MEMCACHED_HOST,
													self::MEMCACHED_PORT + $i,
													$persist_connection,
													$weight,
													$timeout,
													$retry_interval,
													$status,
													$failure_callback );
				}

				$this->_memcache_servers['Other'] = $newMemcacheServer;

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
				
				// $this->_filecache = var_export(, true);
				// file_put_contents('ConfigCache.php', $config_dump);

		} else {
			
		}
	}

	private function loadAPCCache() {
		// Do nothing
	}

	public function active() {
		return $this->_active;
	}

	public function deleteObject( $id, $namespace = null, $kept_hot = false ) {
		$retVal = null;

		if ( !$namespace ) {
			$namespace = 'egDefault';
		}

		$id = $namespace . '::' . $id;

		if ( $kept_hot && isset($this->_piping_hot_cache[$id]) ) {
			unset($this->_piping_hot_cache[$id]);
		}

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
					$memcacheServer = null;

					if (isset($this->_memcache_servers[$namespace])) {
						$memcacheServer = $this->_memcache_servers[$namespace];
					} else {
						$memcacheServer = $this->_memcache_servers['Other'];
					}

					$retVal = $memcacheServer->delete( $id );
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

	public function getObject( $id, $namespace = null, $keep_hot = false ) {
		// TODO extensive error checking and input validation
		$retVal = null;

		if ($this->_active) {
			if ( !$namespace ) {
				$namespace = 'egDefault';
			}

			$id = $namespace . '::' . $id;

			if ( $keep_hot && isset($this->_piping_hot_cache[$id]) ) {
				$retVal = $this->_piping_hot_cache[$id];
			} else {
				if ($this->_cache_tiers & self::USE_APCCACHE) {
					try {
						$retVal = apc_fetch($id);

						$this->_piping_hot_cache[$id] = $retVal;
					} catch ( Exception $exception ) {
						eGlooLogger::writeLog( eGlooLogger::ERROR, 
							'APC Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'APC' );				 
					}
				} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
					try {
						$memcacheServer = null;

						if (isset($this->_memcache_servers[$namespace])) {
							$memcacheServer = $this->_memcache_servers[$namespace];
						} else {
							$memcacheServer = $this->_memcache_servers['Other'];
						}

						$retVal = $memcacheServer->get( $id );

						$this->_piping_hot_cache[$id] = $retVal;
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

						$this->_piping_hot_cache[$id] = $retVal;
					}
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

	public function storeObject( $id, $obj, $namespace = null, $ttl = 0, $keep_hot = false ) {
		// TODO extensive error checking and input validation
		$retVal = null;

		if ($this->_active) {
			if ( !$namespace ) {
				$namespace = 'egDefault';
			}

			$id = $namespace . '::' . $id;

			if ( $keep_hot ) {
				$retVal = $this->_piping_hot_cache[$id] = $obj;
			}

			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					$retVal = apc_store( $id, $obj, $ttl );
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'APC' );
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$memcacheServer = null;

					if (isset($this->_memcache_servers[$namespace])) {
						$memcacheServer = $this->_memcache_servers[$namespace];
					} else {
						$memcacheServer = $this->_memcache_servers['Other'];
					}

					$retVal = $memcacheServer->set( $id, $obj, false, $ttl ); 
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

	public function flushAllCache() {
		$retVal = null;

		$systemInfoBean = SystemInfoBean::getInstance();
		$systemActions = $systemInfoBean->appendValue('SystemActions', 'Preparing to flush CacheGateway Cache');

		if ($this->_active) {
			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					$retVal = apc_clear_cache();
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Flush: ' . $exception->getMessage(), 'APC' );
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->flush();
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'Memcache Cache Flush: ' . $exception->getMessage(), 'Memcache' );
				}
			} else if ($this->_cache_tiers & self::USE_FILECACHE) {
				$this->_filecache = array();
			}
		}

		$systemActions = $systemInfoBean->appendValue('SystemActions', 'CacheGateway Cache flushed');

		return $retVal; 
	}

	public function flushApplicationCache() {
		// TODO Invalidate application level cache
		// For now, just flush all

		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					$retVal = apc_clear_cache();
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Flush: ' . $exception->getMessage(), 'APC' );
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->flush();
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'Memcache Cache Flush: ' . $exception->getMessage(), 'Memcache' );
				}
			} else if ($this->_cache_tiers & self::USE_FILECACHE) {
				$this->_filecache = array();
			}
		}

		return $retVal; 
	}

	public function flushUIBundleCache() {
		// TODO Invalidate bundle level cache
		// For now, just flush all

		$retVal = null;

		if ($this->_active) {
			if ($this->_cache_tiers & self::USE_APCCACHE) {
				try {
					$retVal = apc_clear_cache();
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'APC Cache Flush: ' . $exception->getMessage(), 'APC' );
				}
			} else if ($this->_cache_tiers & self::USE_MEMCACHE) {
				try {
					$retVal = $this->_memcache->flush();
				} catch ( Exception $exception ) {
					eGlooLogger::writeLog( eGlooLogger::ERROR, 
						'Memcache Cache Flush: ' . $exception->getMessage(), 'Memcache' );
				}
			} else if ($this->_cache_tiers & self::USE_FILECACHE) {
				$this->_filecache = array();
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

	/**
	 *
	 *
	 */
	public static function initialize() {
		$cacheGateway = self::getCacheGateway();

		if ($cacheGateway->active()) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Initializing Caching System ', 'Cache' );

			// TODO Add a check for this.  It's unlikely that someone will be switching
			// applications or bundles and not want to invalidate application/bundle level
			// caches, but who knows.
			$egLastApplication = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'egLastApplication', 'Runtime');
			$egLastUIBundle = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'egLastUIBundle', 'Runtime');
			
			$currentApplication = eGlooConfiguration::getApplicationName();
			$currentBundle = eGlooConfiguration::getUIBundleName();
			
			if ($currentApplication !== $egLastApplication) {
				// Invalidate application level cache
				$cacheGateway->flushApplicationCache();
			}

			if ($currentBundle !== $egLastUIBundle) {
				// Invalidate bundle level cache
				$cacheGateway->flushUIBundleCache();
			}

			$egLastApplication = $cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'egLastApplication', $currentApplication, 'Runtime');
			$egLastUIBundle = $cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'egLastUIBundle', $currentBundle, 'Runtime');
		}
	}


	public static function getCacheGateway() {
		if ( !isset(self::$_singleton) ) {
			self::$_singleton = new CacheGateway();

			if ( isset($_SERVER['EG_CACHE']) && $_SERVER['EG_CACHE'] === 'ON' ) {
				self::$_singleton->_active = true;
			} else if ( eGlooConfiguration::getUseCache() ) {
				self::$_singleton->_active = true;
			} else {
				self::$_singleton->_active = false;
			}

			if ( isset($_SERVER['EG_CACHE_APC']) && $_SERVER['EG_CACHE_APC'] === 'ON' ) {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_APCCACHE;
				self::$_singleton->loadAPCCache();
			} else if ( eGlooConfiguration::getUseAPCCache() ) {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_APCCACHE;
				self::$_singleton->loadAPCCache();
			} else {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | !self::USE_APCCACHE;
			}

			if ( isset($_SERVER['EG_CACHE_FILE']) && $_SERVER['EG_CACHE_FILE'] === 'ON' ) {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_FILECACHE;
				self::$_singleton->loadFileCache();
			} else if ( eGlooConfiguration::getUseFileCache() ) {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_FILECACHE;
				self::$_singleton->loadFileCache();
			} else {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | !self::USE_FILECACHE;
			}

			if ( isset($_SERVER['EG_CACHE_MEMCACHE']) && $_SERVER['EG_CACHE_MEMCACHE'] === 'ON' ) {
				self::$_singleton->_cache_tiers = self::$_singleton->_cache_tiers | self::USE_MEMCACHE;
				self::$_singleton->loadMemcache();
			} else if ( eGlooConfiguration::getUseMemcache() ) {
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
			$cache_dump = var_export($this->_filecache, true);
			file_put_contents($this->_cache_file_path, $cache_dump);
		}
	}

}

