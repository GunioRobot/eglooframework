<?php
/**
 * CartItem Class File
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

	// private $_appliedDiscounts = array();
	// private $_availableDiscounts = array();

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

	public function getSubTotal( $preferred_currency = null, $prefer_numeric = false ) {
		$retVal = null;

		if (!isset($this->_subTotalInUSD)) {
			foreach($this->_discountLineItems as $discountLineItem) {
				$retVal = $discountLineItem->getValueInCurrency( $preferred_currency );
			}
			foreach($this->_productLineItems as $productLineItem) {
				$retVal += $productLineItem->getValueInCurrency( $preferred_currency );
			}
			foreach($this->_shippingLineItems as $shippingLineItem) {
				$retVal = $shippingLineItem->getValueInCurrency( $preferred_currency );
			}
			foreach($this->_taxLineItems as $taxLineItem) {
				$retVal = $taxLineItem->getValueInCurrency( $preferred_currency );
			}
		} else {
			$retVal = $this->_subTotal;
		}

		return $retVal;
	}


}

