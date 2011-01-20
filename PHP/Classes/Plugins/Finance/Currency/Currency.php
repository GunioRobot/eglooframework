<?php
/**
 * Currency Class File
 *
 * $file_block_description
 * 
 * Copyright 2011 eGloo, LLC
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Finance
 * @subpackage Currency
 * @version 1.0
 */

/**
 * Currency
 *
 * $short_description
 *
 * $long_description
 *
 * @package Finance
 * @subpackage Currency
 */
abstract class Currency {
	/* Class Constants */

	/* Static Data Members*/

	/* Private Data Members */

	/* Protected Data Members */
	protected $_code = null;
	protected $_numeric_value = null;
	protected $_rounding_mode = null;
	protected $_rounding_precision = null;
	protected $_sign = null;

	/* Public Data Members */

	public function __construct( $numeric_value ) {
		$this->_numeric_value = $numeric_value;
	}

	public function getCode() {
		return $this->_code;
	}

	public function setCode( $code ) {
		$this->_code = $code;
	}

	public function getNumericValue() {
		return $this->_numeric_value;
	}

	public function setNumericValue( $numeric_value ) {
		$this->_numeric_value = $numeric_value;
	}

	public function getRoundingMode() {
		return $this->_rounding_mode;
	}

	public function setRoundingMode( $rounding_mode ) {
		$this->_rounding_mode = $rounding_mode;
	}

	public function getRoundingPrecision() {
		return $this->_rounding_precision;
	}

	public function setRoundingPrecision( $rounding_precision ) {
		$this->_rounding_precision = $rounding_precision;
	}

	public function getSign() {
		return $this->_sign;
	}

	public function setSign( $sign ) {
		$this->_sign = $sign;
	}

	public function __toString() {
		return $this->_sign . round($this->_numeric_value, $this->_rounding_precision, $this->_rounding_mode);
	}

	public function __destruct() {
		
	}

	public static function getInstance( $valueInCurrency = 0 ) {
		return new static( $valueInCurrency );
	}

	public static function getInstanceFromUSD( $valueInUSD ) {
		$retVal = null;

		if ($valueInUSD instanceof USDollarCurrency) {
			$retVal = new static( $valueInUSD->getNumericValue(), PHP_ROUND_HALF_UP, 2, CurrencyExchange::USD );
		} else {
			$retVal = new static( $valueInUSD );
		}

		return $retVal;
	}

}

