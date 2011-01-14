<?php
/**
 * CurrencyExchange Class File
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
 * CurrencyExchange
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CurrencyExchange {

	// Just for now.  These should be populated from XML or another class
	CONST USD = 0x0000;
	CONST EUR = 0x0001;
	CONST JPY = 0x0002;

	// This is ugly, but whatever for now
	private static $_currencyConstantClassMap = array(
		self::USD => 'USDollarCurrency',
		self::EUR => 'EUEuroCurrency',
		self::JPY => 'JPYenCurrency'
	);

	private static $_currencyClassConstantMap = array(
		'USDollarCurrency' => self::USD,
		'EUEuroCurrency' => self::EUR,
		'JPYenCurrency' => self::JPY,
	);

	public static function getCurrencyObjectOfType( $preferred_currency = self::USD, $valueInCurrency = 0) {
		$currency_type_classname = self::$_currencyConstantClassMap[$preferred_currency];
		return $currency_type_classname::getInstance($valueInCurrency);
	}

	public static function getValueInCurrencyFromCurrency( Currency $valueInCurrency, $preferred_currency = self::USD ) {
		$retVal = null;

		$from_currency = self::$_currencyClassConstantMap[get_class($valueInCurrency)];

		$exchangeRate = self::getExchangeRate( $from_currency, $preferred_currency );

		$from_numeric_value = $valueInCurrency->getNumericValue();

		$to_numeric_value = $from_numeric_value * $exchangeRate;

		$preferred_currency_class = self::$_currencyConstantClassMap[$preferred_currency];

		$retVal = new $preferred_currency_class($to_numeric_value);

		return $retVal;
	}

	public static function getValueInCurrencyFromNumeric( $numeric_value, $preferred_currency = self::USD ) {
		$retVal = null;

		$preferred_currency_class = self::$_currencyConstantClassMap[$preferred_currency];

		$retVal = new $preferred_currency_class($numeric_value);

		return $retVal;

	}

	public static function getExchangeRate( $from_currency, $to_currency ) {
		// Run some cool math here
		$retVal = null;

		if ($from_currency === $to_currency) {
			$retVal = 1;
		} else {
			// Query for rate in the future.  Should probably be some external library.
			// For now, hardcode to test.  Yes, this is ugly
			switch( $from_currency ) {
				case self::USD :
					{
						if ( $to_currency === self::EUR ) {
							$retVal = 0.8;
						} else if ( $to_currency === self::JPY ) {
							$retVal = 10;
						}
					}
					break;
				case self::EUR :
					{
						if ( $to_currency === self::USD ) {
							$retVal = 1.4;
						} else if ( $to_currency === self::JPY ) {
							$retVal = 5;
						}
					}
					break;
				case self::JPY :
					{
						if ( $to_currency === self::USD ) {
							$retVal = 0.10;
						} else if ( $to_currency === self::EUR ) {
							$retVal = 0.5;
						}
					}
					break;
				default :
					throw new Exception( 'Invalid currency specified to convert from' );
					break;
			}
		}

		return $retVal;
	}

}

