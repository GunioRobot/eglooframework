<?php
/**
 * CreateImageElementRequestProcessor Class File
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
 * CreateImageElementRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class CreateImageElementRequestProcessor extends RequestProcessor {

    public function processRequest() {
        $userID = $_SESSION['USER_ID'];
        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $imageID = $this->requestInfoBean->getPOST( 'imageID' );
        $imageMIMEType = str_replace( '_', '/', $this->requestInfoBean->getPOST( 'imageMIMEType' ) );
        
        $imageElementTitle = $this->requestInfoBean->getPOST( 'imageElementTitle' );
        $imageElementSummary = $this->requestInfoBean->getPOST( 'imageElementSummary' );

        $imageDTO = new ImageDTO();

        $imageDTO->setImageFileHash( $imageID );
        $imageDTO->setImageMIMEType( $imageMIMEType );
        $imageDTO->setImageElementTitle( $imageElementTitle );
        $imageDTO->setImageElementSummary( $imageElementSummary );

        $daoFactory = DAOFactory::getInstance();
        $imageDAO = $daoFactory->getImageDAO();
        $imageDAO->setImageElement( $userID, $profileID, $profileID, $imageDTO );
    }
}

?>
