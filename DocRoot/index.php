<?php
/**
 * eGloo Framework Bootstrap File 
 *
 * This file contains the bootstrap for the eGloo framework
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
 * @package Bootstrap
 * @version 1.0
 */

    //apd_set_pprof_trace();
    
    include( '../PHP/autoload.php' );
    include( '../PHP/error_handlers.php' );
    
    // Setup the logger
    eGlooLogger::setLoggingLevel( eGlooLogger::$DEVELOPMENT );
    eGlooLogger::setLoggingType( eGlooLogger::$LOG_LOG );
    
    // we need to make sure we can id a particular request path in the logs since
    // multiple instances can be run out of order
    set_error_handler( 'default_error_handler' );
    set_exception_handler( 'default_exception_handler' );
	header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
	
    /*
     * Initialize the session object
     */
    $sessionHandler = new SessionHandler();
    session_start();
    
    //This is to get IE to accept cookies with its new security model
    //TODO move session management to a decorator so javascript/css calls don't have sessions...
	setcookie(session_name(), session_id(), time()+60*60*24*30, '/');
    /**
     * 1) create a request info bean to be filled by the request validator,
     * 	  if the request is valid
     * 2) make a new request validator, and then validate the request
     * 3) if the request is valid, obtain the appropriate request processor
     * 4) process the request
     */

    $webapp = 'eGloo';
    $uibundle = 'OverlayInterface';

//    if ( !isset($_ENV['EG_DEPLOY']) || ( $_ENV['EG_DEPLOY'] !== 'Prod' && 
//		$_ENV['EG_DEPLOY'] !== 'Test' && $_ENV['EG_DEPLOY'] !== 'DevFast' && $_ENV['EG_DEPLOY'] !== 'Dev' ) ) {
//    	$_ENV['EG_DEPLOY'] = 'Dev';
//    }
    
    $requestInfoBean = new RequestInfoBean();
    $requestValidator = RequestValidator::getInstance( $webapp, $uibundle );
    $isValidRequest = $requestValidator->validateAndProcess( $requestInfoBean );
    	
    if ( $isValidRequest ) {
	   $requestProcessor = RequestProcessorFactory::getRequestProcessor( $requestInfoBean );
       $requestProcessor->processRequest();
    } else {
	   eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'INVALID request!', 'RequestValidation', 'Security' );		
    }

?>