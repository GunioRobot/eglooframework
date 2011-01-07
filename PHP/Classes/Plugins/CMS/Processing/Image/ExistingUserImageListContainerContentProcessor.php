<?php
/**
 * ExistingUserImageListContainerContentProcessor Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * ExistingUserImageListContainerContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class ExistingUserImageListContainerContentProcessor extends ContentProcessor {
    
    private $_templateDefault = '../Templates/Frameworks/Common/XHTML/Image/Lists/UserImageListContainer.tpl';
    
    private $_userImageDTOList = null;
    private $_loggedInUserProfile = false;
    private $_profileID = null;
    private $_username = null;
    
    public function __construct() {}
    
    public function prepareContent() {
        // simulate DB connect

        $this->_templateEngine->assign( 'imageManagerExistingUserImageListContainerContentUseTemplate', true );
        $this->_templateEngine->assign( 'imageManagerExistingUserImageListContainerContentTemplate', $this->_templateDefault );
        
        $this->_templateEngine->assign( 'userImageDTOList', $this->_userImageDTOList );
    }

    public function buildStandAlone() {
        return $this->_templateEngine->fetch( $this->_templateDefault );
    }

    public function getLoggedInUser() {
        return $this->_loggedInUserProfile;
    }
    
    public function setLoggedInUser( $_loggedInUserProfile ) {
        $this->_loggedInUserProfile = $_loggedInUserProfile;
    }
    
    public function getProfileID() {
        return $this->_profileID;
    }
        
    public function setProfileID( $profileID ) {
        $this->_profileID = $profileID;
    }

    public function getUsername() {
        return $this->_username;
    }
    
    public function setUsername( $username ) {
        $this->_username = $username;
    }
    
    public function getUserImageDTOList() {
        return $this->_userImageDTOList;
    }
    
    public function setUserImageDTOList( $userImageDTOList ) {
        $this->_userImageDTOList = $userImageDTOList;
    }
}

?>
