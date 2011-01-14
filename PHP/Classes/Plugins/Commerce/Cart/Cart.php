<?php
/**
 * Cart Class File
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
 * Cart
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class Cart {

	private $_cartItems = array();
	private $_total = null;

	public function __construct() {
		
	}

	private function rebuildCart() {
		$this->_total = null;
	}

	public function addCartItem( $cartItem ) {
		$this->_cartItems[] = $cartItem;
		$this->rebuildCart();
	}

	public function getCartItems() {
		return $this->_cartItems;
	}

	public function setCartItems( $cartItems ) {
		$this->_cartItems = $cartItems;
		$this->rebuildCart();
	}

	public function getCartTotal() {
		return $this->_total;
	}

	public function sortCartItems() {
		
	}

	// All totals excluding tax, shipping and discounts
	public function getSubTotalsArray( $preferred_currency = CurrencyExchange::USD, $prefer_numeric = false ) {
		$retVal = array();

		if ($prefer_numeric) {
			foreach($this->_cartItems as $cartItem) {
				$retVal[] = $cartItem->getSubTotal( $preferred_currency, true );
			}
		} else {
			foreach($this->_cartItems as $cartItem) {
				$retVal[] = $cartItem->getSubTotal( $preferred_currency, false );
			}
		}

		return $retVal;
	}

	public function getTotal( $preferred_currency = CurrencyExchange::USD, $prefer_numeric = false ) {
		$retVal = null;

		if (!isset($this->_total)) {
			$numeric_value = 0;

			foreach($this->_cartItems as $cartItem) {
				$numeric_value += $cartItem->getSubTotal( $preferred_currency, true );
			}

			$this->_total = CurrencyExchange::getValueInCurrencyFromNumeric( $numeric_value, $preferred_currency );
			$retVal = $this->_total;
		} else {
			$this->_total = CurrencyExchange::getValueInCurrencyFromCurrency($this->_total, $preferred_currency);
			$retVal = $this->_total;
		}

		if ($prefer_numeric) {
			$retVal = $retVal->getNumericValue();
		}

		return $retVal;
	}

}

