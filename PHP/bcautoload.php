<?php
/**
 * Class and Interface Autoloader
 *
 * This file contains the function definition for the __autoload runtime handler.
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
 * @package Runtime Handlers
 * @version 1.0
 */

// Bring up the eGlooConfiguration
if ( !class_exists( 'eGlooConfiguration', false ) ) {
	include( 'PHP/Classes/Utilities/eGlooConfiguration.php' );
}

// Load the install configuration
eGlooConfiguration::loadConfigurationOptions();

// Bring up the eGlooLogger
if ( !class_exists( 'eGlooLogger', false ) ) {
	include( 'PHP/Classes/Utilities/eGlooLogger.php' );
}

// Initialize the eGlooLogger
eGlooLogger::initialize( eGlooConfiguration::getLoggingLevel(), eGlooConfiguration::getLogFormat() );

// Bring up the caching system (needed for the autoloader)
if ( !class_exists( 'CacheGateway', false ) ) {
	include( 'PHP/Classes/Caching/CacheGateway.php' );
}

// Register eGloo Autoloader
spl_autoload_register('eglooAutoload');

// Load Smarty
if ( eGlooConfiguration::getUseSmarty() ) {
	include( eGlooConfiguration::getSmartyIncludePath() );
}

// Load Doctrine
if ( eGlooConfiguration::getUseDoctrine() ) {
	include( eGlooConfiguration::getDoctrineIncludePath() );
	spl_autoload_register(array('Doctrine', 'autoload'));
}

/**
 * Defines the class and interface autoload runtime handler.
 * 
 * When PHP encounters a class or interface that has not yet been loaded or defined, __autoload 
 * is invoked with the class or interface name as its only parameter.  __autoload searches
 * the defined classpaths for possible matches.	 If a match is found, the file containing the
 * requested definition is included.  If __autoload exits without successfully matching and
 * loading the requested class or interface, the runtime will abort with an error.
 * 
 * The only permitted class or interface filename format is {CLASS_OR_INTERFACE_NAME}.php and is
 * case sensitive.	The only classpath checked is "../PHP/Classes", relative to the project root
 * and inclusive of all subdirectories.
 *
 * @param string $class_name class or interface to load
 */
function eglooAutoload($class_name) {
	$cacheGateway = CacheGateway::getCacheGateway();

	if ( ( $autoload_hash = $cacheGateway->getObject( 'autoload_hash', 'array' ) ) != null ) {
		if ( isset( $autoload_hash[$class_name] ) ) {
			// Make sure we didn't just mark this as "not found"
			if ( $autoload_hash[$class_name] !== false ) {
				include( $autoload_hash[$class_name] );
			}

			return;
		}
	} else {
		$autoload_hash = array();
	}

	static $possible_path = NULL;

	// List here whatever formats you use for your file names. Note that, if you autoload
	// a class that implements a non-loaded interface, you will also need to autoload that 
	// interface. Put the &CLASS wherever the $class_name might appear
	static $permitted_formats = array('eGlooBC&CLASS.php', '&CLASS.php', );

	// Set the first time autoload is called
	if ( NULL === $possible_path ) {
		// These are the default paths for this application
		$framework_classes = eGlooConfiguration::getFrameworkRootPath() . '/PHP';
		$application_classes = eGlooConfiguration::getApplicationsPath() . '/' . 
			eGlooConfiguration::getApplicationName() . '/PHP';

		// Customize this yourself, but leave the array_flip alone. We will use this to
		// get rid of duplicate entries from the include_path .ini list.  By default,
		// this is ordered to prefer application classes over framework classes of the same
		// name.
		$possible_path = array_flip( array( $application_classes, $framework_classes ) );

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
							
							// Since we're in backwards compatibility mode, we only exit early if we found the
							// backwards compatibility mode version.  Since we cache lookups, this isn't a problem
							// for performance after the first run.
							if ( strpos($currentNode->getFileName(), 'eGlooBC') ) {
								break;
							}
						}
					}
				}

				// We found a path, let's short-circuit this loop
				if ( $realPath !== null ) {
					break;
				}
			}

			// No path was found, so let's cache that result for future requests
			if ( $realPath !== null ) {
				include( $realPath );
				$autoload_hash[$class_name] = realpath( $realPath );
				$cacheGateway->storeObject( 'autoload_hash', $autoload_hash, 'array' );
				break;
			}
		}
	}

	// No class file was found, so let's do ourselves a favor and not bother looking again
	// TODO In the future, we should branch on this depending on deployment type
	if ( $realPath === null ) {
		$autoload_hash[$class_name] = false;
		$cacheGateway->storeObject( 'autoload_hash', $autoload_hash, 'array' );
	}

}

/**
 * Convenience method
 */
function echo_r( $mixed ) {
	echo '<pre>';
	print_r($mixed);
	echo '</pre>';
}

/**
 * Convenience method
 */
function die_r( $mixed ) {
	echo_r($mixed);
	die;
}
