<?php
/**
 * PGSQLUserProfileDAO Class File
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
 * PGSQLUserProfileDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLUserProfileDAO extends UserProfileDAO {

  // TODO Rename this to PGSQLUserProfileDAO
 
    /**
     * update profile information
     *
     * @param UserUserProfileDTO 
     * @return boolean 
     */
    public function setProfile($profileDTO, $userID){
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
        
        //get interested in
        $interestedInArray = $profileDTO->getInterestedIn();
        if( $interestedInArray[ UserProfileDTO::$MEN ] === true ){
            //call db function to set interested in men
	  		//Prepare a query for execution
	  		$result = pg_prepare($db_handle, "addInteresedInMen", 'SELECT addSexualPreference($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addInteresedInMen", array($userID, UserProfileDTO::$MEN));
            eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLUserProfileDAO: Setting Interest Men" );
        } else {
            //call db function to remove interested in men
            //Prepare a query for execution
	  		$result = pg_prepare($db_handle, "dropInterestedInMen", 'SELECT dropSexualPreference($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropInterestedInMen", array($userID, UserProfileDTO::$MEN));
            eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLUserProfileDAO: Removing Interest Men" );
        }

        if( $interestedInArray[ UserProfileDTO::$WOMEN ] === true ){
            //call db function to set interested in women
            //Prepare a query for execution
            $result = pg_prepare($db_handle, "addInterestedInWomen", 'SELECT addSexualPreference($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addInterestedInWomen", array($userID, UserProfileDTO::$WOMEN));
            eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLUserProfileDAO: Setting Interest Women" );
        } else {
            //call db function to remove interested in women
            //Prepare a query for execution
            $result = pg_prepare($db_handle, "dropInterestedInWomen", 'SELECT dropSexualPreference($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropInterestedInWomen", array($userID, UserProfileDTO::$WOMEN));
            eGlooLogger::writeLog( eGlooLogger::DEBUG, "PGSQLUserProfileDAO: Removing Interest Women" );
        }
        
        //get looking for
        $lookingForArray = $profileDTO->getLookingFor();
        if( $lookingForArray[ UserProfileDTO::$FRIENDSHIP ] === true ){
            //call db function to set looking for friendship
  	  		//Prepare a query for execution
	  		$result = pg_prepare($db_handle, "addLookingForFriendship", 'SELECT addLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addLookingForFriendship", array($userID, UserProfileDTO::$FRIENDSHIP));
            
        } else {
            //call db function to remove looking for friendship     
            //Prepare a query for execution
	  		$result = pg_prepare($db_handle, "dropLookingForFriendship", 'SELECT dropLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropLookingForFriendship", array($userID, UserProfileDTO::$FRIENDSHIP));
        
        }

        if( $lookingForArray[ UserProfileDTO::$DATING ] === true ){
            //call db function to set looking for Dating
  	  		//Prepare a query for execution
	  		$result = pg_prepare($db_handle, "addLookingForDating", 'SELECT addLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addLookingForDating", array($userID, UserProfileDTO::$DATING));

        } else {
            //call db function to remove looking for Dating
            //Prepare a query for execution
	  		$result = pg_prepare($db_handle, "dropLookingForDating", 'SELECT dropLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropLookingForDating", array($userID, UserProfileDTO::$DATING));
        }

        if( $lookingForArray[ UserProfileDTO::$RELATIONSHIP ] === true ){
            //call db function to set looking for RELATIONSHIP
  	  		//Prepare a query for execution
	  		$result = pg_prepare($db_handle, "addLookingForRelationship", 'SELECT addLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addLookingForRelationship", array($userID, UserProfileDTO::$RELATIONSHIP));

        } else {
            //call db function to remove looking for RELATIONSHIP
            //Prepare a query for execution
	  		$result = pg_prepare($db_handle, "dropLookingForRelationship", 'SELECT dropLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropLookingForRelationship", array($userID, UserProfileDTO::$RELATIONSHIP));
        }
                
        if( $lookingForArray[ UserProfileDTO::$RANDOMPLAY ] === true ){
            //call db function to set looking for RANDOMPLAY
  	  		//Prepare a query for execution
	  		$result = pg_prepare($db_handle, "addLookingForRandomPlay", 'SELECT addLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addLookingForRandomPlay", array($userID, UserProfileDTO::$RANDOMPLAY));

        } else {
            //call db function to remove looking for RANDOMPLAY
            //Prepare a query for execution
	  		$result = pg_prepare($db_handle, "dropLookingForRandomPlay", 'SELECT dropLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropLookingForRandomPlay", array($userID, UserProfileDTO::$RANDOMPLAY));
        }
        
        if( $lookingForArray[ UserProfileDTO::$WHATEVER ] === true ){
            //call db function to set looking for WHATEVER
  	  		//Prepare a query for execution
	  		$result = pg_prepare($db_handle, "addLookingForWhatever", 'SELECT addLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "addLookingForWhatever", array($userID, UserProfileDTO::$WHATEVER));

        } else {
            //call db function to remove looking for WHATEVER
            //Prepare a query for execution
	  		$result = pg_prepare($db_handle, "dropLookingForWhatever", 'SELECT dropLookingFor($1, $2)');
	
			// Execute the prepared query.  Note that it is not necessary to escape
			$result = pg_execute($db_handle, "dropLookingForWhatever", array($userID, UserProfileDTO::$WHATEVER));
        }

		//Email
  		//Prepare a query for execution
        // TODO ADD THIS BACK IN
//  		$result = pg_prepare($db_handle, "setEmail", 'SELECT setEmailAddress($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
//		$result = pg_execute($db_handle, "setEmail", array($userID, $profileDTO->getEmail()));

		//Birthdate
  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "setBDay", 'SELECT setBirthDate($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "setBDay", array($userID, $profileDTO->getBirthDate()));

		//Sex
  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "setSex", 'SELECT setSex($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "setSex", array($userID, $profileDTO->getSex()));

		//Hometown
        //[countryname] - display field
        //[countryisocode] - key field for above
        //[us_stateprovincename] - display field
        //[us_stateprovince_id] - key field for above
        //[us_citytown] - display field
		
  		//Prepare a query for execution
        // TODO THIS SHOULD NOT BE HARDCODED TO US
  		$result = pg_prepare($db_handle, "setUS_HomeTown", 'SELECT setUS_HomeTown($1, $2, $3, $4)');
		
		$homeTown = $profileDTO->getHomeTown();
		
		// Execute the prepared query.  Note that it is not necessary to escape
        // TODO THIS SHOULD NOT BE HARDCODED TO US
		$result = pg_execute($db_handle, "setUS_HomeTown", array($userID, 'US', $homeTown, 'MD'));

		//Residence
		//[countryname] - display
		//[alpha_2isocode] - key for above
		//[us_addressline1] - display
		//[us_addressline2] - display
		//[us_citytown] - display
		//[us_stateprovincename] - display
		//[us_stateprovince_id] - key for above
		//[us_postalcode1] - display
		//[us_postalcode2] - display
		
  		//Prepare a query for execution
        // TODO ADD THIS BACK IN
//  		$result = pg_prepare($db_handle, "setResidence", 'SELECT setResidence($1, $2, $3, $4, $5, $6, $7, $8)');
		
//		$residence = $profileDTO->getResidence();
				
		// Execute the prepared query.  Note that it is not necessary to escape
//		$result = pg_execute($db_handle, "setResidence", array($residence['alpha_2isocode'], $userID, $residence['us_addressline1'], $residence['us_addressline2'], $residence['us_citytown'], $residence['us_stateprovince_id'], $residence['us_postalcode1'], $residence['us_postalcode2']));

    	pg_close( $db_handle );
    }

    /**
     * This function retrieves a created Profile
     * 
     * @param profileID
     * @return UserProfileDTO
     */
    public function getProfile( $profileID ){
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
		$userProfileDTO = new UserProfileDTO();
		
        //Get interested in.
		//Prepare a query for execution
		$result = pg_prepare($db_handle, "getGender", 'SELECT Gender FROM SexualPreferences WHERE Profile_ID=$1');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getGender", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_all($result);
		
		$m = false;
		$w = false;
		
		if($testarray){	
			foreach ($testarray as $value) {
				switch ($value['gender']){
					case UserProfileDTO::$MEN:
						$m = true;
						break;
					case UserProfileDTO::$WOMEN:
						$w = true;
						break;
				}
			}
		}
		
		$userProfileDTO->setInterestedIn($m, $w);
				
        //Get looking for
  		//Prepare a query for execution
		$result = pg_prepare($db_handle, "getLookingFor", 'SELECT LookingForOption FROM LookingFor WHERE Profile_ID=$1');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getLookingFor", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_all($result);
		
		$f = false;
		$d = false;
		$re = false;
		$ra = false;
		$w = false;

		if ($testarray) {		
			foreach ($testarray as $value) {
				switch ($value['lookingforoption']){
					case UserProfileDTO::$FRIENDSHIP:
						$f= true;
						break;
					case UserProfileDTO::$DATING:
						$d = true;
						break;
					case UserProfileDTO::$RELATIONSHIP:
						$re=true;
						break;
					case UserProfileDTO::$RANDOMPLAY:
						$ra=true;
						break;
					case UserProfileDTO::$WHATEVER:
						$w=true;
						break;
				}
			}
		}
		$userProfileDTO->setLookingFor($f, $d, $re, $ra, $w);
		
        //Get Email
  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "getEmail", 'SELECT getEmailAddress($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getEmail", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_assoc($result);
		
		$userProfileDTO->setEmail($testarray['getemailaddress']);            
    	
        //Get Birthdate          
         $result = pg_prepare($db_handle, "getBday", 'SELECT getBirthDate($1);');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getBday", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_assoc($result);
		
		//Not sure if this needs to be converted to different date format.
		$userProfileDTO->setBirthDate($testarray['getbirthdate']);            
        
        //Get Sex
        //*big shit eating grin*
         $result = pg_prepare($db_handle, "getSex", 'SELECT getSex($1);');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getSex", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_assoc($result);
		
		$userProfileDTO->setSex($testarray['getsex']);            
        
        //Get Hometown
         $result = pg_prepare($db_handle, "getHometown", 'SELECT Countries.CountryName, HomeTowns.CountryISOCode, US_StateProvinces.US_StateProvinceName, US_StateProvinces.US_StateProvince_ID, US_HomeTowns.US_CityTown
				FROM HomeTowns 
					INNER JOIN Countries ON HomeTowns.CountryISOCode=Countries.Alpha_2ISOCode 
					INNER JOIN US_HomeTowns ON HomeTowns.Profile_ID=US_HomeTowns.Profile_ID 
					INNER JOIN US_StateProvinces ON US_HomeTowns.US_StateProvince_ID=US_StateProvinces.US_StateProvince_ID
				WHERE HomeTowns.Profile_ID=$1;');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getHometown", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_assoc($result);
		
        // FIXME should return the entire array, not just us_citytown
		$userProfileDTO->setHomeTown($testarray['us_citytown']);        
        
        //Get Residence
         $result = pg_prepare($db_handle, "getResidence", 'SELECT Countries.CountryName, Countries.Alpha_2ISOCode, US_Addresses.US_AddressLine1, US_Addresses.US_AddressLine2, US_Addresses.US_CityTown, US_StateProvinces.US_StateProvinceName, US_Addresses.US_StateProvince_ID, US_PostalCode1, US_PostalCode2
				FROM US_Addresses INNER JOIN UserAddresses ON US_Addresses.US_Address_ID=UserAddresses.Address_ID
					INNER JOIN Addresses ON Addresses.Address_ID=US_Addresses.US_Address_ID 
					INNER JOIN Countries ON Countries.Alpha_2ISOCode=Addresses.CountryISO
					INNER JOIN US_StateProvinces ON US_StateProvinces.US_StateProvince_ID=US_Addresses.US_StateProvince_ID
				WHERE User_ID=(SELECT ProfileCreator FROM Profiles WHERE Profile_ID=$1) AND UserAddresses.LegalAddress=TRUE;');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "getResidence", array($profileID));
		// Need this for making sure it all works.
		$testarray =  pg_fetch_assoc($result);
		
		$userProfileDTO->setResidence($testarray);        
        
        pg_close( $db_handle );
        
        return $userProfileDTO;
    }

    // TODO find a better location for these two methods
    public function getProfileName( $profileID ) {
        $retVal = null;
        //get handle
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

        //escape input variables
        $profileID = pg_escape_string($profileID);

        $query = 
            "SELECT p.profilename from profiles p where " . "p.profile_id='$profileID'";

        $result = pg_query( $db_handle, $query);
        $resultSet = pg_fetch_array( $result, 0, PGSQL_ASSOC );
        $retVal = $resultSet['profilename'];
        
        pg_close( $db_handle );
        
        return $retVal;        
    }
    
    public function setProfileName( $profileID ) {
        
    }

    public function getProfileRealName( $profileID ){
        $retVal = array();
        //get handle
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

        //escape input variables
        $profileID = pg_escape_string( $profileID );

        // FIX this is a db issue, but we need to be forcing foreign key
        // constraints.  This select might not be pulling from the correct
        // tables in terms of the final design, or the intended design, but
        // it appears that profilecreator is the external reference for a
        // user_id, but there is no foreign key constraint
        $query = 
            "SELECT i.firstname, i.lastname from users i, profiles p where " . 
            "p.profile_id='$profileID' AND p.profilecreator=i.user_id";

        $result = pg_query( $db_handle, $query);
        $resultSet = pg_fetch_array( $result, 0, PGSQL_ASSOC );
        $retVal['firstname'] = $resultSet['firstname'];
        $retVal['lastname'] = $resultSet['lastname'];
        
        pg_close( $db_handle );
        
        return $retVal;
    }   

    public function setProfileRealName( $firstName, $middleName, $lastName ) {
        
    }

}

