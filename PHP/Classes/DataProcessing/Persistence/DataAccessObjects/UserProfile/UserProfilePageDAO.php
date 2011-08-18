<?php
/**
 * UserProfilePageDAO Abstract Class File
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
 * @subpackage DataAccessObjects
 * @version 1.0
 */

/**
 * UserProfilePageDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
abstract class UserProfilePageDAO extends AbstractDAO {
    
    /**
     * update profile information
     */
    abstract public function setProfilePageLayout($profileID, $pageLayout);

    /**
     * This function retrieves a created Profile
     * 
     * @param profileID
     * @return UserProfilePageDTO
     */
    abstract public function getProfile( $profileID );

    // TODO find a better location for these two methods
    abstract public function getProfileName( $profileID );
    
    abstract public function setProfileName( $profileID );
	
	abstract public function getProfileCubes( $profileID );
	
	abstract public function deleteAllProfileCubes( $profileID );
	
	abstract public function addCubeToPage( $profileID, $cubeID, $column, $row );
	
}


