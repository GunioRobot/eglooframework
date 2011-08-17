<?php
/**
 * MySQLiOOPQueryExecutionRoutine Class File
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
 * MySQLiOOPQueryExecutionRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class MySQLiOOPQueryExecutionRoutine extends QueryExecutionRoutine {

	public static function executeTransaction( $queryTransaction ) {}

	public static function executeTransactionWithConnection( $queryTransaction, $connection, $returnResponseResource = false ) {
		$retVal = null;

		$resultSet = $refResults = $columns = $result = array();

		$dataPackage = $queryTransaction->getDataPackage();

		$preparedQueryString = $dataPackage['preparedQueryString'];
		$statement = $dataPackage['preparedStatementObject'];

		$statement->execute();

		$statement->store_result();

		$metadata = $statement->result_metadata();

		if (isset($metadata) && !empty($metadata)) {
			while($column = $metadata->fetch_field()) {
				$var = $column->name;
				$$var = null;
				$refResults[] = &$$var;
				$columns[] = $column->name;
			}

			call_user_func_array(array($statement, 'bind_result'), $refResults);

			while($statement->fetch()) {
				$i = 0;

				foreach($columns as $value) {
					$result[$value]  = $refResults[$i];
					$i++;
				}

				$resultSet[] = $result;
			}

			$metadata->close();
		}

		$statement->close();

		$queryResultResource = $resultSet;

		$responseResource = new MySQLiOOPQueryResponseResource($queryResultResource);

		if ($returnResponseResource) {
			$retVal = $responseResource;
		} else {
			$retVal = self::convertResponseResourceIntoResponseTransaction($responseResource);
		}

		// TODO make this conditional on decorator and add ability for error code tracking
		self::$historyOfQueriesExecuted[] = array( 'executedQuery' => $preparedQueryString, 'executedQueryResult' => $resultSet, 'error' => 0 );
		self::$numberOfQueriesExecuted += 1;

		return $retVal;
	}

	protected static function convertResponseResourceIntoResponseTransaction( $queryResponseResource ) {
		$retVal = null;

		$responseDataPackage = array();

		if (!$queryResponseResource->isBooleanValue()) {
			while( $row = $queryResponseResource->fetchNextRowAssociative() ) {
				$responseDataPackage[] = $row;
			}
		} else {
			$responseDataPackage = $queryResponseResource->getBooleanValue();
		}

		$retVal = new QueryResponseTransaction($responseDataPackage);
		$retVal->setQueryDialect(DialectLibrary::MYSQLIOOP);

		return $retVal;
	}

}

