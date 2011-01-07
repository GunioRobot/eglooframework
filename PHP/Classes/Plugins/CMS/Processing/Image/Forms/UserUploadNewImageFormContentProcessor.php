<?php
/**
 * UserUploadNewImageFormContentProcessor Class File
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
 * UserUploadNewImageFormContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class UserUploadNewImageFormContentProcessor extends ContentProcessor {
    
    private $_templateDefault = '../Templates/Frameworks/Common/XHTML/Image/Forms/UserUploadNewImageForm.tpl';
    
    private $_loggedInUserProfile = false;
    private $_profileID = null;
    private $_username = null;
    
    public function __construct() {}
    
    public function prepareContent() {
        // simulate DB connect

        $this->_templateEngine->assign( 'imageManagerUserUploadNewImageFormContainerContentUseTemplate', true );
        $this->_templateEngine->assign( 'imageManagerUserUploadNewImageFormContainerContentTemplate', $this->_templateDefault );
//        $this->_templateEngine->assign( 'loggedInUserProfile', $this->_loggedInUserProfile );
//        $this->_templateEngine->assign( 'profileID', $this->_profileID );
//        $this->_templateEngine->assign( 'username', $this->_username );
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
    
}

?>
