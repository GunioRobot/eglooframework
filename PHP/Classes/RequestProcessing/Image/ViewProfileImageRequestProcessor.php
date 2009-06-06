<?php
/**
 * ViewProfileImageRequestProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2008 eGloo, LLC
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
 * ViewProfileImageRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewProfileImageRequestProcessor extends RequestProcessor {

    public function processRequest() {

        $userID = $_SESSION['USER_ID'];
        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $requesterProfileID = $_SESSION['MAIN_PROFILE_ID'];

        $requestedProfileID = $this->requestInfoBean->getGET( 'profileID' );
        
        $cacheID = 'ProfileImage|' . $requestedProfileID;

        $cacheGateway = CacheGateway::getCacheGateway();
//        $imageDTO = $cacheGateway->getObject( $cacheID, '<type>' );
        
        if ( $imageDTO == null || $imageDTO == false ) {
            $daoFactory = DAOFactory::getInstance();
            $imageDAO = $daoFactory->getImageDAO();
            $imageDTO = $imageDAO->getProfileImageElement( $requesterProfileID, $requestedProfileID );
            
//            $cacheGateway->storeObject( $cacheID, $imageDTO, '<type>', 3600 );
            eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'Profile Image Pulled from DB' );
        } else {
            eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'Profile Image Pulled from Cache' );
        }
        
        header( 'Content-type: ' . $imageDTO->getImageMIMEType() );
        
        echo $imageDTO->getImageContent();
    }
}

?>
