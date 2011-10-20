<?php
/**
 * PGSQLAccountDAO Class File
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
 * PGSQLAccountDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLAccountDAO extends AccountDAO {
 
     /**
     * @return AccountDTO 
     */
    public function registerNewAccount(	$accountName, $password, $userEmail, 
    									$firstName, $lastName, $gender,	
    									$birthMonth, $birthDay, $birthYear, $referalCode) {
        
		$birthday = $birthMonth . "-" . $birthDay . "-" . "-" . $birthYear;
		        
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_UserCreated, output_UserUnique, output_EmailUnique, output_User_ID FROM createNewUser($1, $2, $3, $4, $5, $6, $7, $8)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($accountName, $password, $userEmail, $firstName, $lastName, $gender, $birthday, $referalCode));

		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
        
		$accountDTO = new AccountDTO();
		
		if( $testarray['output_usercreated'] === 't' ) {
			$accountDTO->setRegistrationSuccessful();
		} else {
			if ( $testarray['output_userunique'] !== 't' ) {
        		if ( $testarray['output_emailunique']  !== 't' ) {
        			$accountDTO->setRegistrationError("Failed: Username already exists and email address already exists.");
        			$accountDTO->setRegistrationSuccessful(false);
        		} else {
        			$accountDTO->setRegistrationError("Failed: Username already exists.");
        			$accountDTO->setRegistrationSuccessful(false);
        		}
        	} else {
        		if ( $testarray['output_emailunique']  !== 't' ) {
        			$accountDTO->setRegistrationError("Failed: Email address already exists.");
        			$accountDTO->setRegistrationSuccessful(false);        				
        		}
        	}
		}
		
		//set the user id
		$userID = $testarray['output_user_id'];
		
        $userDTO = new UserDTO();
        $userDTO->setUserID($userID);
        $accountDTO->setUserDTO( $userDTO );
        
        return $accountDTO;
    }

     /**
     * @return userDTO 
     */
    public function userLogin($username, $password, $ipAddress, $userAgent){
        
        $userDTO = new UserDTO();
        //TODO: make appropriate database call
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

//        echo "connected to ". pg_dbname( $db_handle ) . "<br>";

        //Prepare a query for execution
        $result = pg_prepare($db_handle, "query", 'SELECT userLogin($1,$2,$3,$4)');
 
        // Execute the prepared query.  Note that it is not necessary to escape
        $result = pg_execute($db_handle, "query", array($username,$password,$ipAddress,$userAgent));
        $testarray = pg_fetch_assoc($result);
                
        pg_close( $db_handle );  		    
  		
  		//I need to figure out how to have pl/pgsql spit back a false or exception if the select fails.
  		//Might just include another argument to spit back that checks before assigning the userid,
  		//because that will probably be faster than the code below.  Reading about exceptions and so forth.
  		//If the php here fails then replace the 0s with userLogin and it all should work. I accidentally 
  		//updated my index.php again . . . and my test crap went "poop".
//        if ( $testarray['0'] !== '' && $testarray['0'] !== null ) {
//            $userID = $testarray['userlogin'];
//        } else {
//            $userID = 'blank return';
//        }
//  		
        $userDTO->setUserID($testarray['userlogin']);  //Set user_id from the results the db yacked back.
        return $userDTO;
    } 
    
    /**
	 * TEMP FUNCTION
	 * Get main profile id from the user id
	 */
	public function getMainProfileID( $userID ){
		
		//get handle
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

		//escape input variables
		$userID = pg_escape_string($userID);

		$query = 
			"SELECT i.profile_id from individualprofiles i, profiles p where " .
			"i.profile_id=p.profile_id and p.profilecreator='$userID'";

		$result = pg_query( $db_handle, $query);
		$resultSet = pg_fetch_array( $result, 0, PGSQL_ASSOC );
		$retval = $resultSet['profile_id'];

		pg_close( $db_handle );
		
		return $retval;
	}	
    
    public function getUserInformation( $userID ){
        $retVal = array();
        //get handle
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

        //escape input variables
        $userID = pg_escape_string($userID);

        $query = 
            "SELECT i.firstname, i.lastname from users i where " . "i.user_id='$userID'";

        $result = pg_query( $db_handle, $query);
        $resultSet = pg_fetch_array( $result, 0, PGSQL_ASSOC );
        $retVal['firstname'] = $resultSet['firstname'];
        $retVal['lastname'] = $resultSet['lastname'];
        
        pg_close( $db_handle );
        
        return $retVal;
    }   

}

