<?php
/**
 * OracleDBQueryPopulationRoutine Class File
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
 * OracleDBQueryPopulationRoutine
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class OracleDBQueryPopulationRoutine extends QueryPopulationRoutine {

	public function populateQuery( $queryTransaction, $queryParameters, $associative = false, $sort = false, $method = 'oracle_bind_by_name' ) {
		if ($method === 'oracle_bind_by_name') {
			$this->populateQueryWithOracleByBindName( $queryTransaction, $queryParameters, $associative, $sort, $method );
		} else {
			throw new Exception('OracleDBQueryPopulationRoutine: Invalid population method requested');
		}
	}

	// Expects $queryParameters to be in format [0] => (type => 'decimal', value=> 10) etc
	private function populateQueryWithOracleByBindName( $queryTransaction, $queryParameters, $method = 'sprintf', $associative = false, $sort = false ) {
		// TODO - should use oci_bind_by_name
		throw new Exception('OracleDBQueryPopulationRoutine::populateQueryWithOracleByBindName() is not yet implemented.');
	}

}
