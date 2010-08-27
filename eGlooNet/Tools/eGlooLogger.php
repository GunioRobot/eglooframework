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
 * All messages are written to /var/log/eGloo/error.log - the directory /var/log/eGloo
 * and the log file, "error.log", must both be writable by either the user or the group
 * which the apache (httpd) daemon belongs to.  The eGloo directory must also already 
 * exist; the logger will not create the required path.
 *
 * @author George Cooper
 * @author Thomas Patrick Read I		(loggingType)
 */
final class eGlooLogger {
    // Class Constants
    public static $EMERGENCY = 0x80;     // 1000 0000
    public static $ALERT = 0x40;         // 0100 0000
    public static $CRITICAL = 0x20;      // 0010 0000
    public static $ERROR = 0x10;         // 0001 0000
    public static $WARN = 0x8;           // 0000 1000
    public static $NOTICE = 0x4;         // 0000 0100
    public static $INFO = 0x2;           // 0000 0010
    public static $DEBUG = 0x1;          // 0000 0001

    public static $LOG_OFF = 0x0;        // Do Not Log
    public static $DEPLOYMENT = 0xf8;    // Warning or higher
    public static $TESTING = 0xfe;       // Info or higher
    public static $DEVELOPMENT = 0xff;   // Log all

    // Packages
    public static $EGLOOIDENTD = 'eGlooIdentD';
    public static $SESSION = 'Session';
    public static $JAVASCRIPT = 'Javascript';
    public static $CSS = 'CSS';

	public static $LOG_LOG = "log";		// write to error.log
	public static $LOG_HTML = "html";	// write to error.html
	public static $LOG_XML = "xml";		// write to error.xml

    // Attributes
    private static $logFilePath = '/var/log/eGloo/error.log';
    private static $htmlFilePath = '/var/log/eGloo/error.html';
    private static $loggingLevel;
	private static $loggingType = "log";	//set to log by default
    private static $requestID = '';

    // Maps log level bitmasks to the appropriate strings
    private static $logLevelStrings = null;

    public static function setLoggingLevel( $level ) {
        self::$loggingLevel = $level;

        $num = mt_rand ( 0, 0xffffff );
        self::$requestID = sprintf ( "%06x" , $num );

        self::$logLevelStrings = array( self::$EMERGENCY => "EMERGENCY", self::$ALERT => "ALERT",
                                        self::$CRITICAL => "CRITICAL",   self::$ERROR => "ERROR",
                                        self::$WARN => "WARNING",        self::$NOTICE => "NOTICE",
                                        self::$INFO => "INFO",           self::$DEBUG => "DEBUG",
                                        self::$LOG_OFF => "OFF",         self::$DEPLOYMENT => "DEPLOYMENT",
                                        self::$TESTING => "TESTING",     self::$DEVELOPMENT => "DEVELOPMENT" );

        self::writeLog( self::$INFO, 'Logger Started [Mode: DEVELOPMENT]', 'Logger' );
    }
    
    
     /**
     * Sets the logging type, either default log file, HTML, or XML
     * NOTE: DO NOT use XML yet.
     * 
     * @param $type						the type to set, either "log", "html", or "xml"
     * @returns NULL
     */
     public static function setLoggingType($type){
     	self::$loggingType = $type;
     	//log is the default
     	
     	//Create the HTML headers, and change the logfile to an HTML file.
     	if($type == self::$LOG_HTML){
     		self::$logFilePath = self::$htmlFilePath;
     		$header = '<html><body BGCOLOR="#000000">';
     		if ( (file_put_contents( self::$logFilePath, $header . "\n", FILE_APPEND ) ) === false ) {
           		throw new eGlooLoggerException( 'Error writing to log' );
           	}
     	}
     	
     	//TODO make an XML schema for this thang
     	//DO NOT use yet. in fact, I'll just go ahead and comment it out...
     	/*if($type == self::$LOG_XML){
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
            
            $dateDir = date( 'Y-m-d' );
            
            if ( !is_writable( '/var/log/eGloo/' . $dateDir ) ) {
                mkdir( '/var/log/eGloo/' . $dateDir );
            }
            
            //Default, write to error.log
        	if( self::$loggingType == self::$LOG_LOG){
//            	if ( (file_put_contents( self::$logFilePath, $message . "\n", FILE_APPEND ) ) === false ) {
//               		throw new eGlooLoggerException( 'Error writing to log' );
//            	}
                if ( (file_put_contents( '/var/log/eGloo/' . $dateDir . '/' . $logPackage . '.log', $message . "\n", FILE_APPEND ) ) === false ) {
                    throw new eGlooLoggerException( 'Error writing to log' );
                }
            }
            
            //If HTML, write correct headers
            if( self::$loggingType == self::$LOG_HTML){
            	
	            //if Emergency, Alert, Critical, or Error
	            //write in red
	            if($level & self::$EMERGENCY OR $level & self::$ALERT OR $level & self::$CRITICAL OR $level & self::$ERROR){
	            	$message = '<font color="#FF0000">'.$message.'</font></body></html>';
	            	//print $message;
	            	if ( (file_put_contents( self::$logFilePath, $message . "<BR><BR>", FILE_APPEND ) ) === false ) {
	                	throw new eGlooLoggerException( 'Error writing to log' );
	            	}
	            }
	            
	            //if Warning or Notice
	            //write in yellow
	            if( $level & self::$NOTICE OR $level & self::$WARN){
	            	$message = '<font color="#FFFF00">'.$message.'</font></body></html>';
      	            if ( (file_put_contents( self::$logFilePath, $message . "<BR><BR>", FILE_APPEND ) ) === false ) {
	                	throw new eGlooLoggerException( 'Error writing to log' );
	            	}
	            }
	            
	            //if Debug or Info
	            //write in green
	            if($level & self::$INFO OR $level & self::$DEBUG){
	            	$message = '<font color="#00FF00">'.$message.'</font></body></html>';
          	        if ( (file_put_contents( self::$logFilePath, $message . "<BR><BR>", FILE_APPEND ) ) === false ) {
	                	throw new eGlooLoggerException( 'Error writing to log' );
	            	}
	            }
	            
            }//if html
            
            
        }//if correct logging level
    }

}

/**
 * Private exception subclass for use by eGlooLogger
 */
final class eGlooLoggerException extends Exception {

   /**
    * eGlooLoggerException constructor.  Takes a message and a code and invokes
    * the parent (Exception) constructor.  May eventaully contain additional code,
    * but for now acts as a means of determining the exact type of exception thrown
    * so it is possible to track down what threw it.
    *
    * @param $message   the message that this exception will contain
    * @param $code      the optional code of this exception (unused)
    * @returns          an eGlooLoggerException
    */
   public function __construct( $message, $code = 0 ) {
       // Call parent constructor
       parent::__construct( $message, $code );
   }
}

?>