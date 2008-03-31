<?php
/**
 * SetUserProfileDataRequestProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2008 eGloo, LLC
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
 * @author George Cooper
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * SetUserProfileDataRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class SetUserProfileDataRequestProcessor extends RequestProcessor {

    private static $_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InternalMainPage/InternalMainPageContainer.tpl';
    
    public function processRequest() {        
        $profileID = $this->requestInfoBean->getGET( 'profileID' );

		//TODO:check profileID matches session profileid
		

        $daoFactory = DAOFactory::getInstance();
        $userProfileDAO = $daoFactory->getUserProfileDAO();
        $userProfileDTO = new UserProfileDTO();
        
        // Retrieve the required POST elements
        $userProfileDTO->setSex( $this->requestInfoBean->getPOST( 'gender' ) );

        $userBirthDay = $this->requestInfoBean->getPOST( 'userBirthDay' );
        $userBirthMonth = $this->requestInfoBean->getPOST( 'userBirthMonth' );
        $userBirthYear = $this->requestInfoBean->getPOST( 'userBirthYear' );

        // Convert the user's birthdate into ISO 8601 for PostgreSQL or whatever other DB backend that
        // needs a Date type standard
        $birthdate = date( 'c', mktime( 0, 0, 0, $userBirthMonth, $userBirthDay, $userBirthYear ) );
        $userProfileDTO->setBirthDate( $birthdate );

        $userProfileDTO->setHomeTown( $this->requestInfoBean->getPOST( 'hometown' ) );
        //$userProfileDTO->setResidence( $this->requestInfoBean->getPOST( 'residence' ) );

        // Retrieve the optional POST elements
        if ( $this->requestInfoBean->issetPOST( 'lookingForFriendship' ) ) {
            $userProfileDTO->setLookingForFriendship( true );
        } else {
            $userProfileDTO->setLookingForFriendship( false );
        }
        
        if ( $this->requestInfoBean->issetPOST( 'lookingForRelationship' ) ) {
            $userProfileDTO->setLookingForRelationship( true );
        } else {
            $userProfileDTO->setLookingForRelationship( false );
        } 
        
        if ( $this->requestInfoBean->issetPOST( 'lookingForRandomPlay' ) ) {
            $userProfileDTO->setLookingForRandomPlay( true );
        } else {
            $userProfileDTO->setLookingForRandomPlay( false );
        } 
        
        if ( $this->requestInfoBean->issetPOST( 'lookingForDating' ) ) {
            $userProfileDTO->setLookingForDating( true );
        } else {
            $userProfileDTO->setLookingForDating( false );
        } 
        
        if ( $this->requestInfoBean->issetPOST( 'lookingForWhateverICanGet' ) ) {
            $userProfileDTO->setLookingForWhateverICanGet( true );
        } else {
            $userProfileDTO->setLookingForWhateverICanGet( false );
        }
        
        if ( $this->requestInfoBean->issetPOST( 'interestedInMen' ) ) {
            $userProfileDTO->setInterestedInMen( true );
        } else {
            $userProfileDTO->setInterestedInMen( false );
        }
        
        if ( $this->requestInfoBean->issetPOST( 'interestedInWomen' ) ) {
            $userProfileDTO->setInterestedInWomen( true );
        } else {
            $userProfileDTO->setInterestedInWomen( false );
        }

        $userProfileDAO->setProfile( $userProfileDTO, $profileID );

        header("Content-type: text/html; charset=UTF-8");
        echo 'success';
    }

}

?>