<?php
/**
 * eGlooResponseTransaction Class File
 *
 * $file_block_description
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * eGlooResponseTransaction
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
abstract class eGlooResponseTransaction {

	public static $numberOfResponseTransactionsInFlight = 0;

	private $_dataPackage = null;
	private $_destination = null;
	private $_source = null;

	public function __construct( $dataPackage = null, $source = null, $destination = null ) {
		$this->_dataPackage = $dataPackage;
		$this->_source = $source;
		$this->_destination = $destination;

		self::$numberOfResponseTransactionsInFlight += 1;
	}

	public function getDataPackage() {
		return $this->_dataPackage;
	}

	public function setDataPackage( $dataPackage ) {
		// TODO make some better choices depending upon the type we get here
		$this->_dataPackage = $dataPackage;
	}

	public function getDataPackageString() {
		$retVal = null;

		if ( !is_string($this->_dataPackage) ) {
			// TODO
		} else {
			$retVal = $this->_dataPackage;
		}

		return $retVal;
	}

	public function getDestination() {
		return $this->_destination;
	}

	public function setDestination( $destination ) {
		$this->_destination = $destination;
	}

	public function getSource() {
		return $this->_source;
	}

	public function setSource( $source ) {
		$this->_source = $source;
	}

	public static function getNumberOfResponseTransactionsInFlight() {
		return self::$numberOfResponseTransactionsInFlight;
	}

}

