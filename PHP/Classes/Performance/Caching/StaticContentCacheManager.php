<?php
/**
 * StaticContentCacheManager Class File
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
 * StaticContentCacheManager
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class StaticContentCacheManager {

	public static function buildStaticContentCache( $package, $uniquePath, $filename, $output ) {
		// Depending on the requests.xml rules, this could be a security hole
		if ( !is_writable( eGlooConfiguration::getWebRoot() . $package . '/' . $uniquePath ) ) {
			try {
				$mode = 0777;
				$recursive = true;

				mkdir( eGlooConfiguration::getWebRoot() . $package . '/' . $uniquePath, $mode, $recursive );
			} catch (Exception $e){
				// TODO figure out what to do here
			}
		}

		if ( !file_put_contents( eGlooConfiguration::getWebRoot() . $package . '/' . $uniquePath . '/' . $filename, $output ) ) {
			throw new Exception( 'File write failed for ' . eGlooConfiguration::getWebRoot() . $package . '/' . $uniquePath . '/' . $filename );
		}
	}

	public static function clearAllStaticContentCache() {
		
	}

	public static function clearStaticContentCache( $package, $uniquePath = null ) {
		
	}

}

