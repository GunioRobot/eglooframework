<?php
/**
 * VideoDAO Abstract Class File
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
 * @author Tom Read
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package Persistence
 * @version 1.0
 */

/**
 * VideoDAO
 *
 * Needs to be commented
 * 
 * @package Persistence
 */
abstract class VideoDAO {
// TODO pre/post conditions, exception notes, proper returns and security checks 

    /*
     * Video Methods
     */

    /**
     * @return VideoDTO 
     */
    abstract public function getVideo( $userID, $videoDTO );

    /**
     * @return VideoDTO 
     */
    abstract public function getUserVideoList( $userID, $profileID );
    
    /**
     * Takes a fully populated VideoDTO containing a file primary key (Files.
     * FileHash and Files. MIMEType) and all information necessary and relevant
     * to storing an video file, and stores that content in the
     * database.  The new video file instance is linked against the supplied
     * user ID.  This operation is only performed if the user ID requesting this
     * operation has sufficient privileges to do so.
     * 
     * @param $userID  the ID of the user account requesting this operation
     * @param $videoDTO  an VideoDTO referencing the supplied video 
     */
    abstract public function storeNewVideo( $userID, $videoDTO );

    /**
     * @return VideoDTO 
     */
    abstract public function removeVideo( $userID, $videoDTO );

    /*
     * Video Element Methods
     */
     
    /**
     * Takes an VideoDTO containing an video element instance ID and returns an
     * VideoDTO populated with the information and content of the video element
     * instance ID provided, if the profile ID requesting this operation has
     * sufficient privileges to do so.
     * 
     * @param $requesterProfileID  the ID of the profile requesting this operation
     * @param $videoDTO  an VideoDTO referencing the video element instance
     * 
     * @return VideoDTO
     */
    abstract public function getVideoElement( $requesterProfileID, $videoDTO );

    /**
     * Returns an array of VideoDTO objects populated with the information for
     * all video elements owned by the requested profile ID if the profile ID
     * requesting this operation has sufficient privileges to do so.
     *  
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile video
     * is being requested
     * 
     * @return VideoDTO Array
     */
    abstract public function getVideoElementList( $requesterProfileID, $requestedProfileID );

    /**
     * Takes an VideoDTO containing a file primary key (Files.FileHash and
     * Files.MIMEType) and generates an video element instance associated with
     * video file primary key and linked to the requested profile ID
     * (new video element instance owner) if the user ID or the profile ID
     * requesting this operation has sufficient privileges to do so.
     * 
     * @param $userID  the ID of the user account requesting this operation
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile video
     * will be set
     * @param $videoDTO  an VideoDTO referencing the supplied video 
     */
    abstract public function setVideoElement( $userID, $requesterProfileID, $requestedProfileID, $videoDTO );

    /**
     * Takes an VideoDTO containing an video element instance ID and removes the
     * video element instance if the profile ID requesting this operation has
     * sufficient privileges to do so.
     * 
     * @param $profileID  the ID of the profile requesting this operation
     * @param $videoDTO  an VideoDTO referencing the video element instance 
     *
     * @return VideoDTO
     */
    abstract public function removeVideoElement( $profileID, $videoDTO );
    
    /**
     * Returns an VideoDTO populated with the information and content of the
     * profile video associated with the supplied profile ID if the profile ID
     * requesting this operation has sufficient privileges to do so.
     * 
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile video
     * is being requested
     * 
     * @return VideoDTO
     */
    abstract public function getProfileVideoElement( $requesterProfileID, $requestedProfileID );

    /**
     * Takes an VideoDTO containing a file primary key (Files.FileHash and
     * Files.MIMEType) and generates a ProfileVideo element instance associated
     * with video file primary key.  The ProfileVideo element instance is then
     * set as the ProfileVideo element for the supplied profile ID if the
     * user ID or the profile ID requesting this operation has sufficient
     * privileges to do so.
     * 
     * @param $userID  the ID of the user account requesting this operation
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile video
     * will be set
     * @param $videoDTO  an VideoDTO referencing the supplied video 
     */
    abstract public function setProfileVideoElement( $userID, $requesterProfileID, $requestedProfileID, $videoDTO );

    /**
     * Removes the ProfileVideo element instance associated with the requested
     * profile ID if the profile ID requesting this operation has sufficient
     * privileges to do so.
     * 
     * @param 
     * @param $requesterProfileID  the ID of the profile requesting this
     * operation
     * @param $requestedProfileID  the ID of the profile whose profile video
     * is being removed
     * 
     * @return VideoDTO 
     */
    abstract public function removeProfileVideoElement( $requesterProfileID, $requestedProfileID );

}
?>
