<?php
/**
 * eGloo Framework Bootstrap File 
 *
 * This file contains the bootstrap for the eGloo framework
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
 * @package Bootstrap
 * @version 1.0
 */

// Check for the minimum PHP version to run the framework
if (version_compare(PHP_VERSION, '5.3.0', '<')) {
	echo 'You are using PHP version ' . PHP_VERSION . '.  ' .
		'eGloo requires PHP version 5.3.0 or higher.';
	exit;
} else {
	// Setup the OOP autoloader
	include( 'PHP/Includes/eGlooAutoload.php' );
}

// Check for Memcache
if (!extension_loaded('memcache') && !extension_loaded('memcached')) {
	echo 'Memcache support not detected.  Please install Memcache or Memcached for PHP.';
	exit;
}

// Build a request info bean
$requestInfoBean = RequestInfoBean::getInstance();

// Get a request validator based on the current application and UI bundle
$requestValidator =
	RequestValidator::getInstance( eGlooConfiguration::getApplicationPath(), eGlooConfiguration::getUIBundleName() );

if ( !$requestValidator->initializeInfoBean($requestInfoBean) ) {
	eGlooLogger::writeLog( eGlooLogger::EMERGENCY, 'Could not initialize request info bean', 'Security' );
	exit;
}

// Validate this request and update the info bean accordingly
$isValidRequest = $requestValidator->validateAndProcess( $requestInfoBean );

// If the request is valid, process it.  Otherwise, log it and die
if ( $isValidRequest ) {
	$requestProcessor = RequestProcessorFactory::getRequestProcessor( $requestInfoBean );
	$requestProcessor->processRequest();
} else {
	$errorRequestProcessor = RequestProcessorFactory::getErrorRequestProcessor( $requestInfoBean );
	
	if ($errorRequestProcessor) {
		$errorRequestProcessor->processErrorRequest();
	} else {
		// We probably want to do something a bit more... elegant here.  Eventually
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'INVALID request!', 'RequestValidation', 'Security' );
	}
}

