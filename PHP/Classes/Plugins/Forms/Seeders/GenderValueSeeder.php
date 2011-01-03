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
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * GenderValueSeeder
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class GenderValueSeeder extends ValueSeeder {

	protected $_defaultValue = 'male';

	protected $_genderValues = array(
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

	public function getValues() {
		return $this->_genderValues;
	}

}

