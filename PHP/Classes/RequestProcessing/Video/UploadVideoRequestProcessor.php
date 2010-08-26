<?php
/**
 * UploadVideoRequestProcessor Class File
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * UploadVideoRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class UploadVideoRequestProcessor extends RequestProcessor {
    // TODO SANITY CHECKs -- Do not assume RequestProcessor wasn't drunk when it sent this to you
    private static $_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InternalMainPage/InternalMainPageContainer.tpl';
    
    public function processRequest() {

        if ( isset($_FILES['userfile']) ) {
            $userID = $_SESSION['USER_ID'];
            $profileID = $_SESSION['MAIN_PROFILE_ID'];

            $tempFileName = $_FILES['userfile']['tmp_name'];
            
            $info = new finfo( FILEINFO_MIME, '/opt/local/share/file/magic' );
            $mimeType = $info->file( $tempFileName );

            $videoHash = hash_file( 'sha256', $tempFileName );
            $videoName = $_FILES['userfile']['name'];
            $fileSize = $_FILES['userfile']['size'];

            //write the file to disk
			//TODO: Parse Name of file
            
            //TODO needs to be resorted so that the video conversion happens, then the file is stored
            $videoDTO = new VideoDTO();
            $videoDTO->setVideoFileName($videoName);
            $videoDTO->setVideoFileSize($fileSize);
            $videoDTO->setVideoContent(file_get_contents($tempFileName));
            
            $daoFactory = DAOFactory::getInstance();
            $videoDAO = $daoFactory->getVideoDAO();
            $videoDAO->storeNewVideo($userID, $videoDTO);
            
            //convert teh file
			$videoConvertCmd = escapeshellcmd("ffmpeg -i $videoName $videoName.flv");
			exec($videoConvertCmd);
                      
            //file_put_contents( '/tmp/blah.jpg', base64_decode(pg_unescape_bytea(pg_escape_bytea(base64_encode($imageDTO->getImageContent())))), FILE_APPEND );
        } else {
            print_r($_FILES);
            eGlooLogger::writeLog( eGlooLogger::DEBUG, 'UploadImageRequestProcessor: File not found' );
        }

//        header("Content-type: text/html; charset=UTF-8");
    }

}


?>
