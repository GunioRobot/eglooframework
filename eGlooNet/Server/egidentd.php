<?php
/**
 * eGloo Ident Daemon File 
 *
 * This file contains the daemon for the eGloo ident service
 * 
 * Copyright 2008 eGloo, LLC
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package eGlooNet
 * @version 1.0
 */
    
    include( '../Support/autoload.php' );
    include( '../Support/error_handlers.php' );
    
    // Setup the logger
    eGlooLogger::setLoggingLevel( eGlooLogger::$DEVELOPMENT );
    eGlooLogger::setLoggingType( eGlooLogger::$LOG_LOG );
    
    // we need to make sure we can id a particular request path in the logs since
    // multiple instances can be run out of order
    set_error_handler( 'default_error_handler' );
    set_exception_handler( 'default_exception_handler' );

    $authServerConnectionManager = ServerConnectionManagerFactory::getAuthenticationServerConnectionManager();
	
    while ( true === true ) {
    	eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Listening for clients", eGlooLogger::$EGLOOIDENTD );
	    
    	$authServerConnectionManager->listen();
	    
	    eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Checking if client found", eGlooLogger::$EGLOOIDENTD );

	    if ( $authServerConnectionManager->clientConnected() ) {
	    	eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Client found, authenticating", eGlooLogger::$EGLOOIDENTD );
	    	
	    	$authServerConnectionManager->authenticateClient();
	    	
	    	eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Client authenticated", eGlooLogger::$EGLOOIDENTD );
	    } else {
	    	eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Client not connected", eGlooLogger::$EGLOOIDENTD );
	    	// TODO error checking
	    }
	    
    }

//    socket_close( $mysock );
	
	eGlooLogger::writeLog( eGlooLogger::$DEBUG, "Sockets closed, server shutting down...", eGlooLogger::$EGLOOIDENTD );
?>