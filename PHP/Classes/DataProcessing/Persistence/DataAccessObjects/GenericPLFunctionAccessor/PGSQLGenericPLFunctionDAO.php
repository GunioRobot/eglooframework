<?php
/**
 * PGSQLGenericPLFunctionDAO Class File
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
 * PGSQLGenericPLFunctionDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLGenericPLFunctionDAO extends GenericPLFunctionDAO {
 	
	private $DB_QUERIES_XML_LOCATION = "../XML/DBQueries.xml";
 
	/**
     * @param queryname - the name of the query we want
     * @param inputValues - key => value mappings of input variables required for this query
 	 */
    public function selectGenericData( $queryName, $inputValues ){
    		
		/**
		 * Ensure that the input value is an actual array
		 */
		if( !is_array( $inputValues ) ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::selectGenericData ERROR: input parameter inputValues is not an array" );
			return false;
		}
		
		$genericQueryKeyLookup = $queryName;	
		$genericPLSelectQuery = null;

	
		/**
		 * check the cache gateway for this query object
		 */
		$cacheGateway = CacheGateway::getCacheGateway();
        
        if ( ( $genericPLSelectQuery = $cacheGateway->getObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $genericQueryKeyLookup, 'GenericDAOQueries' ) ) == null ) {

			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::selectGenericData - GenericPLSelectQuery $genericQueryKeyLookup, has not been made yet, building from XML" );

  			/**
			 *  It's not in the cache... build the query from xml
			 */	
			 if( ( $genericPLSelectQuery = $this->buildPLSelectQueryFromXML( $queryName ) ) == null ){
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::selectGenericData - GenericSPLSelectQuery $genericQueryKeyLookup, error building query from XML, returning null" );
				return null;
			}
        } else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::selectGenericData - GenericPLSelectQuery $genericQueryKeyLookup, Grabbed from cache!" );
        }
		
	
		/**
		 * ensure we have all the needed input variables supplied
		 */	
		$orderedInputArray = array();
		foreach( $genericPLSelectQuery->getVariableOrderArray() as $inputParam ){

			if( ! array_key_exists($inputParam, $inputValues) ) {
    			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::selectGenericData ERROR: $inputParam is not set!" );
    			//TODO throw exception
    			return false;
    		}
			$orderedInputArray[] = 	$inputValues[ $inputParam ];
		}
		
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::selectGenericData  " . $genericPLSelectQuery->getQuery() );
		
		/**
		 * Make call to DB
		 */
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
		$result = pg_prepare( $db_handle, "query", $genericPLSelectQuery->getQuery() );

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", $orderedInputArray );
		$resultSet =  pg_fetch_all($result);
		pg_close( $db_handle );

		$retval = null;
		
       /**
        * Interpret the results from the DB
        */ 
		if( $genericPLSelectQuery->isMultipleResults() ) {
			
			$genericCubeItems = array();
			
			//did the DB return any results back?		
			if($resultSet){
				
				//iterate over the result set	
				foreach( $resultSet as $row ){
					
					$gcDTO = new GenericQueryDTO();
					
					//Fill up the general cube DTO
					foreach( $genericPLSelectQuery->getSelectItems() as $key => $value){
						$resultSetValue = $row[ strtolower( (string) $key ) ] ;
						
						//the value of the select items designates the key's value type	
						if($value == "boolean"){
							if( $resultSetValue === 't' ){
								$resultSetValue = true;
							} else {
								$resultSetValue = false;
							}
						}
						$gcDTO->setFieldValue( $key, $resultSetValue );
					}
			        $genericCubeItems[] = $gcDTO;
				}
			}
			$retval = $genericCubeItems;
			
		} else {
			
			$gcDTO = new GenericQueryDTO();
			
			//did the DB return any results back?	
			if($resultSet){
				
				//iterate over the result set
				foreach( $resultSet as $row ){
					
					//fill up the general cube dto
					foreach( $genericPLSelectQuery->getSelectItems() as $key => $value){
						$resultSetValue = $row[ strtolower( (string) $key ) ] ;
								
						//the value of the select items designates the key's value type
						if($value === "boolean"){
								
							if( $resultSetValue === 't' ){
								$resultSetValue = true;
							} else {
								$resultSetValue = false;
							}
						}
						$gcDTO->setFieldValue( $key, $resultSetValue );
					}
					
					//only go get the first one.
					break;
				}
			}
			$retval = $gcDTO;
		}
 
		return $retval;		

	}
 
 	
 	/**
     * @param queryname - the name of the query we want
 	 * @return GenericSelectQuery
 	 */
 	private function buildPLSelectQueryFromXML( $queryName ){
 
 
		$xmlSelectQuery = null;
		$outputItems = array();
		$variableOrderArray = array();	
		$multipleResults = false;
		$functionName = "";		
		$outputVars = "";	
		$inputVars = "";	
		
        $dbQueriesXMLObject = simplexml_load_file( $this->DB_QUERIES_XML_LOCATION );
        
        
        $selectQueries = $dbQueriesXMLObject->xpath("/DBQueries:Queries/PLSelect[@name='$queryName']");
       	$xmlSelectQuery = $selectQueries[0];		
       	
		if( $xmlSelectQuery === null ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLGenericPLFunctionDAO::buildPLSelectQueryFromXML ERROR: select query $queryName can not be found." );
			return null;
		}
		
		//check if we want multiple results
		if( strtolower( (string) $xmlSelectQuery['multipleResults'] ) === "true" ){
			$multipleResults = true;
		}
		
		$functionName = (string) $xmlSelectQuery['functionName'];
									
		//output vars
		foreach( $xmlSelectQuery->xpath( 'child::PLOutputVariable' ) as $outputVariable ){
			
			$outVar = strtolower( (string) $outputVariable['name'] );
			
			if($outputVars === ""){
				$outputVars = $outVar; 
			} else {
				$outputVars = $outputVars . ", " . $outVar;	
			}
			$outputItems[ $outVar ] = (string) $outputVariable['type'];
		}
		
		//input vars
		$inputCounter = 1;
		foreach( $xmlSelectQuery->xpath( 'child::PLInputVariable' ) as $inputVariable ){
			
			$outVar = strtolower( (string) $outputVariable['name'] );
			
			if($inputVars === ""){
				$inputVars = '$' . $inputCounter; 
			} else {
				$inputVars = $inputVars . ', $' . $inputCounter; 
			}
			$inputCounter++;
			
			$variableOrderArray[ (string) $inputVariable['order'] ]	=  (string) $inputVariable['name'];
		}
			
		ksort( $variableOrderArray );

		//create query:
		$query = "SELECT $outputVars FROM $functionName($inputVars)";
		
		$genericPLSelectQuery = new GenericPLSelectQuery($query, $variableOrderArray, $multipleResults, $outputItems );
 		
 		//cache this object
 		$genericQueryKeyLookup = $queryName;
		$cacheGateway = CacheGateway::getCacheGateway();
		$cacheGateway->storeObject( eGlooConfiguration::getUniqueInstanceIdentifier() . '::' . $genericQueryKeyLookup, $genericPLSelectQuery, 'GenericDAOQueries' );
		
		return $genericPLSelectQuery;
 		
 	}
	
	
 }

