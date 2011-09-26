<?php
/**
 * MySQLiOOPQueryPopulationRoutine Class File
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
 * MySQLiOOPQueryPopulationRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiOOPQueryPopulationRoutine extends QueryPopulationRoutine {

	public function populateQuery( $queryTransaction, $queryParameters, $associative = false, $sort = false, $method = 'sprintf' ) {
		if ( $method === 'stmt' ) {
			$this->populateQueryWithSTMT( $queryTransaction, $queryParameters, $associative, $sort, $method );
		} else if ($method === 'sprintf') {
			$this->populateQueryWithVsprintf( $queryTransaction, $queryParameters, $associative, $sort, $method );
		} else {
			throw new Exception('MySQLiOOPQueryPopulationRoutine: Invalid population method requested');
		}
	}

	// Expects $queryParameters to be in format [0] => (type => 'decimal', value=> 10) etc
	private function populateQueryWithVsprintf( $queryTransaction, $queryParameters, $associative = false, $sort = false ) {
		// For now we're going to assume string
		// TODO should not assume database connection is egPrimary
		$connection = DBConnectionManager::getConnection()->getRawConnectionResource();
		$dataPackageString = $queryTransaction->getDataPackageString();
		$populatedDataPackageString = null;

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Query to prepare: ' . "\n" . $dataPackageString, 'DataProcessing' );
		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Parameters: ' . "\n" . print_r($queryParameters, true), 'DataProcessing' );

		// Check if we're doing string replacement or if we can just use sprintf (for now)
		if (!$associative && !empty($queryParameters)) {
			if ($sort) {
				ksort($queryParameters);
			}

			$processedParameters = array();

			foreach($queryParameters as $key => $value) {
				if ( $value['type'] === 'string' ) {
					if (is_string($value['value'])) {
						if (isset($value['quote']) && $value['quote']) {
							$processedParameters[] = '\'' . $connection->real_escape_string($value['value']) . '\'';
						} else if (isset($value['escape']) && !$value['escape']) {
							$processedParameters[] = $value['value'];
						} else {
							$processedParameters[] = $connection->real_escape_string($value['value']);
						}
					} else {
						throw new Exception('MySQLiOOPQueryPopulationRoutine: Type mismatch.  Expected string, got ' . gettype($value['value']) . ' with value ' . $value['value']);
					}
				} else if ( $value['type'] === 'integer' ) {
					if (is_int($value['value'])) {
						$processedParameters[] = $value['value'];
					} else {
						$integer_value = intval($value['value']);
						$processedParameters[] = $integer_value;

						// TODO implement this
						// throw new Exception('MySQLiOOPQueryPopulationRoutine: Type mismatch.  Expected int, got ' . gettype($value['value']) . ' with value ' . $value['value']);
					}
				} else if ( $value['type'] === 'float' ) {
					if (is_float($value['value'])) {
						$processedParameters[] = $value['value'];
					} else {
						throw new Exception('MySQLiOOPQueryPopulationRoutine: Type mismatch.  Expected float, got ' . gettype($value['value']) . ' with value ' . $value['value']);
					}
				} else if ( $value['type'] === 'mixed' ) {
					$processedParameters[] = $value['value'];
				} else {
					throw new Exception('MySQLiOOPQueryPopulationRoutine: Invalid type specified for value: ' . $value['value']);
				}
			}

			$populatedDataPackageString = vsprintf($dataPackageString, $processedParameters);

			self::$numberOfQueriesPopulated += 1;
		} else if ( empty($queryParameters) ) {
			// Means we don't want to do vsprintf on this, just return the prepared query string
			$populatedDataPackageString = $dataPackageString;
		}

		eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Prepared query: ' . "\n" . $populatedDataPackageString, 'DataProcessing' );

		// $connection = DBConnectionManager::getConnection()->getRawConnectionResource();
		$statement = $connection->stmt_init();

		if (!$statement->prepare($populatedDataPackageString)) {
			throw new Exception("Could not prepare statement for $populatedDataPackageString: " . mysqli_error($connection) );
		}

		$queryTransaction->setDataPackage(array('preparedQueryString' => $populatedDataPackageString, 'preparedStatementObject' => $statement));

		// TODO only maintain this if trace decorator detected and add error tracking
		self::$historyOfQueriesPopulated[] =
			array('unpreparedQueryString' => $dataPackageString, 'parameters' => $queryParameters, 'preparedQueryString' => $populatedDataPackageString, 'error' => 0 );
	}

	private function populateQueryWithSTMT( $queryTransaction, $queryParameters, $associative = false, $sort = false ) {
		$dataPackageString = $queryTransaction->getDataPackageString();
		$connection = DBConnectionManager::getConnection()->getRawConnectionResource();
		$statement = $connection->stmt_init();

		if (!$statement->prepare($dataPackageString)) {
			throw new Exception("Could not prepare statement for $dataPackageString: " . mysqli_error($connection) );
		}

		if (!empty($queryParameters)) {
			$refParams = array();

			foreach($queryParameters as $key => $value) {
				$refParams[$key] = &$queryParameters[$key]['value'];
			}

			call_user_func_array(array($statement, 'bind_param'), $refParams);

			self::$numberOfQueriesPopulated += 1;
		}

		die_r('for the lulz');
	}

}
