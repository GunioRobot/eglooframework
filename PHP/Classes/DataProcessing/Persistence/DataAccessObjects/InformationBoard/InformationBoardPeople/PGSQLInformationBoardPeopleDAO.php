<?php
/**
 * PGSQLInformationBoardPeopleDAO Class File
 *
 * Needs to be commented
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
 * @package Persistence
 * @version 1.0
 */

/**
 * PGSQLInformationBoardPeopleDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
class PGSQLInformationBoardPeopleDAO extends InformationBoardPeopleDAO {
 
    public function getInformationBoardPeopleBase( $options = null ) {
       
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//get bidirectional first
		$query = "select u.username, u.firstname, u.lastname, p.profile_id, p.profilename from users u, profiles p " .
				"where u.user_id = p.profilecreator order by random() limit 30"; 


		$result = pg_query( $db_handle, $query);

        $informationBoardPeopleBaseDTO = new InformationBoardPeopleBaseDTO();

		for($row = 0; $row< pg_num_rows( $result ); $row++){
			$resultSet = pg_fetch_array( $result, $row, PGSQL_ASSOC );
			$person = new Person();
			$person->setUserName(  $resultSet['username'] );
			$person->setFirstName( $resultSet['firstname'] );
			$person->setLastName( $resultSet['lastname'] );
			$person->setProfileID( $resultSet['profile_id'] );        
			$person->setProfileName( $resultSet['profilename'] );
	        $informationBoardPeopleBaseDTO->addPeopleItem( $person );
		}
 
		pg_close( $db_handle );
		
        return $informationBoardPeopleBaseDTO;
    }
 
}

