<?php
/**
 * Class and Interface Autoloader
 *
 * This file contains the function definition for the __autoload runtime handler.
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
 * @package Runtime Handlers
 * @version 1.0
 */

/**
 * Defines the class and interface autoload runtime handler.
 * 
 * When PHP encounters a class or interface that has not yet been loaded or defined, __autoload 
 * is invoked with the class or interface name as its only parameter.  __autoload searches
 * the defined classpaths for possible matches.  If a match is found, the file containing the
 * requested definition is included.  If __autoload exits without successfully matching and
 * loading the requested class or interface, the runtime will abort with an error.
 * 
 * The only permitted class or interface filename format is {CLASS_OR_INTERFACE_NAME}.php and is
 * case sensitive.  The only classpath checked is "../PHP/Classes", relative to the project root
 * and inclusive of all subdirectories.
 *
 * @param string $class_name class or interface to load
 */
function __autoload($class_name) {

    if ( !class_exists( 'CacheGateway', false ) ) {
        include( '../Caching/CacheGateway.php' );
    }

    $cacheGateway = CacheGateway::getCacheGateway();

    if ( ( $autoload_hash = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'autoload_hash', 'Runtime' ) ) != null ) {
        if ( isset( $autoload_hash[$class_name] ) ) {
            include( $autoload_hash[$class_name] );
            return;
        }
    } else {
        $autoload_hash = array();
    }

    static $possible_path = NULL;

    // List here whatever formats you use for your file names. Note that, if you autoload
    // a class that implements a non-loaded interface, you will also need to autoload that 
    // interface. Put the &CLASS wherever the $class_name might appear
    static $permitted_formats = array("&CLASS.php");

    // Set the first time autoload is called
    if ( NULL === $possible_path ) {
        // These are the default paths for this application
        $possible_path = array_flip( array( "../" ) );
        // Customize this yourself, but leave the array_flip alone. We will use this to
        // get rid of duplicate entries from the include_path .ini list.

        // Merge the flipped arrays to get rid of duplicate "keys" (which are really the
        // valid include paths) then strip out the keys leaving only uniques. This is 
        // marginally faster than using array_combine and array_unique and much more elegant.
        $possible_path = array_keys( array_merge( $possible_path,
            array_flip( explode( ini_get( "include_path" ), ";" ) ) ) );
    }

    $possibility = str_replace( "&CLASS", $class_name, $permitted_formats );
    $realPath = null;

    foreach ( $possible_path as $directory ) {
        if ( file_exists( $directory ) && is_dir( $directory ) ) {
            $it = new RecursiveDirectoryIterator( $directory );

            foreach ( new RecursiveIteratorIterator( $it ) as $currentNode ) {
                if ( strpos( $currentNode->getFileName(), $class_name ) !== false ) {
                    // class_name was included, now compare against all permitted file name patterns
                    foreach ( $possibility as $compare ) {
                        // by using $compare, you will get a qualified file name
                        if ( $compare === $currentNode->getFileName() ) {
                            $realPath = $currentNode->getPathName();
                            break;
                        }
                    }
                }
                
                if ( $realPath !== null ) {
                    break;
                }
            }

            if ( $realPath !== null ) {
                include( $realPath );
                $autoload_hash[$class_name] = realpath( $realPath );
                $cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'autoload_hash', $autoload_hash, 'Runtime' );                
                break;
            }
        }
    }
    
}

?>