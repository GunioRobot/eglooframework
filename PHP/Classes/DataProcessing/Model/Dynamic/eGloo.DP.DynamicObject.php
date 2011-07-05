<?php
/**
 * eGloo\DP\DynamicObject Class File
 *
 * Contains the class definition for the eGloo\DP\DynamicObject
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

namespace eGloo\DP;

/**
 * eGloo\DP\DynamicObject
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class DynamicObject {

	protected $_managed_members = array();

	public function __construct() {
		
	}

	// public function __clone() {
	// 	
	// }

	public function __call( $name, $arguments ) {
		$log_message = 'Mock Method Call: Attempted invocation of concrete method "' .
			$name . '" on object of type "' . get_class($this) . '"' . "\n\t" .
			count($arguments) . ' method argument(s) provided: ';

		foreach( $arguments as $argument ) {
			$log_message .= '(' . gettype($argument) . ') ' . $argument . ' ';
		}

		\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );
	}

	public static function __callStatic( $name, $arguments ) {
		$log_message = 'Mock Static Method Call: Attempted invocation of concrete static method "' .
			$name . '" on class of type "' . get_called_class() . '"' . "\n\t" .
			count($arguments) . ' method argument(s) provided: ';

		foreach( $arguments as $argument ) {
			$log_message .= '(' . gettype($argument) . ') ' . $argument . ' ';
		}

		\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );
	}

	// public function __invoke() {
	// 	
	// }

	public function __get( $key ) {
		$retVal = null;

		if ( isset($this->_managed_members[$key]) ) {
			$retVal = $this->_managed_members[$key];
		} else {
			$log_message = 'Mock Member Read: Attempted read of concrete member "' .
				$key . '" on object of type "' . get_class($this) . '"';

			\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );
		}

		return $retVal;
	}

	public function __isset( $key ) {
		$retVal = false;

		if ( isset($this->_managed_members[$key]) && $this->_managed_members[$key]['value'] !== null ) {
			$retVal = true;
		} else {
			$log_message = 'Mock Member isset() or empty(): Attempted isset() or empty() check of concrete member "' .
				$key . '" on object of type "' . get_class($this) . '"';

			\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );
		}

		return $retVal;
	}

	public function __set( $key, $value ) {
		if ( isset($this->_managed_members[$key]) ) {
			$this->_managed_members[$key]['value'] = $value;
		} else {
			$log_message = 'Mock Member Write: Attempted write of concrete member "' .
				$key . '" on object of type "' . get_class($this) . '"' . "\n\t" . 'Write Value: ';

			if ( !is_object($value) && !is_array($value) ) {
				$log_message .= $value;
			} else {
				$log_message .= var_export( $value, true );
			}

			\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );
		}
	}

	public function __unset( $key ) {
		if ( isset($this->_managed_members[$key]) ) {
			$this->_managed_members[$key]['value'] = null;
		} else {
			$log_message = 'Mock Member unset(): Attempted unset() of concrete member "' .
				$key . '" on object of type "' . get_class($this) . '"';

			\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );
		}
	}

	// public function __set_state() {
	// 	
	// }
	// 
	// public function __sleep() {
	// 	
	// }
	// 
	// public function __wakeup() {
	// 	
	// }

	public function __toString() {
		$retVal = '';

		$log_message = 'Mock Method toString(): Attempted invocation of concrete method "__toString' .
			'" on object of type "' . get_class($this) . '"';

		\eGlooLogger::writeLog( \eGlooLogger::EMERGENCY, $log_message, 'DDPNS' );

		return $retVal;
	}

	public function __destruct() {
		
	}

}

