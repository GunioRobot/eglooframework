<?php
/**
 * PGSQLRankingDAO Class File
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
 * @author Keith Buel
 * @author Matthew Brennan
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * PGSQLRankingDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
class PGSQLRankingDAO extends RankingDAO {

	/*
	 * See Comments in RankingDAO
	 */
	public function createNewRanking($profileID, $elementID, $ranking){
		
		//Set the return value's initial value
		$returnVal = NULL;
		
		//Create a handle to the database
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//If the handle was successfully created, then proceed.
		if($db_handle){
		
			//Prepare a query for execution
  			$result = pg_prepare($db_handle, "query", 'SELECT output_successful, output_datecreated FROM rankelement($1, $2, $3);');	

			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "query", array($profileID, $elementID, $ranking));
			
			$testarray =  pg_fetch_assoc($result);
			pg_close( $db_handle );
		
			//If the ranking was created successfully, create a RankingDTO object for return
			if( $testarray['output_successful'] === 't' ) {
				
				//Create RankingDTO
				$returnVal = new RankingDTO();
				$returnVal->setElementID($elementID);
				$returnVal->setProfileID($profileID);
				$returnVal->setRank($ranking);
				$returnVal->setCreationDate($testarray['output_datecreated']);
				$returnVal->setSuccessful(TRUE);
				
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLRankingDAO::createNewRanking ERROR: The ranking was not created successfully" );
        	    $returnVal = NULL;
        	    eGlooLogger::writeLog( eGlooLogger::DEBUG, "SessionHandler::READ -- sessionid = $sessionID" );
        	}
        	
		} else {			
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLRankingDAO::createNewRanking ERROR: The db handle was not created successfully" );
		}
 		
 		return $returnVal;
	}
	
	/*
	 * See RankingDAO
	 */
    public function getProfileElementRanking( $profileID, $elementID ){
        
        $elementID = '-9223372036854775808';
        $profileID = '-9223372036854775807';
        
        //Initialize the returned value
  		$result = NULL;
    
    	//Create a handle to the database
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
  
    	//If the handle was successfully created, then proceed with retrieving the ranking.
		if($db_handle){
  			
  			//Create a query
			$result = pg_query($db_handle, "SELECT ranking, dateranked FROM profileelementrankings WHERE element_id = $elementID and profile_id = $profileID;");
			
			//If the query was successful, create the Ranking object
			if  ($result) {
				
				$assoc_array =  pg_fetch_assoc($result);
				
				$result = new RankingDTO();
				$result->setCreationDate($assoc_array['dateranked']);
				$result->setElementID($elementID);
				$result->setProfileID($profileID);
				$result->setRank($assoc_array['ranking']);
				$result->setSuccessful(TRUE);
				
			} else {	
				$result = NULL;
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLRankingDAO::getProfileElementRanking ERROR: The query did not successfully run: ".pg_last_error() );
			}
   
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLRankingDAO::getProfileElementRanking ERROR: The db handle was not created successfully" );
		}
		
	    pg_close( $db_handle );
	    
 		return $result;
    }

	/*
	 * See RankingDAO
	 */
    public function getElementRanking( $elementID ){
    	
    	//Initialize the returned value
  		$result = NULL;
    	
    	//Create a handle to the database
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
  
    	//If the handle was successfully created, then proceed.
		if($db_handle){
  			
  			//Create a query
			$result = pg_query($db_handle, "SELECT rank FROM elements WHERE element_id = $elementID;");
			
			//If the query was successful, extract the Rank value for the row.
			if  ($result) {
				$array =  pg_fetch_assoc($result);
				$result = $array['rank'];
			} else {	
				$result = NULL;
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLRankingDAO::getElementRanking ERROR: The query did not successfully run: ".pg_last_error() );
			}
	
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLRankingDAO::getElementRanking ERROR: The db handle was not created successfully" );
		}
		
	    pg_close( $db_handle );
		
 		return $result;
    }
    
}