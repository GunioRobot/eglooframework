<?php
/**
 * CacheGateway Class File
 *
 * Contains the class definition for the CacheGateway, a wrapper class for
 * tiered caching access and use.
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

    private $_memcache = null;
    private static $_singleton;

    const MEMCACHE_ADDR = '127.0.0.1';
    const MEMCACHE_PORT = 11211;

    private function __construct() {}

    private function loadMemCache() {
        $this->_memcache = new Memcache();

        try {
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
        
    }

    public function deleteObject( $id ) {
        $retVal = null;
        try {
            $retVal = $this->_memcache->delete( $id );
        } catch ( Exception $exception ) {
            eGlooLogger::writeLog( eGlooLogger::$ERROR, 
                           'Memcache Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );                
        }
        
        return $retVal;        
    }

    public function getObject( $id, $type ) {
        // TODO extensive error checking and input validation
        $retVal = null;
        try {
            $retVal = $this->_memcache->get( $id );
        } catch ( Exception $exception ) {
            eGlooLogger::writeLog( eGlooLogger::$ERROR, 
                           'Memcache Cache Lookup for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );                
        }
        
        return $retVal;
    } 

    public function getStats() {
        return $this->_memcache->getStats();
    }

    public function storeObject( $id, $obj, $type, $ttl = 0 ) {
        // TODO extensive error checking and input validation
        $retVal = null;
        
        try {
            $retVal = $this->_memcache->set( $id, $obj, false, $ttl ); 
        } catch ( Exception $exception ) {
            eGlooLogger::writeLog( eGlooLogger::$ERROR, 
                           'Memcache Cache Write for id \'' . $id . '\': ' . $exception->getMessage(), 'Memcache' );                            
        }
        
        return $retVal; 
    }

    public static function getCacheGateway() {
        if ( !isset(self::$_singleton) ) {
            self::$_singleton = new CacheGateway();
            self::$_singleton->loadMemCache();
        }
        
        return self::$_singleton; 
    }

}
 
?>
