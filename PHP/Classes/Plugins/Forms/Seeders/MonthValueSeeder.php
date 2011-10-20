<?php
/**
 * MonthValueSeeder Class File
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
 * MonthValueSeeder
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Seeders
 */
class MonthValueSeeder extends ValueSeeder {

	protected $_defaultValue = 'January';

	protected $_monthValues = array(
		0 => '-Select-',
		1 => 'January',
		2 => 'February',
		3 => 'March',
		4 => 'April',
		5 => 'May',
		6 => 'June',
		7 => 'July',
		8 => 'August',
		9 => 'September',
		10 => 'October',
		11 => 'November',
		12 => 'December' );

	/**
	 * Static Data Members
	 */
	private static $_singleton = null;

	private function __construct() {
		
	}

	public static function getInstance() {
		if (!self::$_singleton) {
			self::$_singleton = new MonthValueSeeder();
		}

		return self::$_singleton;
	}

	public function getDefaultValue() {
		return $this->_defaultValue;
	}

	public function getValues() {
		return $this->_monthValues;
	}

}

