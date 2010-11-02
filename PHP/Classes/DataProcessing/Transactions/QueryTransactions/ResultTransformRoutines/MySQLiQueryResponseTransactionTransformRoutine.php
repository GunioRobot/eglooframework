<?php
/**
 * MySQLiQueryResponseTransactionTransformRoutine Class File
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
 * MySQLiQueryResponseTransactionTransformRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiQueryResponseTransactionTransformRoutine extends QueryResponseTransactionTransformRoutine {

	public function getSingleColumnFeederStringFormat( $queryResponseTransaction ) {
		$retVal = null;
		$reformatted = array();
		$column_key = null;

		$dataPackage = $queryResponseTransaction->getDataPackage();

		if (isset($dataPackage[0]) && count($dataPackage[0])) {
			foreach($dataPackage[0] as $key => $value) {
				$column_key = $key;
				break;
			}

			$reformatted[$key] = array();
			$reformatted['column_key'] = $key;

			foreach($dataPackage as $row) {
				foreach($row as $inner) {
					$reformatted[$key][] = $inner;
				}
			}

			$retVal = $reformatted;
		} else if (empty($dataPackage)) {
			$retVal = null;
		} else {
			throw new Exception( 'MySQLiQueryResponseTransactionTransformRoutine: Invalid column data set supplied' );
		}

		self::$numberOfTransformations += 1;

		return $retVal;
	}

}

