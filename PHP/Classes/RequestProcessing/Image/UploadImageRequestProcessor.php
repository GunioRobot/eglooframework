<?php
/**
 * UploadImageRequestProcessor Class File
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
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * UploadImageRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class UploadImageRequestProcessor extends RequestProcessor {
    
    public function processRequest() {
    	
        if ( isset($_FILES['userfile']) && $_FILES['userfile']['name'] !== "" ) {
        	
        	
        	$imageName = $_FILES['userfile']['name'];
        	$file = $_FILES['userfile'];
        	      	
            $tempFileName = $_FILES['userfile']['tmp_name'];
            
            $info = new finfo( FILEINFO_MIME, '/usr/share/file/magic' );
            $mimeType = $info->file( $tempFileName );


            $imageHash = hash_file( 'sha256', $tempFileName );
            $imageName = $_FILES['userfile']['name'];
            $fileSize = $_FILES['userfile']['size'];

            $userID = $_SESSION['USER_ID'];
            $profileID = $_SESSION['MAIN_PROFILE_ID'];

            $magickWand = NewMagickWand();
            MagickReadImage( $magickWand, $tempFileName );
            
            $imageWidth = MagickGetImageWidth( $magickWand );
            $imageHeight = MagickGetImageHeight( $magickWand ); 

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
 	    	 	    	
 	    	$daoFactory = AbstractDAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			$success = $gqDTO->get_output_successful();
			if( $success ){
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "SUCCESSFUL call to $daoFunction");
			} else {
				eGlooLogger::writeLog( eGlooLogger::DEBUG, "FAILURE call to $daoFunction");
			}
        } else {
        	eGlooLogger::writeLog( eGlooLogger::DEBUG, "$_FILES" );
        }
        
        
        // else {
        //    print_r($_FILES);
        //    eGlooLogger::writeLog( eGlooLogger::DEBUG, 'UploadImageRequestProcessor: File not found' );
       // }




        //forward the request to view FridgePicsInfoBoardRequestProcessor
        $fridgePicsInfoBoardRequestProcessor= new FridgePicsInfoBoardRequestProcessor();
        
        $reqBean = new RequestInfoBean();
        $reqBean->setRequestClass( 'infoBoard' );
        $reqBean->setRequestID( 'FridgePics' );
        
        $fridgePicsInfoBoardRequestProcessor->setRequestInfoBean( $reqBean);
       	$fridgePicsInfoBoardRequestProcessor->processRequest();
       	


//        header("Content-type: text/html; charset=UTF-8");
    }

}

?>
