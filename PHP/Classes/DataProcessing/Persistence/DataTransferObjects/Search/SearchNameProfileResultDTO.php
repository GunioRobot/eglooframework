<?php
/**
 * SearchNameProfileResultDTO Class File
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
 * @author George Cooper
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 * @version 1.0
 */

/**
 * SearchNameProfileResultDTO
 * 
 * Needs to be commented
 *
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataTransferObjects
 */
class SearchNameProfileResultDTO {
 
    private $firstName = null;
    private $lastName = null;
    private $profileID = null;
	private $profileName = null;
	 
    public function getFirstName() {
        return $this->firstName;
    }

    public function setFirstName( $firstName ) {
        $this->firstName = $firstName;
    }

    public function getLastName() {
        return $this->lastName;
    }

    public function setLastName( $lastName ) {
        $this->lastName = $lastName;
    }

    public function getProfileID() {
        return $this->profileID;
    }

    public function setProfileID( $profileID ) {
        $this->profileID = $profileID;
    }
    
	public function getProfileName() {
        return $this->profileName;
    }

    public function setProfileName( $profileName ) {
        $this->profileName = $profileName;
    }
    
 }
