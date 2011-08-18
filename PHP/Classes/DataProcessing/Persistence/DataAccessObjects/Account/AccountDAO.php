<?php
/**
 * AccountDAO Abstract Class File
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * AccountDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
abstract class AccountDAO extends AbstractDAO {
    
    /**
     * @return AccountDTO 
     */
    abstract public function registerNewAccount($accountName, $password, $userEmail, 
    											$firstName, $lastName, $gender,	
    											$birthMonth, $birthDay, $birthYear, $referalCode);

    /**
     * @return AccountDTO 
     */
    abstract public function userLogin($username, $password, $ipAddress, $userAgent);


	/**
	 * TEMP FUNCTION
	 * Get main profile id from the user id
	 * 
	 * @param $userID the user name to query on to get the user id
	 */
	abstract public function getMainProfileID( $userID );

    abstract public function getUserInformation( $userID );
    
}

