<?php
/**
 * PGSQLUserProfilePageDAO Class File
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * PGSQLUserProfilePageDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLUserProfilePageDAO extends UserProfilePageDAO {
    
    /**
     * update profile information
     */
    public function setProfilePageLayout($profileID, $pageLayout) {
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT setProfilePageLayout($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID, $pageLayout));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
    		
    }

    /**
     * This function retrieves a created Profile
     * 
     * @param profileID
     * @return UserProfilePageDTO
     */
    public function getProfile( $profileID ) {
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT getProfilePageLayout($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
    	
    	
    	$userProfilePageLayoutDTO = new UserProfilePageDTO();
    	$userProfilePageLayoutDTO->setProfileID($profileID);
    	$userProfilePageLayoutDTO->setProfilePageLayout($testarray['getprofilepagelayout']);
    	
    	return $userProfilePageLayoutDTO;
    }
    
    public function getProfileCubes( $profileID ){
    	
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'select element_id, layoutrow, layoutcolumn from profilelayout where profile_id = $1 order by layoutcolumn asc, layoutrow asc');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID));

		$testarray =  pg_fetch_all($result);
		pg_close( $db_handle );
		
		$retval = array();
		
		//Make an array of cubedtos
		if( ! $testarray ) return $retval;
		
		foreach ($testarray as $row) {	
			$retval[ $row['layoutcolumn'] ][] = $row['element_id'];
		}

    	return $retval;
    	
    }


    public function deleteAllProfileCubes( $profileID ){
    	
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'delete from profilelayout where profile_id = $1');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID));
		
		pg_close( $db_handle );    	
    }


    public function addCubeToPage( $profileID, $cubeID, $column, $row ){
    	
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'INSERT INTO profilelayout (profile_id, element_id, layoutcolumn, layoutrow ) VALUES ($1, $2, $3, $4)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID, $cubeID, $column, $row ));

		pg_close( $db_handle );    	
    }

    // TODO find a better location for these two methods
    public function getProfileName( $profileID ) {
        $retVal = null;
        //get handle
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

        //escape input variables
        $profileID = pg_escape_string($profileID);

        $query = 
            "SELECT p.profilename from profiles p where " . "p.profile_id='$profileID'";

        $result = pg_query( $db_handle, $query);
        $resultSet = pg_fetch_array( $result, 0, PGSQL_ASSOC );
        $retVal = $resultSet['profilename'];
        
        pg_close( $db_handle );
        
        return $retVal;        
    }
    
    public function setProfileName( $profileID ) {
        
    }

}


