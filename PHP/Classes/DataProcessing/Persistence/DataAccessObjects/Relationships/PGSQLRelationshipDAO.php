<?php
/**
 * PGSQLRelationshipDAO Class File
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
 * PGSQLRelationshipDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLRelationshipDAO extends RelationshipDAO {
 	
 	
 	
    /**
     * This function creates a new bidirectional request in the database.
     * 
     * @param requesterProfileID - The profile ID of the person requesting the relationship
     * @param accepterProfileID - The profile ID of the person who will need to accept the relationship
     * @param relationshipType - The type of relationship requested.  This relationship must be one of the
     * 							types listed as constants in this class.
     * @return boolean - true if successful, false otherwise
     */
	public function requestBIDirectionalRelationship( 	$requesterProfileID,
														$accepterProfileID,
														$relationshipType ){
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

        // TODO Make sure to check against relationship type constants

		//escape input variables
		$requesterProfileID = pg_escape_string($requesterProfileID);
		$accepterProfileID = pg_escape_string($accepterProfileID);
		$relationshipType = pg_escape_string($relationshipType);

		$query = "insert into relationships ( bidirectionalrelationship, accepted, accepterprofile_id, requesterprofile_id ) values ('true', 'false', '$accepterProfileID', '$requesterProfileID')";
		$result = pg_query( $db_handle, $query);
		
		$query = "insert into bidirectionalrelationships (relationship_id, profile_id1, profile_id2, relationshiptype) " .
				"values (currval('seq_relationshps_relationship_id'), '$requesterProfileID', '$accepterProfileID', '$relationshipType')";
		$result = pg_query( $db_handle, $query);

		pg_close( $db_handle );

		//TODO: exception handeling if invite already exists													
	}
    

    /**
     * This function creates a new bidirectional relationship request in the database.
     * 
     * @param relationshipID - The relationship id
     * @param profileID - the accepter ID (The current logged in profile ID).  This is for 
     * 					  ensuring that this profileID is actually the one who can accept it.
	 *
     * @return boolean - true if successful, false otherwise 
     */
	public function acceptRelationship( $relationshipID, $accepterProfileID ){
		
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape input variables
		$relationshipID = pg_escape_string($relationshipID);
		$accepterProfileID = pg_escape_string($accepterProfileID);

		$query = "update relationships set accepted = 'True' where relationship_id = '$relationshipID' and accepterprofile_id = '$accepterProfileID'";

		$result = pg_query( $db_handle, $query);

		pg_close( $db_handle );
	}


	/**
	 * This function declines a relationship request by removing the row from the database
	 * 
	 * 
     * @param relationshipID -  The relationshipID
     * @param accepterProfileID - The profile ID of the person who declined the accept (current user profileID)
     * 
     * @return boolean - true if successful, false otherwise
     */
    public function declineRelationship( $relationshipID, $accepterProfileID ){
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape input variables
		$relationshipID = pg_escape_string($relationshipID);
		$accepterProfileID = pg_escape_string($accepterProfileID);

		$query = "delete from relationships where relationship_id = '$relationshipID' and accepterprofile_ID = '$accepterProfileID'";

		$result = pg_query( $db_handle, $query);

		pg_close( $db_handle );
    	
    }


    /**
     * This function removes a relationship
     * 
     * @param relationshipID -  The relationshipID
     * @param profileID - The profile ID of the person who declined the accept (current user profileID)
     * 
     * @return boolean - true if successful, false otherwise
     */
    public function removeRelationship( $relationshipID, $profileID ){
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape input variables
		$relationshipID = pg_escape_string($relationshipID);
		$profileID = pg_escape_string($profileID);

		$query = "delete from relationships where relationship_id = '$relationshipID' and ( accepterprofile_ID = '$profileID' or requesterprofile_id = '$profileID' )";

		$result = pg_query( $db_handle, $query);

		pg_close( $db_handle );
    	
    }

    	

	/**
	 * This function gets all relationship requests for a particular profileID
	 * 
	 * @param profileID - the particular profile id to query on
	 * 
	 * @return array - An array of relationshipRequestDTOs
	 */
	public function getAllRelationshipRequests( $requestedProfileID, $userProfileID ){


		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape name
		$requestedProfileID = pg_escape_string($requestedProfileID);

		//get bidirectional first
		$query = "select r.relationship_id, r.requesterprofile_id, p.profilename, br.relationshiptype " .
				"from relationships r, profiles p, bidirectionalrelationships br " .
				"where r.accepted = 'false' " .
				"and r.accepterprofile_id = '$requestedProfileID' " .
				"and r.bidirectionalrelationship = 'true'  " .
				"and r.requesterprofile_id = p.profile_id " .
				"and r.relationship_id = br.relationship_id";

		$result = pg_query( $db_handle, $query);

		$relationShipRequests = array();

		for($row = 0; $row< pg_num_rows( $result ); $row++){
			$resultSet = pg_fetch_array( $result, $row, PGSQL_ASSOC );
			$BIrDTO = new BIDirectionalRelationshipDTO();
			$BIrDTO->setOtherProfileID( $resultSet['requesterprofile_id'] );
			$BIrDTO->setOtherProfileName( $resultSet['profilename'] );
			$BIrDTO->setRelationshipType( $resultSet['relationshiptype'] );
			$BIrDTO->setRelationshipID( $resultSet['relationship_id']);        
	        $relationShipRequests[] = $BIrDTO;
		}
 
		pg_close( $db_handle );
		
		return $relationShipRequests;
		
		//TODO unidirectional
	}

	
	/**
	 * This function gets all relationships for a particular profileID
	 * 
	 * @param profileID - the particular profile id to query on
	 * 
	 * @return array - An array of relationshipDTOs
	 */
	public function getAllRelationships( $requestedProfileID, $userProfileID ){


		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape name
		$requestedProfileID = pg_escape_string($requestedProfileID);

		//get bidirectional first
		$query = 
			"select relationship_id, otherprofile_id, otherprofilename, relationshiptype " .
			"from (  " .
			
			"select r.relationship_id, r.requesterprofile_id as otherprofile_id, p.profilename as otherprofilename, br.relationshiptype  " . 
			"from relationships r, profiles p, bidirectionalrelationships br  " .
			"where r.accepted = 'true' " .
			"and r.accepterprofile_id = '$requestedProfileID'  " .
			"and r.bidirectionalrelationship = 'true'  " . 
			"and r.requesterprofile_id = p.profile_id  " .
			"and r.relationship_id = br.relationship_id  " . 
			
			"union  " . 
			
			"select r.relationship_id, r.accepterprofile_id as otherprofile_id, p.profilename as otherprofilename, br.relationshiptype  " . 
			"from relationships r, profiles p, bidirectionalrelationships br  " .
			"where r.accepted = 'true'  " .  
			"and r.requesterprofile_id = '$requestedProfileID'  " .
			"and r.bidirectionalrelationship = 'true'  " . 
			"and r.accepterprofile_id = p.profile_id  " .
			"and r.relationship_id = br.relationship_id  " . 
			") as foo  ";



		$result = pg_query( $db_handle, $query);

		$relationShipRequests = array();

		for($row = 0; $row< pg_num_rows( $result ); $row++){
			$resultSet = pg_fetch_array( $result, $row, PGSQL_ASSOC );
			$BIrDTO = new BIDirectionalRelationshipDTO();
			$BIrDTO->setOtherProfileID( $resultSet['otherprofile_id'] );
			$BIrDTO->setOtherProfileName( $resultSet['otherprofilename'] );
			$BIrDTO->setRelationshipType( $resultSet['relationshiptype'] );
			$BIrDTO->setRelationshipID( $resultSet['relationship_id']);        
	        $relationShipRequests[] = $BIrDTO;
		}
 
		pg_close( $db_handle );
		
		return $relationShipRequests;

		//TODO uni directional
		
	}
	
	
	
	 
	/**
	 * This function returns all relationships between the two profiles
	 * 
	 * @param profileID1
	 * @param profileID2
	 */
	public function getProfilesRelationships( $profileID1, $profileID2 ){
	
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape name
		$profileID1 = pg_escape_string($profileID1);
		$profileID2 = pg_escape_string($profileID2);
		//get bidirectional first
		$query = 
			"select relationshiptype " .
			"from (  " .
			
			"select br.relationshiptype  " . 
			"from relationships r, bidirectionalrelationships br  " .
			"where r.accepted = 'true' " .
			"and r.accepterprofile_id = '$profileID1'  " .
			"and r.requesterprofile_id = '$profileID2'  " .
			"and r.bidirectionalrelationship = 'true'  " . 
			"and r.relationship_id = br.relationship_id  " . 
			
			"union  " . 
			
			"select br.relationshiptype  " . 
			"from relationships r, bidirectionalrelationships br  " .
			"where r.accepted = 'true'  " .  
			"and r.accepterprofile_id = '$profileID2'  " .
			"and r.requesterprofile_id = '$profileID1'  " .
			"and r.bidirectionalrelationship = 'true'  " . 
			"and r.relationship_id = br.relationship_id  " . 
			") as foo  ";



		$result = pg_query( $db_handle, $query);

		$relationShipRequests = array();

		for($row = 0; $row< pg_num_rows( $result ); $row++){
			$resultSet = pg_fetch_array( $result, $row, PGSQL_ASSOC );
			$BIrDTO = new BIDirectionalRelationshipDTO();
			$BIrDTO->setRelationshipType( $resultSet['relationshiptype'] );
	        $relationShipRequests[] = $BIrDTO;
		}
 
		pg_close( $db_handle );
		
		return $relationShipRequests;
	}	
 }

