<?php
/**
 * ImageDAO Abstract Class File
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
 * ImageDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
abstract class ImageDAO extends AbstractDAO {

	// TODO pre/post conditions, exception notes, proper returns and security checks 

    /*
     * Image Methods
     */

    /**
     * @return ImageDTO 
     */
    abstract public function getImage( $userID, $imageDTO );

    /**
     * @return ImageDTO 
     */
    abstract public function getUserImageList( $userID, $profileID );
    
    /**
     * Takes a fully populated ImageDTO containing a file primary key (Files.
     * FileHash and Files. MIMEType) and all information necessary and relevant
     * to storing an image file, and stores that content in the
     * database.  The new image file instance is linked against the supplied
     * user ID.  This operation is only performed if the user ID requesting this
     * operation has sufficient privileges to do so.
     * 
     * @param $userID  the ID of the user account requesting this operation
     * @param $imageDTO  an ImageDTO referencing the supplied image 
     */
    abstract public function storeNewImage( $userID, $imageDTO );

    /**
     * @return ImageDTO 
     */
    abstract public function removeImage( $userID, $imageDTO );

    /*
     * Image Element Methods
     */
     
    /**
     * Takes an ImageDTO containing an image element instance ID and returns an
     * ImageDTO populated with the information and content of the image element
     * instance ID provided, if the profile ID requesting this operation has
     * sufficient privileges to do so.
     * 
     * @param $requesterProfileID  the ID of the profile requesting this operation
     * @param $imageDTO  an ImageDTO referencing the image element instance
     * 
     * @return ImageDTO
     */
    abstract public function getImageElement( $requesterProfileID, $imageDTO );

    /**
     * Returns an array of ImageDTO objects populated with the information for
     * all image elements owned by the requested profile ID if the profile ID
     * requesting this operation has sufficient privileges to do so.
     *  
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile image
     * is being requested
     * 
     * @return ImageDTO Array
     */
    abstract public function getImageElementList( $requesterProfileID, $requestedProfileID );

    /**
     * Takes an ImageDTO containing a file primary key (Files.FileHash and
     * Files.MIMEType) and generates an image element instance associated with
     * image file primary key and linked to the requested profile ID
     * (new image element instance owner) if the user ID or the profile ID
     * requesting this operation has sufficient privileges to do so.
     * 
     * @param $userID  the ID of the user account requesting this operation
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile image
     * will be set
     * @param $imageDTO  an ImageDTO referencing the supplied image 
     */
    abstract public function setImageElement( $userID, $requesterProfileID, $requestedProfileID, $imageDTO );

    /**
     * Takes an ImageDTO containing an image element instance ID and removes the
     * image element instance if the profile ID requesting this operation has
     * sufficient privileges to do so.
     * 
     * @param $profileID  the ID of the profile requesting this operation
     * @param $imageDTO  an ImageDTO referencing the image element instance 
     *
     * @return ImageDTO
     */
    abstract public function removeImageElement( $profileID, $imageDTO );
    
    /**
     * Returns an ImageDTO populated with the information and content of the
     * profile image associated with the supplied profile ID if the profile ID
     * requesting this operation has sufficient privileges to do so.
     * 
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile image
     * is being requested
     * 
     * @return ImageDTO
     */
    abstract public function getProfileImageElement( $requesterProfileID, $requestedProfileID );

    /**
     * Takes an ImageDTO containing a file primary key (Files.FileHash and
     * Files.MIMEType) and generates a ProfileImage element instance associated
     * with image file primary key.  The ProfileImage element instance is then
     * set as the ProfileImage element for the supplied profile ID if the
     * user ID or the profile ID requesting this operation has sufficient
     * privileges to do so.
     * 
     * @param $userID  the ID of the user account requesting this operation
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile image
     * will be set
     * @param $imageDTO  an ImageDTO referencing the supplied image 
     */
    abstract public function setProfileImageElement( $userID, $requesterProfileID, $requestedProfileID, $imageDTO );

    /**
     * Removes the ProfileImage element instance associated with the requested
     * profile ID if the profile ID requesting this operation has sufficient
     * privileges to do so.
     * 
     * @param 
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile image
     * is being removed
     * 
     * @return ImageDTO 
     */
    abstract public function removeProfileImageElement( $requesterProfileID, $requestedProfileID );

}

