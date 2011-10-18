<?php
namespace eGloo\Plugin\Text;

use \eGloo\Configuration as Configuration;

/**
 * eGloo\Plugin\Text\Inflector Class File
 *
 * Contains the class definition for the eGloo\Plugin\Text\Inflector
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
 * @category $category
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

include Configuration::getFrameworkRootPath() . '/Library/CakePHP/lib/Cake/Utility/Inflector.php';

/**
 * eGloo\Plugin\Text\Inflector
 *
 * $short_description
 *
 * $long_description
 *
 * @category $category
 * @package $package
 * @subpackage $subpackage
 */
class Inflector {

	/**
	 * Return $word in plural form
	 *
	 * @param string $word Word in singular
	 * @return string Word in plural
	 */
	public static function pluralize( $word ) {
		// For now
		return \Inflector::pluralize( $word );
	}

}

