<?php
/**
 * PGSQLRegistrationDAO Class File
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
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * PGSQLRegistrationDAO
 *
 * $short_description
 *
 * $long_description
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLRegistrationDAO extends RegistrationDAO {

	public function CRUDCreate( $formDTO ) {
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		$username = $formDTO->getUsername();
		$username_id = null;

		if ( !$this->usernameAvailable( $username ) ) {
			echo_r('Username not available');
		} else {
			echo_r('Username available');
			// $username_id = $this->createUsername( $username );
			
			// if ($username_id !== false && $username_id !== null) {
			// 	echo_r('Username creation success: ' . $username_id);
			// } else {
			// 	echo_r('Failure: ');
			// 	echo_r($username_id);
			// }
		}
die;
		//Prepare a query for execution
		$result = pg_prepare($db_handle, "query", 'INSERT INTO profilelayout (profile_id, element_id, layoutcolumn, layoutrow ) VALUES ($1, $2, $3, $4)');

		// Execute the prepared query.	Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID, $cubeID, $column, $row ));

		pg_close( $db_handle );		

		echo_r($formDTO);
		die_r('here2');
	}

	public function CRUDRead( $formDTO ) {
		
	}

	public function CRUDUpdate( $formDTO ) {
		
	}

	public function CRUDDestroy( $formDTO ) {
		
	}

	public function usernameAvailable( $username ) {
		$retVal = false;

		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//Prepare a query for execution
		$result = pg_prepare($db_handle, 'usernameAvailable', 'SELECT username_id FROM usernames WHERE username = $1 LIMIT 1');

		// Execute the prepared query.	Note that it is not necessary to escape
		$result = pg_execute($db_handle, 'usernameAvailable', array($username));

		$resultValues = pg_fetch_assoc( $result );
		$username_id = $resultValues['username_id'];

		if ( $username_id === null || $username_id === false ) {
			$retVal = true;
		} else {
			$retVal = false;
		}

		// Close connection
		pg_close( $db_handle );

		return $retVal;
	}

	public function createUsername( $username ) {
		$retVal = false;

		// Get a connection
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//Prepare a query for execution
		$result = pg_prepare( $db_handle, 'insertUsername', 'INSERT INTO usernames ( active, username ) VALUES ( $1, $2 )' );

		// Execute the prepared query.	Note that it is not necessary to escape
		$result = pg_execute( $db_handle, 'insertUsername', array( 'false', $username ) );
		
		if ( $result !== false ) {
			// Get the insert ID
			$result = pg_prepare( $db_handle, 'getLastUsernameID', 'SELECT currval(\'seq_usernames_username_id\') AS username_id' );
			$result = pg_execute( $db_handle, 'getLastUsernameID', array() );

			$resultValues = pg_fetch_assoc( $result );
			$username_id = $resultValues['username_id'];

			$retVal = $username_id !== false && $username_id !== null ? $username_id : false;
		} else {
			$retVal = $result;
		}

		// Close this connection
		pg_close( $db_handle );

		return $retVal;
	}


}

