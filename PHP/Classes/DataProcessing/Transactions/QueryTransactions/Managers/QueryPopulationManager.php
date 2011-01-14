<?php
/**
 * QueryPopulationManager Class File
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
 * QueryPopulationManager
 *
 * $short_description
 *
 * $long_description
 *
 * @package $package
 * @subpackage $subpackage
 */
class QueryPopulationManager {

	public static function populateQueryTransaction( QueryTransaction $queryTransaction, $queryParameters ) {
		// TODO Make this a bit more generalized and less raw

		if ( $queryTransaction->getQueryDialect() === DialectLibrary::MYSQL ) {
			$queryPopulationRoutine = new MySQLQueryPopulationRoutine();
			$queryPopulationRoutine->populateQuery( $queryTransaction, $queryParameters );
		} else if ( $queryTransaction->getQueryDialect() === DialectLibrary::MYSQLI ){
			$queryPopulationRoutine = new MySQLiQueryPopulationRoutine();
			$queryPopulationRoutine->populateQuery( $queryTransaction, $queryParameters );
		} else if ( $queryTransaction->getQueryDialect() === DialectLibrary::MYSQLIOOP ){
			$queryPopulationRoutine = new MySQLiOOPQueryPopulationRoutine();
			$queryPopulationRoutine->populateQuery( $queryTransaction, $queryParameters );
		}

	}

}

