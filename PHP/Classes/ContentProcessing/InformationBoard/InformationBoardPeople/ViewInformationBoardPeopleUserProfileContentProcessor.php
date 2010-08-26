<?php
/**
 * ViewInformationBoardPeopleUserProfileContentProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2010 eGloo, LLC
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
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * ViewInformationBoardPeopleUserProfileContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class ViewInformationBoardPeopleUserProfileContentProcessor extends ContentProcessor {

    private $_buildContainer = false;

    // Do the viewer profile and viewed profile have a relationship
    private $_hasRelationship = false;
    private $_relationshipType = null;

    private $_userProfileDTO = null;
    private $_userProfileID = null;
    private $_userProfileImageDTO = null;
    private $_userProfileName = null;
    private $_userProfileRealName = null;
    
    public function __construct() {}

    public function prepareContent() {
        $this->_templateEngine->assign( 'buildContainer', $this->_buildContainer );
        
        $this->_templateEngine->assign( 'hasRelationship', $this->_hasRelationship );
        
        $this->_templateEngine->assign( 'userProfileID', $this->_userProfileID );
        $this->_templateEngine->assign( 'userProfileImageDTO', $this->_userProfileImageDTO );
        $this->_templateEngine->assign( 'userProfileName', $this->_userProfileName );
        $this->_templateEngine->assign( 'userProfileRealName', $this->_userProfileRealName );
        
        $userBirthDate = date( 'F j, Y', strtotime( $this->_userProfileDTO->getBirthDate() ) );
        $this->_templateEngine->assign( 'userBirthDate', $userBirthDate );
                
        $this->_templateEngine->assign( 'userProfileDTO', $this->_userProfileDTO );
    }

    public function buildStandAlone() {
        return $this->_templateEngine->fetch( $this->_templateDefault );
    }
    
    public function buildContainer() {
        return $this->_buildContainer;
    }

    public function setBuildContainer( $buildContainer = true ) {
        $this->_buildContainer = $buildContainer;
    }

    public function hasRelationship() {
        return $this->_hasRelationship;
    }

    public function setHasRelationship( $hasRelationship = true ) {
        $this->_hasRelationship = $hasRelationship;
    }

    public function getProfileImageDTO() {
        return $this->_userProfileImageDTO;
    }

    public function setProfileImageDTO( $userProfileImageDTO ) {
        $this->_userProfileImageDTO = $userProfileImageDTO;
    }

    public function getUserProfileDTO() {
        return $this->_userProfileDTO;
    }
        
    public function setUserProfileDTO( $userProfileDTO ) {
        $this->_userProfileDTO = $userProfileDTO;
    }

    public function getUserProfileID() {
        return $this->_userProfileID;
    }
    
    public function setUserProfileID( $userProfileID ) {
        $this->_userProfileID = $userProfileID;
    }

    public function getUserProfileName() {
        return $this->_userProfileName;
    }
    
    public function setUserProfileName( $userProfileName ) {
        $this->_userProfileName = $userProfileName;
    }
    
    public function getUserProfileRealName() {
        return $this->_userProfileRealName;
    }

    public function setUserProfileRealName( $userProfileRealName ) {
        $this->_userProfileRealName = $userProfileRealName;
    }

}

?>
