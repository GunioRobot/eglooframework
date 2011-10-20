<?php
namespace eGloo\Daemon;

use \eGloo\Configuration as Configuration;
use \System_Daemon as System_Daemon;

/**
 * eGlooDaemon Class File
 *
 * Contains the class definition for the eGlooDaemon
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
 * @package Daemons
 * @version 1.0
 */

/**
 * eGlooDaemon
 *
 * $short_description
 *
 * $long_description
 *
 * @category System
 * @package Daemons
 */
abstract class Daemon {

	/**
	 * @var array Parsed options (key/value)
	 */
	protected $_parsed_options = null;

	public function __construct() {
		$this->prepareDaemonizer();
	}

	public function __destruct() {
		
	}

	protected function prepareDaemonizer() {
		$system_daemon_include_path = Configuration::getFrameworkRootPath() . '/Library/';
		$system_daemon_include_path .= 'PEAR/System/System_Daemon/System/Daemon.php';

		require_once( $system_daemon_include_path );
	}

	abstract public function start();

	abstract public function run();

	abstract public function pause();

	abstract public function stop();

	abstract public function kill();

	/**
	 * Returns protected class member $_parsed_options
	 *
	 * @return array Parsed options (key/value)
	 */
	public function getParsedOptions() {
		return $this->_parsed_options;
	}

	/**
	 * Sets protected class member $_parsed_options
	 *
	 * @param parsed_options array Parsed options
	 */
	public function setParsedOptions( $parsed_options ) {
		$this->_parsed_options = $parsed_options;
	}

}

deprecate( __FILE__, '\eGloo\Daemons\Daemon' );
