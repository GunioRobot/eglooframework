<?php
namespace eGloo\Messaging;

use \eGloo;
use \eGloo\Configuration as Configuration;
use \eGloo\Utility\Logger as Logger;

use \Exception as Exception;

/**
 * eGloo\Messaging\Alert Class File
 *
 * Contains the class definition for the eGloo\Messaging\Alert
 * 
 * Copyright 2011 eGloo LLC
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
 * @copyright 2011 eGloo LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category System
 * @package Messaging
 * @subpackage Alerts
 * @version 1.0
 */

/**
 * eGloo\Messaging\Alert
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Messaging
 * @subpackage Alerts
 */
class Alert {

	public static function sendAlert() {
		
	}

	public static function sendEmailAlert( $subject = '', $message = '', $trigger_type = null ) {
		foreach( Configuration::getAlerts() as $alert_id => $alert ) {
			if ( !$trigger_type ) {
				switch( strtolower($alert['type']) ) {
					case 'email' :
						$mail_to = $alert['value'];
						$mail_subject = 'eGloo Alert: ' . $subject;

						if ( mail( $mail_to, $mail_subject, $message ) ) {
							Logger::writeLog( Logger::DEBUG, 'eGloo Alert: Successfully sent email notification', 'Mail' );
						} else {
							Logger::writeLog( Logger::EMERGENCY, 'eGloo Alert: Did not successfully send email notification', 'Mail' );
						}

						break;
					default :
						break;
				}
			} else if ( isset($alert['trigger']) && $alert['trigger'] === $tigger_type ) {
				switch( strtolower($alert['type']) ) {
					case 'email' :
						$mail_to = $alert['value'];
						$mail_subject = 'eGloo Alert: ' . $subject;

						if ( mail( $mail_to, $mail_subject, $message ) ) {
							Logger::writeLog( Logger::DEBUG, 'eGloo Alert: Successfully sent email notification', 'Mail' );
						} else {
							Logger::writeLog( Logger::EMERGENCY, 'eGloo Alert: Did not successfully send email notification', 'Mail' );
						}

						break;
					default :
						break;
				}
			}
		}

	}

	public static function sendSMSAlert() {
		
	}

}

