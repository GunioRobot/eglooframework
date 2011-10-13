<?php
namespace eGloo;

use \ErrorException as ErrorException;

/**
 * eGloo\Logger Class File
 *
 * Contains the class definition for the eGloo\Logger, a final class for 
 * eGloo framework logging functionality.
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
 * @category System
 * @package Utilities
 * @subpackage Logging
 * @version 1.0
 */

/**
 * eGloo\Logger is responsible for handling all system logging for the eGloo web 
 * application.	 It contains static constants for specifying levels of logging
 * and the type of notice of any particular logged message.	 Messages are evaluated
 * to determine if their notice level matches the current logging level of the system
 * and, if so, are tagged with the date, time, notice level, message and additional
 * output required based on the notice level of the message and any additional
 * information passed in via an array.
 * 
 * All messages are written to the log path defined in Configuration::getLoggingPath().
 * The logging path and the log file, "error.log", must both be writable by either the user
 * or the group which the apache (httpd) daemon belongs to.	 The eGloo directory must also
 * already exist; the logger will not create the required path.
 *
 * @author George Cooper
 * @author Thomas Patrick Read I ("Red Tom")
 * @category System
 * @package Utilities
 * @subpackage Logging
 */
final class Logger {
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

	const LOG_LOG		= 0x0;	// write to error.log
	const LOG_HTML		= 0x2;	// write to error.html
	const LOG_XML		= 0x4;	// write to error.xml

	// Attributes
	private static $aggregateApplicationLogs = true;
	private static $loggingLevel;
	private static $loggingType = self::LOG_LOG;	//set to log by default
	private static $requestID = '';
	private static $requestDate = null;
	private static $showErrors = false;
	private static $timezone = 'America/New_York';

	// Maps log level bitmasks to the appropriate strings
	private static $logLevelStrings = null;

	public static function initialize( $level, $format ) {
		self::$requestDate = date( 'Y-m-d' );

		$num = mt_rand ( 0, 0xffffff );
		self::$requestID = sprintf ( "%06x", $num );
		
		self::setLoggingLevel( $level );
		self::setLoggingType( $format );

		set_error_handler( array('\eGloo\Logger', 'global_error_handler') );
		set_exception_handler( array('\eGloo\Logger', 'global_exception_handler') );

		if ( !defined('STDIN') ) {
			// TODO fix this so it actually says the actual level
			self::writeLog( self::INFO, 'Logger Started [Mode: DEVELOPMENT]', 'Logger' );
		}
	}

	public static function setLoggingLevel( $level ) {
		self::$loggingLevel = $level;


		self::$logLevelStrings = array( self::EMERGENCY => "EMERGENCY", self::ALERT => "ALERT",
										self::CRITICAL => "CRITICAL",	self::ERROR => "ERROR",
										self::WARN => "WARNING",		self::NOTICE => "NOTICE",
										self::INFO => "INFO",			self::DEBUG => "DEBUG",
										self::LOG_OFF => "OFF",			self::PRODUCTION => "DEPLOYMENT",
										self::STAGING => "TESTING",		self::DEVELOPMENT => "DEVELOPMENT" );
	}

	public static function getLoggingLevel() {
		return self::$loggingLevel;
	}
	
	 /**
	 * Sets the logging type, either default log file, HTML, or XML
	 * NOTE: DO NOT use XML yet.
	 * 
	 * @param $type		the type to set, either "log", "html", or "xml"
	 * @returns null
	 */
	 public static function setLoggingType($type){
		self::$loggingType = $type;
		//log is the default
		
		//Create the HTML headers, and change the logfile to an HTML file.
		if($type == self::LOG_HTML){
			self::$logFilePath = self::$htmlFilePath;
			$header = '<html><body BGCOLOR="#000000">';
			if ( (file_put_contents( self::$logFilePath, $header . "\n", FILE_APPEND ) ) === false ) {
				throw new Logger\Exception( 'Error writing to log' );
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
	 * @param $level					a bitmask (hexadecimal int) that represents the logging 
	 *									level specified
	 * @param $message					the message to be printed to the log file
	 * @param $data						an optional array containing any other information that 
	 *									should be printed along with the given message
	 * @param $timezone					an additional timezone to display in the logs, in case
	 *									developers are not in the same timezone as their servers
	 * @throws eGloo\Logger\Exception		if there was an error writing to the log file
	 * @returns null 
	 */
	public static function writeLog( $level, $message, $logPackage = 'Default', $data = null, $timezone = 'America/New_York', $aggregateApplicationLogs = true ) {
		if ( $level & self::$loggingLevel ) {
			// Make sure we're running an eGloo Web App before actually logging to the web logs
			if ( !defined('STDIN') ) {
				$dateTime = new DateTime( 'now' );
				$server_local_time = $dateTime->format( 'Y.m.d h:i:sa T' );

				$dateTime->setTimezone( new DateTimeZone( $timezone ) );
				$requested_local_time = $dateTime->format( 'Y.m.d h:i:sa T' );

				$message = '[RequestID:' . self::$requestID . '] ' . $server_local_time . ' / ' . $requested_local_time . ' [' . 
							self::$logLevelStrings[$level] . '] ' . $message;

				$message = wordwrap( $message, 120, "\n\t" );

				$log_path = Configuration::getLoggingPath() . '/' . self::$requestDate;

				if ( $aggregateApplicationLogs && preg_match( '~^[a-zA-Z0-9.]+$~', Configuration::getApplicationName() ) ) {
					$log_path .= '/Applications/' . Configuration::getApplicationName();
				}

				if ( !is_writable( $log_path ) ) {
					try {
						if ( Configuration::getDeployment() === Configuration::DEVELOPMENT ) {
							$mode = 0755;
						} else {
							$mode = 0750;
						}

						$recursive = true;

						mkdir( $log_path, $mode, $recursive );
					} catch (Exception $e){
						echo_r($e->getMessage());
					}
				}

				//Default, write to error.log
				if( self::$loggingType == self::LOG_LOG){
					if ( (file_put_contents( $log_path . '/' . $logPackage . '.log', $message . "\n", FILE_APPEND ) ) === false ) {
						throw new Logger\Exception( 'Error writing to log' );
					}
				}
			} else {
				echo $message . "\n";
			}
		}
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
		if ( !defined('STDIN') ) {
			$exceptionType = get_class($exception);

			$dump_prefix = time() . '.' . self::$requestID;

			$cookie = 'cookie.log';
			$files = 'files.log';
			$server = 'server.log';
			$session = 'session.log';
			$trace = 'trace.log';

			$dump_file_path = Configuration::getLoggingPath() . '/' . self::$requestDate;

			if ( self::$aggregateApplicationLogs ) {
				$dump_file_path .= '/Applications/' . Configuration::getApplicationName() . '/Dumps/' . $dump_prefix;
			} else {
				$dump_file_path .= '/Dumps/' . $dump_prefix;
			}

			if ( !is_writable( $dump_file_path ) ) {
				try {
					if ( Configuration::getDeployment() === Configuration::DEVELOPMENT ) {
						$mode = 0755;
					} else {
						$mode = 0750;
					}

					$recursive = true;

					mkdir( $dump_file_path, $mode, $recursive );
				} catch (Exception $e){
					echo_r($e->getMessage());
				}
			}

			// TODO have options to be able to log information per log call, like session. That way we can log stuff if needed, but not default
			// to possibly revealing confidential info
			file_put_contents( $dump_file_path . '/' . $server, print_r( $_SERVER, true ) );
			file_put_contents( $dump_file_path . '/' . $trace, $exception->getTraceAsString() );

			if ( isset( $_COOKIE ) && !empty( $_COOKIE ) ) {
				file_put_contents( $dump_file_path . '/' . $cookie, print_r( $_COOKIE, true ) );
			}

			if ( isset( $_FILES ) && !empty( $_FILES ) ) {
				file_put_contents( $dump_file_path . '/' . $files, print_r( $_FILES, true ) );
			}

			if ( isset( $_SESSION ) ) {
				file_put_contents( $dump_file_path . '/' . $session, print_r( $_SESSION, true ) );
			}

			if ( class_exists( 'RequestInfoBean' ) ) {
				$rib = 'rib.log';

				$requestInfoBean = RequestInfoBean::getInstance();

				file_put_contents( $dump_file_path . '/' . $rib, print_r( $requestInfoBean, true ) );
			} else if ( isset( $_GET ) && isset( $_POST ) ) {
				$get = 'get.log';
				$post = 'post.log';

				file_put_contents( $dump_file_path . '/' . $get, print_r( $_GET, true ) );
				file_put_contents( $dump_file_path . '/' . $post, print_r( $_POST, true ) );
			}

			// TODO record request ID in trace / log

			// TODO determine how to handle multiple app logging -- branching folders or unique trace hashes
			// Should probably be a deployment option

			// Use eGlooHTTPRequest stuff... if it exists
			$not_found = 'Not found in HTTP headers';

			$request_uri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : $not_found;
			$redirect_query_string = isset($_SERVER['REDIRECT_QUERY_STRING']) ? $_SERVER['REDIRECT_QUERY_STRING'] : $not_found;

			$http_host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $not_found;
			$http_user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : $not_found;

			$http_referer = isset( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $not_found;

			$http_accept = isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : $not_found;
			$http_accept_charset = isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : $not_found;
			$http_accept_encoding = isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : $not_found;
			$http_accept_language = isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : $not_found;

			$http_cookie = isset($_SERVER['HTTP_COOKIE']) ? $_SERVER['HTTP_COOKIE'] : $not_found;
			$http_cache_control = isset($_SERVER['HTTP_CACHE_CONTROL']) ? $_SERVER['HTTP_CACHE_CONTROL'] : $not_found;

			$server_address = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $not_found;
			$server_name = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : $not_found;
			$server_port = isset($_SERVER['SERVER_PORT']) ? $_SERVER['SERVER_PORT'] : $not_found;

			$remote_address = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : $not_found;
			$remote_port = isset($_SERVER['REMOTE_PORT']) ? $_SERVER['REMOTE_PORT'] : $not_found;

			$using_ssl = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== '' ? 'Yes' : 'No';

			$request_domain = null;

			// Partially to make sure this is something sane, partially to check for spoofing
			if ( $http_host !== $not_found && preg_match( '~^[a-zA-Z0-9.:]*$~', $http_host ) && strpos( $http_host, $server_name) !==false ) {
				$request_domain = $http_host;
			}

			$request_port = null;

			// Partially to make sure this is something sane, partially to check for spoofing
			if ( $server_port !== $not_found && preg_match( '~^[0-9]+$~', $server_port ) ) {
				$request_port = $server_port !== '80' ? ':' . $server_port : '';
			}

			$request_url = null;

			if ( $request_domain !== null && $request_port !== null ) {
				$request_protocol = isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] !== '' ? 'https://' : 'http://';

				if ( $request_port === '' || ($request_port !== '' && !strpos($request_domain, $request_port)) ) {
					$request_url = $request_protocol . $request_domain . $request_port . $request_uri;
				} else {
					$request_url = $request_protocol . $request_domain . $request_uri;
				}
			} else {
				$request_url = 'Could not be reliably constructed from HTTP headers';
			}

			$log_output = 'Programmer Error: Uncaught exception of type "' . $exceptionType . '"' .
				"\n\n\t" . 'Application: ' . Configuration::getApplicationName() .
				"\n\t" . 'InterfaceBundle: ' . Configuration::getUIBundleName() .
				"\n\n\t" . 'Request URI: ' . $request_uri .
				"\n\t" . 'Redirect Query String: ' . $redirect_query_string .
				"\n\t" . 'Request URL: ' . $request_url .
				"\n\n\t" . 'Exception caught by global exception handler on line ' . __LINE__ . ' in file: ' . $_SERVER['SCRIPT_NAME'] .
				"\n\t" . 'Exception Message: ' . $exception->getMessage() .
				"\n\n\t" . 'See dump files under "' . $dump_file_path . '" for details' .
				"\n\n\t" . 'HTTP Host: ' . $http_host .
				"\n\t" . 'HTTP User-Agent: ' . $http_user_agent .
				"\n\t" . 'HTTP Referrer: ' . $http_referer .
				"\n\n\t" . 'HTTP Accept: ' . $http_accept .
				"\n\t" . 'HTTP Accept-Charset: ' . $http_accept_charset .
				"\n\t" . 'HTTP Accept-Encoding: ' . $http_accept_encoding .
				"\n\t" . 'HTTP Accept-Language: ' . $http_accept_language .
				"\n\n\t" . 'HTTP Cookie: ' . $http_cookie .
				"\n\t" . 'HTTP Cache-Control: ' . $http_cache_control .
				"\n\n\t" . 'Remote IP: ' . $remote_address .
				"\n\t" . 'Remote Port: ' . $remote_port .
				"\n\n\t" . 'Server Name: ' . $server_name .
				"\n\t" . 'Server IP: ' . $server_address .
				"\n\t" . 'Server Port: ' . $server_port .
				"\n\t" . 'Using SSL: ' . $using_ssl;

			self::writeLog( self::EMERGENCY, $log_output, 'Default', null, self::$timezone, self::$aggregateApplicationLogs );

			$output_header = "<font size='1'>" .
				"<table dir='ltr' border='1' cellspacing='0' cellpadding='1'>" .
				"<tr><th align='left' bgcolor='#f57900' colspan='5'>" .
				"<span style='background-color: #cc0000; color: #fce94f; font-size: x-large;'>( ! )</span>&nbsp;" .
				'A fatal error has occurred.  Please see the Default.log file for ' .
				self::$requestDate . '.	 Request ID: ' . self::$requestID . "&nbsp;</th></tr></table></font>";

			if ( (self::DEVELOPMENT & self::$loggingLevel) && Configuration::getDisplayErrors() ) {
				echo_r( $output_header );
			}

			$html_output = '<br />' . '<b>Programmer Error:</b> Uncaught exception of type "' . $exceptionType . '"' .
				"<br /><br />" . '<b>Application:</b> ' . Configuration::getApplicationName() .
				"<br />" . '<b>InterfaceBundle:</b> ' . Configuration::getUIBundleName() .
				'<br /><br />' . '<b>Request URI:</b> ' . $request_uri .
				'<br />' . '<b>Redirect Query String:</b> ' . $redirect_query_string .
				'<br />' . '<b>Request URL:</b> ' . $request_url .
				"<br /><br />" . '<b>Exception Message:</b> ' . $exception->getMessage() .
				'<br /><br />' . 'See dump files under <b>"' . $dump_file_path . '"</b> for details' .
				'<br /><br />' . '<b>HTTP Host:</b> ' . $http_host .
				'<br />' . '<b>HTTP User-Agent:</b> ' . $http_user_agent .
				'<br />' . '<b>HTTP Referrer:</b> ' . $http_referer .
				'<br /><br />' . '<b>HTTP Accept:</b> ' . $http_accept .
				'<br />' . '<b>HTTP Accept-Charset:</b> ' . $http_accept_charset .
				'<br />' . '<b>HTTP Accept-Encoding:</b> ' . $http_accept_encoding .
				'<br />' . '<b>HTTP Accept-Language:</b> ' . $http_accept_language .
				'<br /><br />' . '<b>HTTP Cookie:</b> ' . $http_cookie .
				'<br />' . '<b>HTTP Cache-Control:</b> ' . $http_cache_control .
				'<br /><br />' . '<b>Remote IP:</b> ' . $remote_address .
				'<br />' . '<b>Remote Port:</b> ' . $remote_port .
				'<br /><br />' . '<b>Server Name:</b> ' . $server_name .
				'<br />' . '<b>Server IP:</b> ' . $server_address .
				'<br />' . '<b>Server Port:</b> ' . $server_port .
				'<br />' . '<b>Using SSL:</b> ' . $using_ssl .
				'<br /><br /><b>Backtrace:</b><br />' .
				$exception->getTraceAsString();

			if ( (self::DEVELOPMENT & self::$loggingLevel) && Configuration::getDisplayTraces() ) {
				echo_r( $html_output );
			}

			foreach( Configuration::getAlerts() as $alert_id => $alert ) {
				if ( isset($alert['trigger']) && $alert['trigger'] === 'ErrorException' &&
					Configuration::getDeployment() !== Configuration::DEVELOPMENT ) {

					switch( strtolower($alert['type']) ) {
						case 'email' :
							$mail_to = $alert['value'];
							$subject = 'System Alert: Uncaught ' . $exceptionType;
							$message = str_replace( "\t", '', $log_output) . "\n\n" . 'Backtrace:' . "\n\n" . $exception->getTraceAsString();

							$mail_success = mail( $mail_to, $subject, $message );

							if ( $mail_success ) {
								$notice = 'Successfully sent uncaught exception email notification';

								self::writeLog( self::EMERGENCY, $notice, 'Default', null, self::$timezone, self::$aggregateApplicationLogs );
								if ( (self::DEVELOPMENT & self::$loggingLevel) && Configuration::getDisplayTraces() ) {
									echo_r( $notice );
								}
							} else {
								$notice = 'Did not successfully send email notification';

								if ( (self::DEVELOPMENT & self::$loggingLevel) && Configuration::getDisplayTraces() ) {
									echo_r( $notice );
								}
							}

							break;
						default :
							break;
					}
				}
			}

			// If we get an error, we should terminate this request immediately
			if (in_array($exception->getCode(), array(E_USER_ERROR, E_ERROR))) {
				exit;
			}
		} else {
			print_r( "\n\n" . $exception );

			// If we get an error, we should terminate this request immediately
			if (in_array($exception->getCode(), array(E_USER_ERROR, E_ERROR))) {
				exit;
			}
		}
	}

	public static function global_error_handler($severity, $message, $filename, $linenum, $context ) {
		throw new ErrorException($message, 0, $severity, $filename, $linenum);
	}

}

