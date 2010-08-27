<?php
/**
 * Default Exception and Error Handlers 
 *
 * This file contains the function definitions for the default_exception_handler 
 * and default_error_handler runtime handlers.
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
 * Defines the default exception runtime handler.
 * 
 * Long Description Goes Here
 * 
 * @param Exception $exception exception thrown
 * @todo Finish commenting
 */
function default_exception_handler( $exception ) {
    if ( class_exists( 'eGlooLogger' ) ) {
        eGlooLogger::writeLog( eGlooLogger::$EMERGENCY, 
                           '[File: ' . $_SERVER['SCRIPT_NAME'] . 
                           ' Line: ' . __LINE__ . '] Programmer ' .
                           'Error: Request Handler Threw Unknown Exception' .
                           '\n\t' . $exception->getMessage() );    
    } else {
        
    }
    
    exit;
}

/**
 * Defines the default error runtime handler.
 * 
 * Long Description Goes Here
 * 
 * @todo Finish commenting
 */
function default_error_handler( $severity, $message, $filename, $linenum, $errcontext ) {
    $killRequest = false;

    switch( $severity ) {
        case E_USER_NOTICE :
            $levelString = 'USER NOTICE';
            break;
        case E_USER_WARNING :
            $levelString = 'USER WARNING';
            break;
        case E_USER_ERROR :
            $levelString = 'USER ERROR';
            $killRequest = true;
            break;
        case E_ERROR :
            $levelString = 'RUN-TIME ERROR';
            $killRequest = true;
            break;        
        case E_WARNING :
            $levelString = 'RUN-TIME WARNING';     // Non-fatal error
            break;
        case E_NOTICE :
            $levelString = 'RUN-TIME NOTICE';      // Could indicate an error
            break;
        case E_RECOVERABLE_ERROR :
            $levelString = 'RECOVERABLE ERROR';    // Recoverable error
            break;
        case E_STRICT :
            $levelString = 'STRICT NOTICE';        // Could indicate an error
            break;
        default :
            $levelString = 'UNKNOWN';
            break;
    }
    
    if ( class_exists( 'eGlooLogger' ) ) {
        // TODO this should go to a dispatch function of some kind
        if ( strpos( $message, 'Memcache' ) !== false ) {
            eGlooLogger::writeLog( eGlooLogger::$EMERGENCY, $levelString . ' ' . 
                                          $message . ' ' . $filename . ' ' . $linenum, 'Memcache' );
        } else {
            eGlooLogger::writeLog( eGlooLogger::$EMERGENCY, $levelString . ' ' . 
                                          $message . ' ' . $filename . ' ' . $linenum );            
        }
        if ( $killRequest === true ) {
            exit;
        }
    } else {
        
    }
}


?>
