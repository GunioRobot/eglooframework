<?php
/**
 * YearValueSeeder Class File
 *
 * $file_block_description
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
 * @category Plugins
 * @package Forms
 * @subpackage Seeders
 * @version 1.0
 */

/**
 * YearValueSeeder
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Seeders
 */
class YearValueSeeder extends ValueSeeder {

	protected $_defaultValue = null;

	protected $_yearValues = array();

	/**
	 * Static Data Members
	 */
	private static $_singleton = null;

	private function __construct() {
		$this->_yearValues[null] = '-';

		$starting_year = intval(date('Y')) - 18;

		for( $i = 0; $i < 80 ; $i++ ) {
			$this->_yearValues[$starting_year - $i] = $starting_year - $i;
		}

		$this->_defaultValue = $starting_year;
	}

	public static function getInstance() {
		if (!self::$_singleton) {
			self::$_singleton = new YearValueSeeder();
		}

		return self::$_singleton;
	}

	public function getDefaultValue() {
		return $this->_defaultValue;
	}

	public function getValues( $with_select_null = true ) {
		$retVal = $this->_yearValues;

		if ( !$with_select_null ) {
			unset($retVal[null]);
		}

		return $retVal;
	}

}

