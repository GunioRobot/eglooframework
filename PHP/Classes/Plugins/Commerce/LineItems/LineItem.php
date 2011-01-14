<?php
/**
 * LineItem Class File
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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * LineItem
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class LineItem {

	private $_valueInCurrency = null;

	public function __construct( $valueInCurrency = null, $preferred_currency = CurrencyExchange::USD ) {
		if ( isset($valueInCurrency) && $valueInCurrency instanceof Currency ) {
			$this->_valueInCurrency = $valueInCurrency;
		} else if (isset($valueInCurrency)){
			$preferredCurrency = CurrencyExchange::getCurrencyObjectOfType($preferred_currency, $valueInCurrency);
			$this->_valueInCurrency = $preferredCurrency;
		}
	}

	public function getValueInCurrency( $preferred_currency = CurrencyExchange::USD) {
		$retVal = null;

		$retVal = CurrencyExchange::getValueInCurrencyFromCurrency($this->_valueInCurrency, $preferred_currency);

		return $retVal;
	}

	public function setValueInCurrency( $valueInCurrency, $preferred_currency = CurrencyExchange::USD ) {
		if ( $valueInCurrency instanceof Currency ) {
			$this->_valueInCurrency = $valueInCurrency;
		} else {
			$preferredCurrency = CurrencyExchange::getCurrencyObjectOfType($preferred_currency, $valueInCurrency);
			$this->_valueInCurrency = $preferredCurrency;
		}

	}

	public function getNumericValueInCurrency( $preferred_currency = CurrencyExchange::USD ) {
		$retVal = null;

		$currencyObject = CurrencyExchange::getValueInCurrencyFromCurrency($this->_valueInCurrency, $preferred_currency);

		$retVal = $currencyObject->getNumericValue();

		return $retVal;
	}

}

