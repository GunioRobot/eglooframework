<?php
/**
 * PGSQLFridgeDAO Class File
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * PGSQLFridgeDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
class PGSQLFridgeDAO {
    
    /**
     * @return array of FridgeDTOs 
     */
    public function getFridgeItemList( $profileID ) {

    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'select elementtype_id, elementpackagepath from 
									elementtypes where elementpackage=\'UserProfileCube\' and elementtype_id not in (
									select elementtypes.elementtype_id from elements, elementtypes, profilelayout where
									profilelayout.profile_id=$1 and
									profilelayout.element_id=elements.element_id and
									elements.elementtype_id=elementtypes.elementtype_id and
									elementtypes.elementpackage=\'UserProfileCube\'
									)'
							);

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID));

		$testarray =  pg_fetch_all($result);
		pg_close( $db_handle );
		

    	$fridgeDTOArray = array();
    	if( ! $testarray ) return $fridgeDTOArray;
    	
    	foreach( $testarray as $row ){
    		$fridgeDTO = new FridgeDTO();
    		$cubeInfoPlist = simplexml_load_file(  $row['elementpackagepath'] . "/" . "Info.plist" );
	        $cubeName = (string) $cubeInfoPlist['CubeName'];
	        
    		$fridgeDTO->setElementTypeID( $row['elementtype_id'] );
    		$fridgeDTO->setCubeName( $cubeName );
    		$fridgeDTOArray[] = $fridgeDTO;
    	}
    	
    	return $fridgeDTOArray;

    }

}

