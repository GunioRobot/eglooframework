<?php
/**
 * GetUserProfilePageElementsRequestProcessor Class File
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
 * GetUserProfilePageElementsRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class GetUserProfilePageElementsRequestProcessor extends RequestProcessor {

    private static $_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InternalMainPage/InternalMainPageContainer.tpl';
    
    public function processRequest() {
//        $cacheGateway = CacheGateway::getCacheGateway();
//        
//        if ( ($output = $cacheGateway->getObject( 'adf', '<type>' ) ) == null ) {
//            $this->_templateEngine = new TemplateEngine( 'dev', 'us' );
//
//            $userProfileCenterContainerContent = new UserProfileCenterContainerContentProcessor();
//            $userProfileCenterContainerContent->setTemplateEngine( $this->_templateEngine );
//            $userProfileCenterContainerContent->prepareContent();
//
//            $output = $this->_templateEngine->fetch( self::$_template );
//
//            //$cacheGateway->storeObject( 'adf', $output, '<type>' );
//        }

        $profileID = $this->requestInfoBean->getGET( 'profileID' );
        
        $daoFactory = AbstractDAOFactory::getInstance();
        $userProfilePageDAO = $daoFactory->getUserProfilePageDAO();
        $userProfilePageDTO = $userProfilePageDAO->getProfile( $profileID );

        header("Content-type: text/html; charset=UTF-8");
        echo $userProfilePageDTO->getProfilePageLayout();
    }

}

?>