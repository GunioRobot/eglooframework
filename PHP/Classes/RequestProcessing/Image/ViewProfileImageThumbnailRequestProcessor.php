<?php
/**
 * ViewProfileImageThumbnailRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * ViewProfileImageThumbnailRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewProfileImageThumbnailRequestProcessor extends RequestProcessor {

    public function processRequest() {

        $userID = $_SESSION['USER_ID'];
        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $requesterProfileID = $_SESSION['MAIN_PROFILE_ID'];

        $requestedProfileID = $this->requestInfoBean->getGET( 'profileID' );
        $requestedProfileImageHash = $this->requestInfoBean->getGET( 'profileImageHash' );
        
        $cacheID = 'ProfileImageThumb|' . $requestedProfileID . '|' . $requestedProfileImageHash;

        $cacheGateway = CacheGateway::getCacheGateway();
        $profileImageThumbnailDTO = $cacheGateway->getObject( $cacheID, 'Content' );
        
        // FIX We probably shouldn't be caching these using the requested hash if it doesn't match
        // the real hash...
        
        if ( $profileImageThumbnailDTO == null || $profileImageThumbnailDTO == false ) {
            $daoFactory = AbstractDAOFactory::getInstance();
            $imageDAO = $daoFactory->getImageDAO();
            $imageDTO = $imageDAO->getProfileImageElement( $requesterProfileID, $requestedProfileID );

            if ( $imageDTO->getImageFileHash() !== $requestedProfileImageHash ) {
            	// FIX Should we be throwing an error here?
            	// This means the user has changed their profile image, but the change wasn't reflected
            	// until this user requested this page
            	// For now we'll just reset the requested image hash to what's in the DB
            	$cacheID = 'ProfileImageThumb|' . $requestedProfileID . '|' . $imageDTO->getImageFileHash();
            }
            
            $magickWand = NewMagickWand();
			MagickReadImageBlob( $magickWand, $imageDTO->getImageContent() );

	        $imageWidth = MagickGetImageWidth( $magickWand );
	        $imageHeight = MagickGetImageHeight( $magickWand ); 
	
	        $imageHeightMax = 64;
	        $imageWidthMax = 64;
	        
	        $dimensionRatio = 1;
	
			if ( $imageWidth > $imageHeight ) {
				$newWidth = $imageWidthMax;
				$newHeight = ( $imageHeight / $imageWidth ) * $newWidth;
			} else if ( $imageHeight > $imageWidth ) {
				$newHeight = $imageHeightMax;
				$newWidth = ( $imageWidth / $imageHeight ) * $newHeight;
			} else {
				$newHeight = $imageHeightMax;
				$newWidth = $imageWidthMax;
			}
	        
	        MagickResizeImage( $magickWand, $newWidth, $newHeight, MW_LANCZOSFILTER, 0.65 );
	        
	        $profileImageThumbnailDTO = new ProfileImageDTO();
	        $profileImageThumbnailDTO->setImageMIMEType( $imageDTO->getImageMIMEType() );
	        $profileImageThumbnailDTO->setImageFileHash( $imageDTO->getImageFileHash() );
	        $profileImageThumbnailDTO->setImageContent( MagickGetImageBlob( $magickWand ) );

            $cacheGateway->storeObject( $cacheID, $profileImageThumbnailDTO, 'Content', 3600 );
            eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Profile Image Thumbnail Pulled from DB' );
        } else {
            eGlooLogger::writeLog( eGlooLogger::DEBUG, 'Profile Image Thumbnail Pulled from Cache' );
        }
        
        header( 'Content-type: ' . $profileImageThumbnailDTO->getImageMIMEType() );
        
        echo $profileImageThumbnailDTO->getImageContent();
    }
}

?>
