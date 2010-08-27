<?php
/**
 * eGlooLogger Class File
 *
 * Contains the class definition for the eGlooLogger, a final class for 
 * eGloo framework logging functionality.
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
 * @package Utilities
 * @version 1.0
 */

/**
 * eGlooLogger is responsible for handling all system logging for the eGloo web 
 * application.  It contains static constants for specifying levels of logging
 * and the type of notice of any particular logged message.  Messages are evaluated
 * to determine if their notice level matches the current logging level of the system
 * and, if so, are tagged with the date, time, notice level, message and additional
 * output required based on the notice level of the message and any additional
 * information passed in via an array.  
 * 
 * All messages are written to the log path defined in eGlooConfiguration::getLoggingPath().
 * The logging path and the log file, "error.log", must both be writable by either the user
 * or the group which the apache (httpd) daemon belongs to.  The eGloo directory must also
 * already exist; the logger will not create the required path.
 *
 * @author George Cooper
 * @author Thomas Patrick Read I ("Red Tom")
 */
final class eGlooLogger {
    // Class Constants
    const EMERGENCY		= 0x80;		// 1000 0000
    const ALERT			= 0x40;		// 0100 0000
    const CRITICAL		= 0x20;		// 0010 0000
    const ERROR			= 0x10;		// 0001 0000
    const WARN			= 0x8;		// 0000 1000
    const NOTICE		= 0x4;		// 0000 0100
    const INFO			= 0x2;		// 0000 0010
    const DEBUG			= 0x1;		// 0000 0001

    const LOG_OFF		= 0x0;		// Do Not Log
    const PRODUCTION	= 0xf8;		// Warning or higher
    const STAGING		= 0xfe;		// Info or higher
    const DEVELOPMENT	= 0xff;		// Log all

    // Packages
    const SESSION		= 'Session';
    const JAVASCRIPT	= 'Javascript';
    const CSS			= 'CSS';

	const LOG_LOG		= "log";	// write to error.log
	const LOG_HTML		= "html";	// write to error.html
	const LOG_XML		= "xml";	// write to error.xml

    // Attributes
	private static $loggingLevel;
	private static $loggingType = "log";	//set to log by default
	private static $requestID = '';
	private static $requestDate = null;
	private static $showErrors = false;

    // Maps log level bitmasks to the appropriate strings
    private static $logLevelStrings = null;

	public static function initialize( $level, $format ) {
		self::$requestDate = date( 'Y-m-d' );

		$num = mt_rand ( 0, 0xffffff );
		self::$requestID = sprintf ( "%06x" , $num );
		
		self::setLoggingLevel( $level );
		self::setLoggingType( $format );

		set_error_handler( array('eGlooLogger', 'global_error_handler') );
		set_exception_handler( array('eGlooLogger', 'global_exception_handler') );

        self::writeLog( self::INFO, 'Logger Started [Mode: DEVELOPMENT]', 'Logger' );
	}

    public static function setLoggingLevel( $level ) {
        self::$loggingLevel = $level;


        self::$logLevelStrings = array( self::EMERGENCY => "EMERGENCY", self::ALERT => "ALERT",
                                        self::CRITICAL => "CRITICAL",   self::ERROR => "ERROR",
                                        self::WARN => "WARNING",        self::NOTICE => "NOTICE",
                                        self::INFO => "INFO",           self::DEBUG => "DEBUG",
                                        self::LOG_OFF => "OFF",         self::PRODUCTION => "DEPLOYMENT",
                                        self::STAGING => "TESTING",     self::DEVELOPMENT => "DEVELOPMENT" );
    }

    public static function getLoggingLevel() {
        return self::$loggingLevel;
    }
    
     /**
     * Sets the logging type, either default log file, HTML, or XML
     * NOTE: DO NOT use XML yet.
     * 
     * @param $type		the type to set, either "log", "html", or "xml"
     * @returns NULL
     */
     public static function setLoggingType($type){
     	self::$loggingType = $type;
     	//log is the default
     	
     	//Create the HTML headers, and change the logfile to an HTML file.
     	if($type == self::LOG_HTML){
     		self::$logFilePath = self::$htmlFilePath;
     		$header = '<html><body BGCOLOR="#000000">';
     		if ( (file_put_contents( self::$logFilePath, $header . "\n", FILE_APPEND ) ) === false ) {
           		throw new eGlooLoggerException( 'Error writing to log' );
           	}
     	}

     	//TODO make an XML schema for this thang
     	//DO NOT use yet. in fact, I'll just go ahead and comment it out...
     	/*if($type == self::LOG_XML){
     		self::$logFilePath = 'error.xml';
     	}*/

     }

    /**
     * If the notice level of supplied message is set for the logger, prints time, notice level,
     * message and any additional output specified by the level of the notice or the data array
     * passed in.
     *
     * @param $level                   a bitmask (hexadecimal int) that represents the logging 
     *                                 level specified
     * @param $message                 the message to be printed to the log file
     * @param $data                    an optional array containing any other information that 
     *                                 should be printed along with the given message
     * @throws eGlooLoggerException    if there was an error writing to the log file
     * @returns NULL 
     */
    public static function writeLog( $level, $message, $logPackage = 'Default', $data = NULL ) {
       if ( $level & self::$loggingLevel ) {
        	
            $message = '[ReqID:' . self::$requestID . '] ' . date( 'Y.m.d h:i:sa T ' ) . '[' . 
                        self::$logLevelStrings[$level] . '] ' . $message;
			
            $message = wordwrap( $message, 120, "\n\t" );

            if ( !is_writable( eGlooConfiguration::getLoggingPath() . '/' . self::$requestDate ) ) {
                mkdir( eGlooConfiguration::getLoggingPath() . '/' . self::$requestDate );
            }
            
            //Default, write to error.log
        	if( self::$loggingType == self::LOG_LOG){
//            	if ( (file_put_contents( self::$logFilePath, $message . "\n", FILE_APPEND ) ) === false ) {
//               		throw new eGlooLoggerException( 'Error writing to log' );
//            	}
                if ( (file_put_contents( eGlooConfiguration::getLoggingPath() . '/' . self::$requestDate . '/' . $logPackage . '.log', $message . "\n", FILE_APPEND ) ) === false ) {
                    throw new eGlooLoggerException( 'Error writing to log' );
                }
            }
            
            //If HTML, write correct headers
            if( self::$loggingType == self::LOG_HTML){
            	
	            //if Emergency, Alert, Critical, or Error
	            //write in red
	            if($level & self::EMERGENCY OR $level & self::ALERT OR $level & self::CRITICAL OR $level & self::ERROR){
	            	$message = '<font color="#FF0000">'.$message.'</font></body></html>';
	            	//print $message;
	            	if ( (file_put_contents( self::$logFilePath, $message . "<BR><BR>", FILE_APPEND ) ) === false ) {
	                	throw new eGlooLoggerException( 'Error writing to log' );
	            	}
	            }
	            
	            //if Warning or Notice
	            //write in yellow
	            if( $level & self::NOTICE OR $level & self::WARN){
	            	$message = '<font color="#FFFF00">'.$message.'</font></body></html>';
      	            if ( (file_put_contents( self::$logFilePath, $message . "<BR><BR>", FILE_APPEND ) ) === false ) {
	                	throw new eGlooLoggerException( 'Error writing to log' );
	            	}
	            }
	            
	            //if Debug or Info
	            //write in green
	            if($level & self::INFO OR $level & self::DEBUG){
	            	$message = '<font color="#00FF00">'.$message.'</font></body></html>';
          	        if ( (file_put_contents( self::$logFilePath, $message . "<BR><BR>", FILE_APPEND ) ) === false ) {
	                	throw new eGlooLoggerException( 'Error writing to log' );
	            	}
	            }
	            
            }//if html
            
            
        }//if correct logging level
    }

	/**
	 * Defines the default exception runtime handler.
	 * 
	 * Long Description Goes Here
	 * 
	 * @param Exception $exception exception thrown
	 * @todo Finish commenting
	 */
	public static function global_exception_handler( $exception ) {
		$exceptionType = get_class($exception);

		$trace = time() . '.' . self::$requestID . '.eglootrace';

		if ( !is_writable( eGlooConfiguration::getLoggingPath() . '/' . self::$requestDate . '/Traces' ) ) {
			try {
				$mode = 0777;
				$recursive = true;

				mkdir( eGlooConfiguration::getLoggingPath() . '/' . self::$requestDate . '/Traces', $mode, $recursive );
			} catch (Exception $e){
				echo_r($e->getMessage());
			}
		}

		file_put_contents( eGlooConfiguration::getLoggingPath() . '/' . self::$requestDate . '/Traces/' . $trace, $exception->getTraceAsString() );

		// TODO determine how to handle multiple app logging -- branching folders or unique trace hashes
		// Should probably be a deployment option
		self::writeLog( self::EMERGENCY,
			'Programmer Error: Uncaught exception of type "' . $exceptionType . '"' .
			"\n\t" . 'Application: ' . eGlooConfiguration::getApplicationName() .
			"\n\t" . 'InterfaceBundle: ' . eGlooConfiguration::getUIBundleName() .
			"\n\n\t" . 'Exception caught by global exception handler on line ' . __LINE__ . ' in file: ' . $_SERVER['SCRIPT_NAME'] .
			"\n\t" . 'Exception Message: ' . $exception->getMessage() .
			"\n\n\t" . 'See trace file "' . $trace . '" for details');

		if ((self::DEVELOPMENT & self::$loggingLevel) && eGlooConfiguration::getDisplayErrors()) {
			echo_r(
				"<font size='1'>" .
				"<table dir='ltr' border='1' cellspacing='0' cellpadding='1'>" .
				"<tr><th align='left' bgcolor='#f57900' colspan='5'>" .
				"<span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span>&nbsp;" .
				'A fatal error has occurred.  Please see the Default.log file for ' .
				self::$requestDate . '.  Request ID: ' . self::$requestID . "&nbsp;</th></tr></table></font>"
			);
		}

		if ((self::DEVELOPMENT & self::$loggingLevel) && eGlooConfiguration::getDisplayTraces()) {
			echo_r(
				'<br />' . '<b>Programmer Error:</b> Uncaught exception of type "' . $exceptionType . '"' .
				"<br />" . '<b>Application:</b> ' . eGlooConfiguration::getApplicationName() .
				"<br />" . '<b>InterfaceBundle:</b> ' . eGlooConfiguration::getUIBundleName() .
				"<br /><br />" . '<b>Exception Message:</b> ' . $exception->getMessage() .
				'<br /><br /><b>Backtrace:</b><br />' .
				$exception->getTraceAsString()
			);
		}

		// If we get an error, we should terminate this request immediately
		if (in_array($exception->getCode(), array(E_USER_ERROR, E_ERROR))) {
			exit;
		}

	}

	public static function global_error_handler($severity, $message, $filename, $linenum, $context ) {
	    throw new ErrorException($message, 0, $severity, $filename, $linenum);
	}

	/**
	 * Defines the default error runtime handler.
	 * 
	 * Long Description Goes Here
	 * 
	 * @todo Finish commenting
	 */
	public static function default_error_handler( $severity, $message, $filename, $linenum, $errcontext ) {
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

		self::writeLog( self::EMERGENCY, $levelString . ' ' . 
		                              $message . ' ' . $filename . ' ' . $linenum );            

        if ( $killRequest === true ) {
            exit;
        }

	}

}

