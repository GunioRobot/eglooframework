<?php
/**
 * Smarty Memcache Handler Function Definition File
 *
 * This file contains the function definition for the Smarty Memcache Handler.
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
 * @package Runtime Handlers
 * @version 1.0
 */

/**
 * Defines the Smarty Memcache Handler
 * 
 * Usage Example<br>
 * <pre>
 * $smarty = new Smarty;
 * $smarty->cache_handler_func = 'smarty_cache_memcache';
 * $smarty->caching = true;
 * $smarty->display('index.tpl');
 * </pre>
 * 
 * @param    string   $action         Cache operation to perform ( read | write | clear )
 * @param    mixed    $smarty         Reference to an instance of Smarty
 * @param    string   $cache_content  Reference to cached contents
 * @param    string   $tpl_file       Template file name
 * @param    string   $cache_id       Cache identifier
 * @param    string   $compile_id     Compile identifier
 * @param    integer  $exp_time       Expiration time
 * @return   boolean                  true on success, false otherwise
 */
function smarty_cache_memcache($action, &$smarty, &$cache_content, $tpl_file=null, $cache_id=null, $compile_id=null, $exp_time=null) {
    // Create unique cache id:
    // We are using smarty's internal functions here to be as compatible as possible.
    $_auto_id    = $smarty->_get_auto_id($cache_id, $compile_id);
    $_cache_file = substr($smarty->_get_auto_filename(".", $tpl_file, $_auto_id), 2);
    $memcache_id  = "smarty_memcache|".$_cache_file;
    
    $cacheGateway = CacheGateway::getCacheGateway();

    switch ($action) {
        case 'read':
            // read cache from shared memory
            $cache_content = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $memcache_id, 'SmartyMemcacheHandler' );

            $return = true;
            break;
        case 'write':
            // Put content into cache
            $split      = explode("\n", $cache_content, 2);
            $attributes = unserialize($split[1]);
            $ttl = $attributes['expires'] - time();

            $cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $memcache_id, $cache_content, 'SmartyMemcacheHandler', $ttl );
            $return = true;
            break;
        case 'clear':
            // clear cache info 
            if( empty( $cache_id ) && empty( $compile_id ) && empty( $tpl_file ) ) { 
                // clear them all
                $results = false; 
                //$results = mysql_query("delete from CACHE_PAGES"); 
            } else {
                $cacheGateway->deleteObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $cache_id, 'SmartyMemcacheHandler' );
            }
             
            if(!$results) { 
                $smarty_obj->_trigger_error_msg("cache_handler: query failed."); 
            }
             
            $return = $results;             
            break;        
        default:
            // error, unknown action
            $smarty->trigger_error("cache_handler: unknown action \"$action\"");
            
            $return = false;
            break;
    }

    return $return;
}
