<?php
/**
 * PGSQLCubeDAO Class File
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
 * PGSQLCubeDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLCubeDAO extends CubeDAO {
	
	//*   
    public function getCubeInstance( $cubeID ) {
    	//get a dto based on element unique ID
    	//Will return only the info based off element table.  
		//For now at least.
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_ElementType_ID, output_Creator_ID, output_ElementPackagePath FROM getElementInstance($1, $2);');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($cubeID, null));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		$cubeDTO = new CubeDTO();
		$cubeDTO->setElementInstanceID($cubeID);
		$cubeDTO->setElementTypeID($testarray['output_elementtype_id']);
	//	$cubeDTO->setElementCreatorProfileID($testarray['output_creator_id']);
		$cubeDTO->setElementInstanceCreatorProfileID($testarray['output_creator_id']);
		$cubeDTO->setDirectoryLocation($testarray['output_elementpackagepath']);
		
		return $cubeDTO;
    }
 
 	//* Images, ProfileImages, Profile, Friends cube, BlogCube (list of all blogs), links cube(not now), Quotes(not now)
 	public function createNewCubeInstance($profileID, $cubeTypeID, $elementSpecificArray) {
 		//Creates a new cube.  Needs to have an array passed to it.
 		$inputQuery= 'SELECT output_Successful, output_Element_ID FROM createNewElement($1, $2';
 		$inputQueryArray=array($profileID, $cubeTypeID);
 		$numberOfVariables=2;
 		
 		//Need check to make sure $elementSpecificArray is an array and is populated.
 		if (isset($elementSpecificArray)) {
 			foreach ($elementSpecificArray as $value) {
 				$inputQuery.=', $'."$numberOfVariables";
 				$inputQueryArray[]=$value;
 			}
 		}
 		$inputQuery.=')';
 		
 		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", $inputQuery);

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", $inputQueryArray);

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
 		
 		$cubeDTO = new CubeDTO();
 		return $testarray['output_element_id'];
 		//$cubeDTO->setElementCreatorProfileID($profileID);
 		//$cubeDTO->setElementID($cubeTypeID);
 	}
    
    // This should mirror what getCubeDefinition is doing, except it's DB based
    public function getCubeBundleInfo( $cubeID ) {
        //Deals with the element type dont know what the permission level is.
    }
    
    // Cubes user has made*
    public function getCubeInstanceList( $profileID ) {
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT Element_ID, ElementType_ID FROM Elements WHERE Creator_ID=$1');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID));

		$testarray =  pg_fetch_all($result);
		pg_close( $db_handle );
		
		$retval=array();
		
		//Make an array of cubedtos
		foreach ($testarray as $row) {
			$cubeDTO = new CubeDTO();
			$cubeDTO->setElementInstanceID($row['element_id']);
			$cubeDTO->setElementID($row['elementtype_id']);
			$retval[] = $cubeDTO;
		}
		
		return $retval;
    }
    
    public function removeCubeInstance( $cubeElementInstance ) {
        
    }
    
    
    public function cubeSingletonExists( $profileID, $cubeTypeID ){
    	
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

    		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT elements.element_id FROM elements 
													where elements.elementtype_id =$1 
													and elements.creator_id =$2');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($cubeTypeID, $profileID));

		$testarray =  pg_fetch_all($result);
		pg_close( $db_handle );
		
		if( $testarray ) {
			return $testarray[0]['element_id'];
		} else {
			return null;
		}
    }
    
}

