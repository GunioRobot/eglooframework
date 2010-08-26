<?php
/**
 * SetProfileImageRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * SetProfileImageRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class SetProfileImageRequestProcessor extends RequestProcessor {
    
    public function processRequest() {
   
     //forward the request to get internal main 
     header( 'Location: /profileID=' . $_SESSION['MAIN_PROFILE_ID'] );
	 
     if ( isset($_FILES['profileImage']) && $_FILES['profileImage']['name'] !== "" ) {
        	$imageName = $_FILES['profileImage']['name'];
        	$file = $_FILES['profileImage'];
        	      	
            $tempFileName = $_FILES['profileImage']['tmp_name'];
            
            $info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
            $mimeType = $info->file( $tempFileName );

            $imageHash = hash_file( 'sha256', $tempFileName );
            $imageName = $_FILES['profileImage']['name'];
            $fileSize = $_FILES['profileImage']['size'];

            $userID = $_SESSION['USER_ID'];
            $profileID = $_SESSION['MAIN_PROFILE_ID'];

            $magickWand = NewMagickWand();
            MagickReadImage( $magickWand, $tempFileName );
            
            $imageWidth = MagickGetImageWidth( $magickWand );
            $imageHeight = MagickGetImageHeight( $magickWand ); 

            //Go through uploading the image, first.
            $daoFunction = 'createNewImageFile';
            
            $inputValues = array();
 	    	$inputValues[ 'filehash' ] = $imageHash;
 	    	$inputValues[ 'file' ] = base64_encode(file_get_contents( $tempFileName ));
 	    	$inputValues[ 'mimetype' ] = $mimeType;
 	    	$inputValues[ 'filesize' ] = $fileSize;
 	    	$inputValues[ 'profileID' ] = $profileID;
 	    	$inputValues[ 'filename' ] = $imageName;
 	    	$inputValues[ 'imagedimensionx' ] = $imageWidth;
 	    	$inputValues[ 'imagedimensiony' ] = $imageHeight;
 	    	 	    	
 	    	$daoFactory = DAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			
			//Set the user's profile image			
            $daoFunction = 'setProfileImage';
			$inputValues = array();
	    	$inputValues[ 'userID' ] = $userID;
	    	$inputValues[ 'profileID' ] = $profileID;
	    	$inputValues[ 'imagefilehash' ] = $imageHash;
	    	$inputValues[ 'mimetype' ] = $mimeType;
    		
 	    	$daoFactory = DAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			$success = $gqDTO->get_output_successful();
			if( $success ){
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "SUCCESSFUL call to $daoFunction");
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "FAILURE call to $daoFunction");
			}
        } else {
        	eGlooLogger::writeLog( eGlooLogger::DEBUG, "ASDFASDF: 2. Something wrong " );
        }
    }
}
 
?>