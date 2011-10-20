<?php
/**
 * PGSQLImageDAO Class File
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
 * PGSQLImageDAO
 *
 * Needs to be commented
 * 
 * @category DataProcessing
 * @package Persistence
 * @subpackage DataAccessObjects
 */
class PGSQLImageDAO extends ImageDAO {

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
    public function getImageElement( $requesterProfileID, $imageDTO ) {
    	//Since permissions are not yet implemented $requseterProfileID is not used in the query.
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_ElementType, output_ElementPackagePath, output_Creator_ID, output_DateCreated, output_ImageFileHash, output_MIMEType, output_Title, output_Summery, output_File, output_FileSize, output_DateUploaded, output_Uploader, output_FileName, output_ImageDimensionX, output_ImageDimensionY FROM getImageElementInstance($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($imageDTO->getElementInstanceID()));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		$imageDTO->setElementType($testarray['output_elementtype']);
		$imageDTO->setElementCreatorProfileID($testarray['output_creator_id']);
		$imageDTO->setImageFileHash($testarray['output_imagefilehash']);
		$imageDTO->setImageMIMEType($testarray['output_mimetype']);
		$imageDTO->setImageElementTitle($testarray['output_title']);
		$imageDTO->setImageElementSummary($testarray['output_summery']);
		$imageDTO->setImageContent($testarray['output_file']);
		$imageDTO->setImageFileSize($testarray['output_filesize']);
		$imageDTO->setImageDateUploaded($testarray['output_dateuploaded']);
		$imageDTO->setImageUploader($testarray['output_uploader']);
		$imageDTO->setImageFileName($testarray['output_filename']);
		$imageDTO->setImageDimensionX($testarray['output_imagedimensionx']);
		$imageDTO->setImageDimensionY($testarray['output_imagedimensiony']);
		
		return $imageDTO;
    }

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
    public function getImageElementList( $requesterProfileID, $requestedProfileID ) {
    	//Once again permissions have not be implemented so $requesterProfileID is not used in the below query.
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT ElementTypes.ElementType, ElementTypes.ElementPackagePath, Elements.Creator_ID, Elements.DateCreated, Image_Elements.ImageFileHash, Image_Elements.MIMEType, Image_Elements.Title, Image_Elements.Summery, Files.File,	Files.FileSize,	Files.DateUploaded,	Files.Uploader, ImageFiles.ImageDimensionX, ImageFiles.ImageDimensionY
		FROM ElementTypes INNER JOIN Elements ON ElementTypes.ElementType_ID=Elements.ElementType_ID 
			INNER JOIN Image_Elements ON Elements.Element_ID=Image_Elements.Element_ID 
			INNER JOIN ImageFiles ON Image_Elements.ImageFileHash=ImageFiles.FileHash AND Image_Elements.MIMEType=ImageFiles.MIMEType 
			INNER JOIN Files ON ImageFiles.FileHash=Files.FileHash AND ImageFiles.MIMEType=Files.MIMEType
		WHERE Image_Elements.Creator_ID=$1');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($requestedProfileID));
		
		$testarray =  pg_fetch_all($result);
		pg_close( $db_handle );
		
		if ($testarray) {
			foreach ($testarray as $row) {
				
				$imageDTO = new ImageDTO();
				$imageDTO->setElementType($row['output_elementtype']);
				$imageDTO->setElementCreatorProfileID($row['output_creator_id']);
				$imageDTO->setImageFileHash($row['output_imagefilehash']);
				$imageDTO->setImageMIMEType($row['output_mimetype']);
				$imageDTO->setImageElementTitle($row['output_title']);
				$imageDTO->setImageElementSummary($row['output_summery']);
				$imageDTO->setImageContent($row['output_file']);
				$imageDTO->setImageFileSize($row['output_filesize']);
				$imageDTO->setImageDateUploaded($row['output_dateuploaded']);
				$imageDTO->setImageUploader($row['output_uploader']);
				$imageDTO->setImageFileName($row['output_filename']);
				$imageDTO->setImageDimensionX($row['output_imagedimensionx']);
				$imageDTO->setImageDimensionY($row['output_imagedimensiony']);
				
				return $imageDTO;
			}
		}	
    }

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
    public function setImageElement( $userID, $requesterProfileID, $requestedProfileID, $imageDTO ) {
    	//Make sure the $userID supplied has access to the file.
    	//Make sure the $profileID  used is allowed to set something.
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_Successful, output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath FROM setImageElement($1, $2, $3, $4, $5, $6)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID, $requesterProfileID, $imageDTO->getImageFileHash(), $imageDTO->getImageMIMEType(), $imageDTO->getImageElementTitle(), $imageDTO->getImageElementSummary()));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		if ($testarray) {
			$imageDTO->setElementInstanceID($testarray['output_element_id']);
			$imageDTO->setElementType($testarray['output_type']);
    		$imageDTO->setElementTypeID($testarray['output_type_id']);
    		$imageDTO->setElementCreatorProfileID($requesterProfileID);
		}
    }

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
    public function removeImageElement( $profileID, $imageDTO ) {
    	//Makes sure the $profileID supplied owns the element then removes the element.
    	$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT removeElement($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($profileID, $imageDTO->getElementInstanceID()));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
    }
    
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
    public function getProfileImageElement( $requesterProfileID, $requestedProfileID ) {
    	//Since permissions are not yet implemented $requseterProfileID is not used in the query.
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath, output_Creator_ID, output_DateCreated, output_ImageFileHash, output_MIMEType, output_File, output_FileSize, output_DateUploaded, output_Uploader, output_FileName, output_ImageDimensionX, output_ImageDimensionY FROM getProfileImageElement($1)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($requestedProfileID));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		$imageDTO = new ImageDTO();
		
		$imageDTO->setElementInstanceID($testarray['output_element_id']);
		$imageDTO->setElementID($testarray['output_elementtype_id']);
		$imageDTO->setElementType($testarray['output_elementtype']);
		$imageDTO->setElementCreatorProfileID($testarray['output_creator_id']);
		$imageDTO->setImageFileHash($testarray['output_imagefilehash']);
		$imageDTO->setImageMIMEType($testarray['output_mimetype']);
		$imageDTO->setImageContent(base64_decode($testarray['output_file']));
		$imageDTO->setImageFileSize($testarray['output_filesize']);
		$imageDTO->setImageDateUploaded($testarray['output_dateuploaded']);
		$imageDTO->setImageUploader($testarray['output_uploader']);
		$imageDTO->setImageFileName($testarray['output_filename']);
		$imageDTO->setImageDimensionX($testarray['output_imagedimensionx']);
		$imageDTO->setImageDimensionY($testarray['output_imagedimensiony']);
		
		return $imageDTO;    	
    }

    /**
     * @return  
     */
    public function removeImage( $userID, $imageDTO ) {
    	//ProfileID needed? needs discussion as to who can remove Images.
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT removeImage($1, $2, $3)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($imageDTO->getImageFileHash(), $imageDTO->getImageMIMEType(), $userID));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
    	
    }

    /**
     * @return  
     */
    public function removeProfileImageElement( $requesterProfileID, $requestedProfileID ) {
    	//Gets rid of an element so profile_ID is required
    	//Make sure that profile creator is the userID
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT removeProfileImage($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
//		$result = pg_execute($db_handle, "query", array($userID, $profileID));
	
        // FIX This is handing a profile ID as the user ID (need to change procedure)
        $result = pg_execute($db_handle, "query", array($requesterProfileID, $requestedProfileID));
    	
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
    		
    }

    /**
     * @return ProfileImageDTO 
     */
    public function setProfileImageElement( $userID, $requesterProfileID, $requestedProfileID, $imageDTO ) {
    	//Make sure user owns profile, then create new element 
    	//Make sure image was uploaded by UserID
    	//Is the userID check nessisary, will users be able to see the imageDTO if they do not own the image
		$db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_Successful, output_Element_ID, output_ElementType_ID, output_ElementType, output_ElementPackagePath FROM setProfileImage($1, $2, $3, $4)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID, $requestedProfileID, $imageDTO->getImageFileHash(), $imageDTO->getImageMIMEType()));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
    
    	if ($testarray['output_successful']) {
	    	$profileImageDTO = new ProfileImageDTO();
	    	$profileImageDTO->setImageContent($imageDTO->getImageContent());
	    	$profileImageDTO->setImageDateUploaded($imageDTO->getImageDateUploaded());
	    	$profileImageDTO->setImageDimensionX($imageDTO->getImageDimensionX());
	    	$profileImageDTO->setImageDimensionY($imageDTO->getImageDimensionY());
	    	$profileImageDTO->setImageFileHash($imageDTO->getImageFileHash());
	    	$profileImageDTO->setImageFileName($imageDTO->getImageFileName());
	    	$profileImageDTO->setImageFileSize($imageDTO->getImageFileSize());
	    	$profileImageDTO->setImageMIMEType($imageDTO->getImageMIMEType());
	    	$profileImageDTO->setImageUploader($imageDTO->getImageUploader());
	    	$profileImageDTO->setElementInstanceID($testarray['output_element_id']); //element_id
	    	$profileImageDTO->setElementID($testarray['output_elementtype_id']); //elementtype_id
	    	$profileImageDTO->setElementType($testarray['output_elementtype']);
	    	$profileImageDTO->setElementInstanceCreatorProfileID($requestedProfileID);
    	}
    }

    /**
     * @return ImageDTO 
     */
    public function storeNewImage( $userID, $imageDTO ) {
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();

  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_Successful, output_DateUploaded FROM createNewImageFile($1, $2, $3, $4, $5, $6, $7, $8)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($imageDTO->getImageFileHash(), base64_encode($imageDTO->getImageContent()), $imageDTO->getImageMIMEType(), $imageDTO->getImageFileSize(), $userID, $imageDTO->getImageFileName(), $imageDTO->getImageDimensionX(), $imageDTO->getImageDimensionY()));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		$imageDTO->setImageDateUploaded($testarray['output_dateuploaded']);
		
		return $imageDTO;        
    }

    /**
     * @return ImageDTO 
     */
    public function getImage( $userID, $imageDTO ) {
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
  		//Prepare a query for execution
  		$result = pg_prepare($db_handle, "query", 'SELECT output_File, output_FileSize, output_DateUploaded, output_Uploader, output_FileName, output_ImageDimensionX, output_ImageDimensionY FROM getImageFile($1, $2)');

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($imageDTO->getImageFileHash(), $imageDTO->getImageMIMEType()));
		
		$testarray =  pg_fetch_assoc($result);
		pg_close( $db_handle );
		
		$imageDTO->setImageContent(base64_decode(($testarray['output_file'])));
        $imageDTO->setImageFileSize($testarray['output_filesize']);
		$imageDTO->setImageDateUploaded($testarray['output_dateuploaded']);
		$imageDTO->setImageUploader($testarray['output_uploader']);
		$imageDTO->setImageFileName($testarray['output_filename']);
		$imageDTO->setImageDimensionX($testarray['output_imagedimensionx']);
		$imageDTO->setImageDimensionY($testarray['output_imagedimensiony']);
        
        return $imageDTO;
    }

    /**
     * @return ImageDTO 
     */
    public function getUserImageList( $userID, $profileID ) {
        //no content on these dto
        $retVal=array();
        $db_handle = DBConnectionManager::getConnection()->getRawConnectionResource();
  		//Prepare a query for execution
  		$result = pg_prepare( $db_handle, "query", 'SELECT Files.FileHash, Files.MIMEType, Files.FileSize, Files.DateUploaded, Files.Uploader, Files.FileName, ImageFiles.ImageDimensionX, ImageFiles.ImageDimensionY
													FROM Files 
														INNER JOIN ImageFiles ON Files.FileHash=ImageFiles.FileHash AND Files.MIMEType=ImageFiles.MIMEType INNER JOIN FileOwners ON FileOwners.FileHash=Files.FileHash AND FileOwners.MIMEType=Files.MIMEType
													WHERE FileOwners.User_ID=$1' );

		// Execute the prepared query.  Note that it is not necessary to escape
		$result = pg_execute($db_handle, "query", array($userID));
		
		$testarray =  pg_fetch_all($result);
		pg_close( $db_handle );
        
        foreach ($testarray as $row) {
        	$imageDTO = new ImageDTO();
            $imageDTO->setImageFileHash( $row['filehash'] );
            $imageDTO->setImageMIMEType($row['mimetype']);
        	$imageDTO->setImageFileSize($row['filesize']);
			$imageDTO->setImageDateUploaded($row['dateuploaded']);
			$imageDTO->setImageUploader($row['uploader']);
			$imageDTO->setImageFileName($row['filename']);
			$imageDTO->setImageDimensionX($row['imagedimensionx']);
			$imageDTO->setImageDimensionY($row['imagedimensiony']);
			$retVal[] = $imageDTO;
        }
        
        return $retVal;
    }

} 


