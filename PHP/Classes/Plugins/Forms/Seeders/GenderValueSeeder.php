<?php
/**
 * GenderValueSeeder Class File
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
 * GenderValueSeeder
 *
 * $short_description
 *
 * $long_description
 *
 * @category Plugins
 * @package Forms
 * @subpackage Seeders
 */
class GenderValueSeeder extends ValueSeeder {

	protected $_defaultValue = 0;

	protected $_genderValues = array(
		0 => '-Select-',
		'male' => 'Male',
		'female' => 'Female',
		// 'male_transgender' => 'Transgender Male',
		// 'female_transgender' => 'Transgender Female'
	);

	/**
	 * Static Data Members
	 */
	private static $_singleton = null;

	private function __construct() {
		
	}

	public static function getInstance() {
		if (!self::$_singleton) {
			self::$_singleton = new GenderValueSeeder();
		}

		return self::$_singleton;
	}

	public function getDefaultValue() {
		return $this->_defaultValue;
	}

	public function getValues( $with_select_null = true ) {
		$retVal = $this->_genderValues;

		if ( !$with_select_null ) {
			unset($retVal[0]);
		}

		return $retVal;
	}

}

