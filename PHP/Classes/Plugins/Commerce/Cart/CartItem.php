<?php
/**
 * CartItem Class File
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
 * CartItem
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class CartItem {

	private $_discountLineItems = array();
	private $_productLineItems = array();
	private $_shippingLineItems = array();
	private $_taxLineItems = array();

	private $_subTotal = null;

	public function addDiscountLineItem( $discountLineItem ) {
		$this->addDiscountLineItems(array($discountLineItem));
	}

	public function addDiscountLineItems( $discountLineItems ) {
		foreach( $discountLineItems as $discountLineItem ) {
			$this->_discountLineItems[] = $discountLineItem;
		}
	}

	public function addProductLineItem( $productLineItem ) {
		$this->addProductLineItems(array($productLineItem));
	}

	public function addProductLineItems( $productLineItems ) {
		foreach( $productLineItems as $productLineItem ) {
			$this->_productLineItems[] = $productLineItem;
		}
	}

	public function addShippingLineItem( $shippingLineItem ) {
		$this->addShippingLineItems(array($shippingLineItem));
	}

	public function addShippingLineItems( $shippingLineItems ) {
		foreach( $shippingLineItems as $shippingLineItem ) {
			$this->_shippingLineItems[] = $shippingLineItem;
		}
	}

	public function addTaxLineItem( $taxLineItem ) {
		$this->addTaxLineItems(array($taxLineItem));
	}

	public function addTaxLineItems( $taxLineItems ) {
		foreach( $taxLineItems as $taxLineItem ) {
			$this->_taxLineItems[] = $taxLineItem;
		}
	}

	public function getLineItems( $returnFlat = true ) {
		$retVal = array();

		if ( $returnFlat ) {
			foreach($this->_discountLineItems as $discountLineItem) {
				$retVal[] = $discountLineItem;
			}
			foreach($this->_productLineItems as $productLineItem) {
				$retVal[] = $productLineItem;
			}
			foreach($this->_shippingLineItems as $shippingLineItem) {
				$retVal[] = $shippingLineItem;
			}
			foreach($this->_taxLineItems as $taxLineItem) {
				$retVal[] = $taxLineItem;
			}
		} else {
			$retVal = array(
				'discountLineItems' => $this->_discountLineItems,
				'productLineItems' => $this->_productLineItems,
				'shippingLineItems' => $this->_shippingLineItems,
				'taxLineItems' => $this->_taxLineItems
			);
		}

		return $retVal;
	}

	public function getSubTotal( $preferred_currency = CurrencyExchange::USD, $prefer_numeric = false ) {
		$retVal = null;

		if (!isset($this->_subTotal)) {
			$numeric_value = 0;

			foreach($this->_discountLineItems as $discountLineItem) {
				$foo = $discountLineItem->getNumericValueInCurrency( $preferred_currency );
				$numeric_value += $discountLineItem->getNumericValueInCurrency( $preferred_currency );
			}
			foreach($this->_productLineItems as $productLineItem) {
				$numeric_value += $productLineItem->getNumericValueInCurrency( $preferred_currency );
			}
			foreach($this->_shippingLineItems as $shippingLineItem) {
				$numeric_value += $shippingLineItem->getNumericValueInCurrency( $preferred_currency );
			}
			foreach($this->_taxLineItems as $taxLineItem) {
				$numeric_value += $taxLineItem->getNumericValueInCurrency( $preferred_currency );
			}

			$this->_subTotal = CurrencyExchange::getValueInCurrencyFromNumeric( $numeric_value, $preferred_currency );
			$retVal = $this->_subTotal;
		} else {
			$this->_subTotal = CurrencyExchange::getValueInCurrencyFromCurrency( $this->_subTotal, $preferred_currency );
			$retVal = $this->_subTotal;
		}

		if ($prefer_numeric) {
			$retVal = $retVal->getNumericValue();
		}

		return $retVal;
	}


}

