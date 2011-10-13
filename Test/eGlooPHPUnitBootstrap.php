<?php
namespace eGloo;

/**
 * eGloo Framework PHPUnit Control Script
 *
 * This file contains the control script for PHPUnit testing with the eGloo framework
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
 * @package Scripts
 * @version 1.0
 */

// Check for the minimum PHP version to run the framework
if ( version_compare(PHP_VERSION, '5.3.0', '<') ) {
	echo 'You are using PHP version ' . PHP_VERSION . '.  ' .
		'eGloo requires PHP version 5.3.0 or higher.';
	exit;
}

// Check for Memcache
if ( !extension_loaded('memcache') && !extension_loaded('memcached') ) {
	echo 'Memcache support not detected.  Please install Memcache or Memcached for PHP.';
	exit;
}

// Let's grab the arguments that are directed at the script execution (not command/subcommand)
// This would be anything starting with a dash (-) or double dash (--) before the first argument
// which does not contain one (which should be either local, or some command such as data or forms)
$egloo_script_arguments = array();

$path_set = false;

if ( !$path_set && isset( $_SERVER['EGLOO_INCLUDE_PATH'] ) ) {
	$full_path = $_SERVER['EGLOO_INCLUDE_PATH'] . '/PHP/Includes/eGlooPHPUnitAutoload.php';

	if ( file_exists($full_path) && is_file($full_path) && is_readable($full_path) ) {
		set_include_path( $_SERVER['EGLOO_INCLUDE_PATH'] . ':' . get_include_path() );
		$path_set = true;
	} else {
		echo 'EGLOO_INCLUDE_PATH value of "' . $_SERVER['EGLOO_INCLUDE_PATH'] . '" is not a valid eGloo Framework path.  Ignoring...' . "\n";
	}
}

if ( !$path_set ) {
	$supported_operating_systems = array( 'Mac OS X' => array('Darwin'), 'Ubuntu' => array('Linux') );

	if ( in_array( PHP_OS, $supported_operating_systems['Mac OS X'] ) ) {
		set_include_path( '/Library/Frameworks/eGloo.framework' . ':' . get_include_path() );
		$path_set = true;
	} else if ( in_array( PHP_OS, $supported_operating_systems['Ubuntu'] ) ) {
		set_include_path( '/usr/lib/eglooframework' . ':' . get_include_path() );
		$path_set = true;
	} else {
		echo 'eGloo CLI OS does not support this operating system.  Looking for eGloo Framework in Linux default...' . "\n";
		set_include_path( '/usr/lib/eglooframework' . ':' . get_include_path() );
		$path_set = true;
	}
}

// We have an include path, so let's attempt to include
if ( $path_set ) {
	include( 'PHP/Includes/eGlooPHPUnitAutoload.php' );
}

// Let's make sure we loaded something here
if ( !class_exists('\eGloo\CLI') ) {
	echo 'eGloo Framework not found in expected location.' . "\n\n";
	echo 'Please provide a valid include path one of two ways:' . "\n";
	exit;
}
