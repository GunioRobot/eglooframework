<?php
/**
 * UserProfilePageDTO Class File
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
 * UserProfilePageDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class UserProfilePageDTO extends DataTransferObject {

    private $_profileID = null;
    private $_profilePageLayout = null;
    
    public function getProfileID() {
        return $this->_profileID;
    }

    public function setProfileID( $profileID ) {
        $this->_profileID = $profileID;
    }

    public function getProfilePageLayout() {
        return $this->_profilePageLayout;
    }

    public function setProfilePageLayout( $profilePageLayout ) {
        $this->_profilePageLayout = $profilePageLayout;
    }

}

