<?php
/**
 * UserProfileDTO Class File
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
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * UserProfileDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class UserProfileDTO {
    
    private $sex = null;
    private $interestedIn = array();
    private $birthDate = null;
    private $lookingFor = array();
    private $homeTown = array();
    private $residence = null;
    private $email = null;
    
    //interested in consts
    public static $MEN = "Men";
    public static $WOMEN = "Women";
    
    //looking for consts
    public static $FRIENDSHIP = "Friendship";
    public static $DATING = "Dating";
    public static $RELATIONSHIP = "A Relationship";
    public static $RANDOMPLAY = "Random Play";
    public static $WHATEVER = "Whatever I can get";
    
    public function getSex() {
        return $this->sex;
    }

    public function setSex( $sex ) {
        $this->sex = $sex;
    }

    public function getInterestedIn() {
        return $this->interestedIn;
    }
    
    public function setInterestedIn( $menBool, $womenBool ) {
        $this->interestedIn[ self::$MEN ] = $menBool;
        $this->interestedIn[ self::$WOMEN ] = $womenBool;
    }

    public function getInterestedInMen() {
        return $this->interestedIn[ self::$MEN ];
    }

    public function setInterestedInMen( $menBool ) {
        $this->interestedIn[ self::$MEN ] = $menBool;
    }

    public function getInterestedInWomen() {
        return $this->interestedIn[ self::$WOMEN ];
    }

    public function setInterestedInWomen( $womenBool ) {
        $this->interestedIn[ self::$WOMEN ] = $womenBool;
    }

    public function getBirthDate() {
        return $this->birthDate;
    }

    public function setBirthDate( $birthDate ) {
        return $this->birthDate = $birthDate;
    }


    public function getLookingFor() {
        return $this->lookingFor;
    }
    
    public function setLookingFor( $friendBool, $dateBool, $relationShipBool, $randomBool, $whateverBool ) {
        $this->lookingFor[ self::$FRIENDSHIP ] = $friendBool;
        $this->lookingFor[ self::$DATING ] = $dateBool;
        $this->lookingFor[ self::$RELATIONSHIP ] = $relationShipBool;
        $this->lookingFor[ self::$RANDOMPLAY ] = $randomBool;
        $this->lookingFor[ self::$WHATEVER ] = $whateverBool;
    }

    public function getLookingForFriendship() {
        return $this->lookingFor[ self::$FRIENDSHIP ];
    }

    public function setLookingForFriendship( $friendBool ) {
        $this->lookingFor[ self::$FRIENDSHIP ] = $friendBool;        
    }

    public function getLookingForRelationship() {
        return $this->lookingFor[ self::$RELATIONSHIP ];
    }

    public function setLookingForRelationship( $relationshipBool ) {
        $this->lookingFor[ self::$RELATIONSHIP ] = $relationshipBool;        
    }

    public function getLookingForDating() {
        return $this->lookingFor[ self::$DATING ];
    }

    public function setLookingForDating( $dateBool ) {
        $this->lookingFor[ self::$DATING ] = $dateBool;
    }

    public function getLookingForRandomPlay() {
        return $this->lookingFor[ self::$RANDOMPLAY ];
    }

    public function setLookingForRandomPlay( $randomBool ) {
        $this->lookingFor[ self::$RANDOMPLAY ] = $randomBool;
    }

    public function getLookingForWhateverICanGet() {
        return $this->lookingFor[ self::$WHATEVER ];
    }

    public function setLookingForWhateverICanGet( $whateverBool ) {
        $this->lookingFor[ self::$WHATEVER ] = $whateverBool;
    }

    public function getHomeTown() {
        // FIXME Should not return just citytown
        return $this->homeTown['us_citytown'];
        //Hometown is an array composed of these fields
        //[countryname] - display field
        //[countryisocode] - key field for above
        //[us_stateprovincename] - display field
        //[us_stateprovince_id] - key field for above
        //[us_citytown] - display field
        
    }

    public function setHomeTown( $homeTown ) {
        $this->homeTown['countryisocode'] = 'US'; 
        $this->homeTown['us_citytown'] = $homeTown;
        //Hometown is an array composed of these fields
        //[countryname] - display field
        //[countryisocode] - key field for above
        //[us_stateprovincename] - display field
        //[us_stateprovince_id] - key field for above
        //[us_citytown] - display field
    }

    public function getResidence() {
        return $this->residence;
        //[countryname] - display
		//[alpha_2isocode] - key for above
		//[us_addressline1] - display
		//[us_addressline2] - display
		//[us_citytown] - display
		//[us_stateprovincename] - display
		//[us_stateprovince_id] - key for above
		//[us_postalcode1] - display
		//[us_postalcode2] - display
    }

    public function setResidence( $residence ) {
        $this->residence['alpha_2isocode'] = 'US';
        $this->residence['us_citytown'] = $residence;
        //[countryname] - display
		//[alpha_2isocode] - key for above
		//[us_addressline1] - display
		//[us_addressline2] - display
		//[us_citytown] - display
		//[us_stateprovincename] - display
		//[us_stateprovince_id] - key for above
		//[us_postalcode1] - display
		//[us_postalcode2] - display
        
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail( $email ) {
        $this->email = $email;
    }

 }

