<?php
/**
 * PGSQLSearchDAO Class File
 *
 * Needs to be commented
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * PGSQLSearchDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLSearchDAO extends SearchDAO {
 

    /**
     * This function searches the database for names and their associated
     * profiles based on a search string.  The search string will search
     * the user first name and last name fields in the database.
     * 
     * 
     * @param name - the name to search for
     * @param limit - integer - the number of results to return
     * @param offset - integer - the row number to start retrieving at
     * @return array - An array of SearchNameProfileResultDTOs 
     */
	public function getNameAndProfileIDByName( $name, $limit, $offset ){
		
		
      	if( ! is_int( $limit ) ){
      		eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLSearchDAO::getNameAndProfileIDByName ERROR: limit ($limit) is not an int" );
      		return array();	
      		//TODO throw exception
      	}
      	
      	if( ! is_int( $offset ) ){
      		eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLSearchDAO::getNameAndProfileIDByName ERROR: offset ($offset) is not an int" );	
      		return array();
      		//TODO throw exception
      	}
      	
      	
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape name
		$name = pg_escape_string($name);

		$query = 
			"SELECT first, last, profile_id, profilename FROM (
				SELECT u.firstname || ' ' || u.lastname as name, p.profile_id, p.profilename, u.firstname as first, u.lastname as last
				FROM users u, profiles p where u.user_id = p.profilecreator
			) as newTable where name ilike '%$name%' ORDER BY last LIMIT $limit OFFSET $offset";

		$result = pg_query( $db_handle, $query);
		

		$searchNameProfileResultItems = array();

		for($row = 0; $row< pg_num_rows( $result ); $row++){
			$resultSet = pg_fetch_array( $result, $row, PGSQL_ASSOC );
			$snpDTO = new SearchNameProfileResultDTO();
			$snpDTO->setFirstName( $resultSet['first'] );
			$snpDTO->setLastName( $resultSet['last'] );
			$snpDTO->setProfileID( $resultSet['profile_id'] );
			$snpDTO->setProfileName( $resultSet['profilename'] );
	        $searchNameProfileResultItems[] = $snpDTO;
		}
 
		pg_close( $db_handle );
		
		return $searchNameProfileResultItems;		
		
	}
 
 
 
 }

