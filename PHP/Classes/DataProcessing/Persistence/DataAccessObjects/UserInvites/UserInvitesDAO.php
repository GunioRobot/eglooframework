<?php
/**
 * UserInvitesDAO Abstract Class File
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
 * UserInvitesDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
abstract class UserInvitesDAO extends AbstractDAO {
    
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
	abstract public function addUserInvite($userID, $emailAddress, $referalCode);
    
    /**
     * Checks if this referral code is valid (has it been used yet?)
     *
     * @param referralCode 
     * @return boolean 
     */
	abstract public function isReferralCodeValid($referralCode);

    /**
     * Checks if this referral code is Unique
     *
     * @param referralCode 
     * @return boolean 
     */
	abstract public function isReferralCodeUnique($referralCode);


    /**
     * Mark that this referral code has been used
     *
     * @param referralCode
     * @return boolean
     */
	abstract public function markReferralCodeAsUsed($referralCode);

    /**
     * Get the number of invites a user has left.
     *
     * @param userID
     * @return integer
     */
	abstract public function getNumberOfInvitesLeft($userID);

    /**
     * Set the number of invites a user has.
     *
     * @param userID
     * @param numberOfInvites
     * @return boolean
     */
	abstract public function setNumberOfInvites($userID, $numberOfInvites);
	
	/**
	 * This method returns the user association level.
	 * 
	 * @param - userID, the user id of the user we want to query
	 * @return - int, the user association level
	 */
	abstract public function getUserAssociationLevel($userID);
	
}

