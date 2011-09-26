<?php
/**
 * Class and Interface Autoloader
 *
 * This file contains the function definition for the __autoload runtime handler.
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
 * @package Runtime Handlers
 * @version 1.0
 */

// Bring up the eGlooConfiguration
if ( !class_exists( 'eGlooConfiguration', false ) ) {
	include( 'PHP/Classes/System/Configuration/eGlooConfiguration.php' );
}

// Load the install configuration
eGlooConfiguration::loadCLIConfigurationOptions();

// Bring up the eGlooLogger
if ( !class_exists( 'eGlooLogger', false ) ) {
	include( 'PHP/Classes/System/Utilities/eGlooLogger.php' );
}

global $egloo_script_arguments;

if ( in_array('-v', $egloo_script_arguments) ) {
	$logging_level = eGlooLogger::STAGING;
} else if ( in_array('-vv', $egloo_script_arguments) ) {
	$logging_level = eGlooLogger::DEVELOPMENT;
} else if ( in_array('-q', $egloo_script_arguments) ) {
	$logging_level = eGlooLogger::LOG_OFF;
} else {
	$logging_level = eGlooLogger::PRODUCTION;
}

// Initialize the eGlooLogger
eGlooLogger::initialize( $logging_level, eGlooConfiguration::getLogFormat( false ) );


// Bring up the caching system (needed for the autoloader)
if ( !class_exists( 'CacheGateway', false ) ) {
	include( 'PHP/Classes/Performance/Caching/CacheGateway.php' );
}

// Register eGloo Autoloader
spl_autoload_register('eglooAutoload');

/**
 * These conditional includes are ordered for speed; do not reorganize without benchmarking and serious testing
 */

// Load Haanga
if ( eGlooConfiguration::getUseHaanga() ) {
	include( eGlooConfiguration::getHaangaIncludePath() );
}

// Load Smarty
if ( eGlooConfiguration::getUseSmarty() ) {
	include( eGlooConfiguration::getSmartyIncludePath() );	
}

// Load Swift
if ( eGlooConfiguration::getUseSwift() ) {
	include( eGlooConfiguration::getSwiftIncludePath() );
}

// Load Twig
if ( eGlooConfiguration::getUseTwig() ) {
	include( eGlooConfiguration::getTwigIncludePath() );
	spl_autoload_register(array('Twig_Autoloader', 'autoload'));
}

// Load S3/CloudFront
if ( eGlooConfiguration::getUseS3() ) {
	include( eGlooConfiguration::getS3IncludePath() );
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
	
	if ( ( $autoload_hash = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'autoload_hash', 'Runtime', true ) ) != null ) {
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

	// static $possible_path = null;
	$possible_path = null;

	static $cli_paths = null;

	// List here whatever formats you use for your file names. Note that, if you autoload
	// a class that implements a non-loaded interface, you will also need to autoload that 
	// interface. Put the &CLASS wherever the $class_name might appear
	static $permitted_formats = array("&CLASS.php");

	$sanityCheckClassLoading = eGlooConfiguration::getPerformSanityCheckClassLoading();

	// Set the first time autoload is called
	if ( null === $cli_paths ) {
		// These are the default paths for this application
		$framework_classes = eGlooConfiguration::getFrameworkPHPPath( true );
		$application_classes = eGlooConfiguration::getApplicationPHPPath( true );
		$extra_class_path = eGlooConfiguration::getExtraClassPath( true );

		$base_class_paths = array( $application_classes, $extra_class_path, $framework_classes );

		if ( class_exists('eGlooCLI', false) ) {
			$class_paths = array_merge( eGlooCLI::getClassPaths(), $base_class_paths );
		} else {
			$class_paths = $base_class_paths;
		}

		// Customize this yourself, but leave the array_flip alone. We will use this to
		// get rid of duplicate entries from the include_path .ini list.  By default,
		// this is ordered to prefer application classes over framework classes of the same
		// name.
		$possible_path = array_flip( $class_paths );

		// Merge the flipped arrays to get rid of duplicate "keys" (which are really the
		// valid include paths) then strip out the keys leaving only uniques. This is 
		// marginally faster than using array_combine and array_unique and much more elegant.
		$possible_path = array_keys( array_merge( $possible_path,
			array_flip( explode( ini_get( "include_path" ), ";" ) ) ) );

		if ( class_exists('eGlooCLI', false) ) {
			$cli_paths = $possible_path;
		}
	} else {
		$possible_path = $cli_paths;
	}

	$possibility = str_replace( "&CLASS", $class_name, $permitted_formats );
	$realPath = null;

	if ($sanityCheckClassLoading) {
		$instances = array();
	}

	foreach ( $possible_path as $directory ) {
		if ($sanityCheckClassLoading) {
			$instances[$directory] = array();
		}

		if ( file_exists( $directory ) && is_dir( $directory ) ) {

			$it = new RecursiveDirectoryIterator( $directory );

			foreach ( new RecursiveIteratorIterator( $it ) as $currentNode ) {

				if ( strpos( $currentNode->getFileName(), $class_name ) !== false ) {
					// class_name was included, now compare against all permitted file name patterns
					foreach ( $possibility as $compare ) {
						// by using $compare, you will get a qualified file name

						if ( $compare === $currentNode->getFileName() ) {
							$realPath = $currentNode->getPathName();
							if ($sanityCheckClassLoading && !in_array($realPath, $instances[$directory])) {
								$instances[$directory][] = $realPath;
							} else {
								break;
							}
						}
					}
				}

				// We found a path and if we're not doing sanity checking, let's short-circuit this loop
				if ( $realPath !== null && !$sanityCheckClassLoading) {
					break;
				}

			}

			// Path was found, so let's cache that result for future requests
			if ( $realPath !== null ) {
				if ($sanityCheckClassLoading) {
					foreach( $instances as $directory => $instancePathSet) {
						$noConflicts = true;
				
						if (count($instancePathSet) > 1) {
							$noConflicts = false;
							$errorMessage = 'Duplicate class "' . $class_name . '" found.';
				
							foreach($instances as $classPath => $classPathInstances) {
								foreach($classPathInstances as $instance) {
									$errorMessage .= "\n\tClass Path: " . $classPath . "\n\tInstance: " . $instance . "\n";
								}
							}

							// In case you want to know why we do this, it's because exceptions in a PHP autoloader
							// blow up the stack when thrown.  So instead of throwing, we create it and pass it by hand
							// to the global exception handler, just as if it was thrown.  Voila!
							$errorException = new ErrorException($errorMessage);

							eGlooLogger::global_exception_handler($errorException);
							exit;
						}
					}
				}

				include( $realPath );
				$autoload_hash[$class_name] = realpath( $realPath );
				$cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'autoload_hash', $autoload_hash, 'Runtime', 0, true );
				break;
			}
		}
	}

	// No class file was found, so let's do ourselves a favor and not bother looking again
	// TODO In the future, we should branch on this depending on deployment type
	if ( $realPath === null ) {
		$autoload_hash[$class_name] = false;
		$cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . 'autoload_hash', $autoload_hash, 'Runtime', 0, true );
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

function echo_d( $mixed ) {
	echo '<pre>';
	var_dump($mixed);
	echo '</pre>';
}

/**
 * Convenience method
 */
function die_r( $mixed ) {
	// workaround for http://pecl.php.net/bugs/bug.php?id=16721
	// Credit: popthestack
	if ( isset($_SESSION) ) {
		session_write_close();
	}

	echo_r($mixed);
	die;
}

function die_d( $mixed ) {
	// workaround for http://pecl.php.net/bugs/bug.php?id=16721
	// Credit: popthestack
	if ( isset($_SESSION) ) {
		session_write_close();
	}

	echo_d($mixed);
	die;
}

/**
 * Convenience method
 */
function big( $mixed ) {
	echo '<h1>';
	print_r($mixed);
	echo '</h1>';
}

/**
 * almost_empty function.
 *
 * Like empty(), returns true if input evaluates to 0, '', an empty array, or null.
 * In addition, almost_empty will return true if input is an n-deep array or object with
 * only 'empty' values.
 *
 * Optionally, pass a second $trim param. If this param is true, almost_empty will trim
 * any strings it finds, returning true if $in contains only white space.
 *
 * This function (almost_empty) is from the Zoop framework. http://zoopframework.com/
 *
 * This function (almost_empty) is covered under the Zoop Framework License:
 * 2006 Supernerd LLC and Contributors. All Rights Reserved.
 * This software is subject to the provisions of the Zope Public License, Version 2.1 (ZPL)
 *
 * @copyright 2006 Supernerd LLC and Contributors. All Rights Reserved.
 * @license Documentation/Packaged Software Licenses/Zoop (http://zoopframework.com/license)
 * @see empty
 * @access public
 * @param mixed $in
 * @param bool $trim
 * @return bool
 */
function almost_empty($in, $trim = false) {
	if ($trim && is_string($in)) {
		$in = trim($in);
	}

	if ( empty( $in ) ) {
		return true;
	} else if (is_array($in) || is_object($in)) {
		foreach ((array)$in as $_val) {
			if (!almost_empty($_val)) return false;
		}
		return true;
	} else {
		return false;
	}
}
