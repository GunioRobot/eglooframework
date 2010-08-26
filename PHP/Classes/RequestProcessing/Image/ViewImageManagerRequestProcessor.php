<?php
/**
 * ViewImageManagerRequestProcessor Class File
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
 * ViewImageManagerRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewImageManagerRequestProcessor extends RequestProcessor {
    private $_templateDefault = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/Image/ImageManagerDialog.tpl';
    
    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );

        $profileID = $_SESSION['MAIN_PROFILE_ID'];

        $output = null;
    
        $profileID = $_SESSION['MAIN_PROFILE_ID'];
        $userID = $_SESSION['USER_ID'];        

        $daoFactory = DAOFactory::getInstance();
        $imageDAO = $daoFactory->getImageDAO();

        $imageDTOList = $imageDAO->getUserImageList($userID, $profileID);

        $existingUserImageListContainerContentProcessor = new ExistingUserImageListContainerContentProcessor();
        $existingUserImageListContainerContentProcessor->setTemplateEngine( $this->_templateEngine );
        $existingUserImageListContainerContentProcessor->setUserImageDTOList( $imageDTOList );
        $existingUserImageListContainerContentProcessor->prepareContent();

        $existingUserImageElementListContainerContentProcessor = new ExistingUserImageElementListContainerContentProcessor();
        $existingUserImageElementListContainerContentProcessor->setTemplateEngine( $this->_templateEngine );
        $existingUserImageElementListContainerContentProcessor->prepareContent();

        $userUpdateImageElementFormContentProcessor = new UserUpdateImageElementFormContentProcessor();
        $userUpdateImageElementFormContentProcessor->setTemplateEngine( $this->_templateEngine );
        $userUpdateImageElementFormContentProcessor->prepareContent();

        $userUploadNewImageFormContentProcessor = new UserUploadNewImageFormContentProcessor();
        $userUploadNewImageFormContentProcessor->setTemplateEngine( $this->_templateEngine );
        $userUploadNewImageFormContentProcessor->prepareContent();

        $output = $this->_templateEngine->fetch( $this->_templateDefault );

        header("Content-type: text/html; charset=UTF-8");
        
        echo $output;
    }

}
 
?>
