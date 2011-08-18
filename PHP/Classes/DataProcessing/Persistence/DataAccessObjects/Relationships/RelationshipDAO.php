<?php
/**
 * RelationshipDAO Abstract Class File
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
 * RelationshipDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
abstract class RelationshipDAO extends AbstractDAO {
    
    /**
     * This defines the possible relationship types available
     */
    public static $FRIEND_RELATION = "Friends";
    
     /**
     * This function creates a new bidirectional request in the database.
     * 
     * @param requesterProfileID - The profile ID of the person requesting the relationship
     * @param accepterProfileID - The profile ID of the person who will need to accept the relationship
     * @param relationshipType - The type of relationship requested.  This relationship must be one of the
     * 							types listed as constants in this class.
     * @return boolean - true if successful, false otherwise
     */
	abstract public function requestBIDirectionalRelationship( 	$requesterProfileID,
																$accepterProfileID,
																$relationshipType );
    

    /**
     * This function creates a new bidirectional relationship request in the database.
     * 
     * @param relationshipID - The relationship id
     * @param profileID - the accepter ID (The current logged in profile ID).  This is for 
     * 					  ensuring that this profileID is actually the one who can accept it.
	 *
     * @return boolean - true if successful, false otherwise 
     */
	abstract public function acceptRelationship( $relationshipID, $accepterProfileID );


	/**
	 * This function declines a relationship request by removing the row from the database
	 * 
	 * 
     * @param relationshipID -  The relationshipID
     * @param accepterProfileID - The profile ID of the person who declined the accept (current user profileID)
     * 
     * @return boolean - true if successful, false otherwise
     */
    abstract public function declineRelationship( $relationshipID, $accepterProfileID );


    /**
     * This function removes a relationship
     * 
     * @param relationshipID -  The relationshipID
     * @param profileID - The profile ID of the person who declined the accept (current user profileID)
     * 
     * @return boolean - true if successful, false otherwise
     */
    abstract public function removeRelationship( $relationshipID, $profileID );
    	

	/**
	 * This function gets all relationship requests for a particular profileID
	 * 
	 * @param profileID - the particular profile id to query on
	 * 
	 * @return array - An array of relationshipRequestDTOs
	 */
	abstract public function getAllRelationshipRequests( $requestedProfileID, $userProfileID );

	
	/**
	 * This function gets all relationships for a particular profileID
	 * 
	 * @param profileID - the particular profile id to query on
	 * 
	 * @return array - An array of relationshipDTOs
	 */
	abstract public function getAllRelationships( $requestedProfileID, $userProfileID );	
	
	/**
	 * This function returns all relationships between the two profiles
	 * 
	 * @param profileID1
	 * @param profileID2
	 */
	abstract public function getProfilesRelationships( $profileID1, $profileID2 );
}
