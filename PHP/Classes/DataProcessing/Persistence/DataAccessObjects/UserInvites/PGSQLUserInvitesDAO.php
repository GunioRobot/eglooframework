<?php
/**
 * PGSQLUserInvitesDAO Class File
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
 * PGSQLUserInvitesDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLUserInvitesDAO extends UserInvitesDAO {
     /**
     * Add a user invite.
     * 
     * 1) Decrement the number of invites left
     * 2) Persist referred email address and referal code
     * 
     * @param userID - the userID of the referrer
     * @param emailAddress - the emailAddress of the person being referred
     * @param referalCode - the unique generated referal code
     * @return boolean 
     */
	public function addUserInvite($userID, $emailAddress, $referralCode){
	    
	    $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT addUserInvite($1, $2, $3)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID, $emailAddress, $referralCode));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		if( $testarray['adduserinvite'] === 't' ){
			return true;
		} else {
			return false;
		}
        
	}
    
    /**
     * Checks if this referral code is valid (has it been used yet?)
     *
     * @param referralCode 
     * @return boolean 
     */
	public function isReferralCodeValid($referralCode){
	    $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT checkReferral_ID($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($referralCode));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		if( $testarray['checkreferral_id'] === 't' ){
			return true;
		} else {
			return false;
		}
		
	}

	/**
     * Checks if this referral code is Unique
     *
     * @param referralCode 
     * @return boolean 
     */
	public function isReferralCodeUnique($referralCode){
		
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT isReferral_IDUnique($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($referralCode));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );

		if( $testarray['isreferral_idunique'] === 't'){
			return true;
		} else {
			return false;
		}
	}


    /**
     * Mark that this referral code has been used
     *
     * @param referralCode
     * @return boolean
     */
	public function markReferralCodeAsUsed($referralCode){

	}
	
	//We should also have a way of making manually referencing a user if we have the above method.
	
	
	/**
     * Get the number of invites a user has left.
     *
     * @param userID
     * @return integer
     */
	public function getNumberOfInvitesLeft($userID){
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT getNumberOfInvitesLeft($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		return $testarray['getnumberofinvitesleft'];		
	}
	
   /**
     * Set the number of invites a user has.
     *
     * @param userID
     * @param numberOfInvites
     * @return boolean
     */
	public function setNumberOfInvites($userID, $numberOfInvites){
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT setNumberOfInvites($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID, $numberOfInvites));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		if( $testarray['setnumberofinvites'] === 't'){
			return true;
		} else {
			return false;
		}					
	}
	
	/**
	 * This method returns the user association level.
	 * 
	 * @param - userID, the user id of the user we want to query
	 * @return - int, the user association level
	 */
	public function getUserAssociationLevel($userID){
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT getUserAssociationLevel($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		return (int) $testarray['getuserassociationlevel'];		
		
	}
	
 }

