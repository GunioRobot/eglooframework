<?php
/**
 * PostgreSQLPopulationRoutine Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package $package
 * @subpackage $subpackage
 * @version 1.0
 */

/**
 * PostgreSQLPopulationRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class PostgreSQLQueryPopulationRoutine extends QueryPopulationRoutine {

	public function populateQuery( $queryTransaction, $queryParameters, $associative = false, $sort = false, $method = 'sprintf' ) {
		if ($method === 'sprintf') {
			$this->populateQueryWithVsprintf( $queryTransaction, $queryParameters, $associative, $sort, $method );
		} else {
			throw new Exception('PostgreSQLQueryPopulationRoutine: Invalid population method requested');
		}
	}

	// Expects $queryParameters to be in format [0] => (type => 'decimal', value=> 10) etc
	private function populateQueryWithVsprintf( $queryTransaction, $queryParameters, $associative = false, $sort = false ) {
		// For now we're going to assume string
		$dataPackageString = $queryTransaction->getDataPackageString();
		$populatedDataPackageString = null;

		// Check if we're doing string replacement or if we can just use sprintf (for now)
		if (!$associative) {
			if ($sort) {
				ksort($queryParameters);
			}

			$processedParameters = array();

			foreach($queryParameters as $key => $value) {
				if ( $value['type'] === 'string' ) {
					if (is_string($value['value'])) {
						$processedParameters[] = pg_escape_string($value['value']);
					} else {
						throw new Exception('PostgreSQLQueryPopulationRoutine: Type mismatch.  Expected string, got ' . gettype($value['value']) . ' with value ' . $value['value']);
					}
				} else if ( $value['type'] === 'integer' ) {
					if (is_int($value['value'])) {
						$processedParameters[] = $value['value'];
					} else {
						throw new Exception('PostgreSQLQueryPopulationRoutine: Type mismatch.  Expected int, got ' . gettype($value['value']) . ' with value ' . $value['value']);
					}
				} else if ( $value['type'] === 'float' ) {
					if (is_float($value['value'])) {
						$processedParameters[] = $value['value'];
					} else {
						throw new Exception('PostgreSQLQueryPopulationRoutine: Type mismatch.  Expected float, got ' . gettype($value['value']) . ' with value ' . $value['value']);
					}
				} else {
					throw new Exception('PostgreSQLQueryPopulationRoutine: Invalid type specified for value: ' . $value['value']);
				}
			}
			$populatedDataPackageString = vsprintf($dataPackageString, $processedParameters);
		} else {
			
		}

		$queryTransaction->setDataPackage($populatedDataPackageString);
	}

}

