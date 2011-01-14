<?php
/**
 * USDollarCurrency Class File
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
 * USDollarCurrency
 *
 * $short_description
 *
 * $long_description
 *
 * @package Finance
 * @subpackage Currency
 */
class USDollarCurrency extends Currency {
	/* Class Constants */

	/* Static Data Members*/

	/* Private Data Members */
	
	/* Protected Data Members */
	protected $_code = 'USD';
	protected $_sign = '$';

	/* Public Data Members */

	public function __construct( $numeric_value, $rounding_mode = PHP_ROUND_HALF_UP, $rounding_precision = 2 ) {
		$this->_numeric_value = $numeric_value;
		$this->_rounding_mode = $rounding_mode;
		$this->_rounding_precision = $rounding_precision;
	}

	public function __destruct() {
		
	}

}

