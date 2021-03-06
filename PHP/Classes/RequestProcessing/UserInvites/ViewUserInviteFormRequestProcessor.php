<?php
/**
 * ViewUserInviteFormRequestProcessor Class File
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
 * ViewUserInviteFormRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewUserInviteFormRequestProcessor extends RequestProcessor {

    public function processRequest() {
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();

        $templateDirector->setTemplateBuilder( $templateBuilder );
                
        $userCacheID = $_SESSION['USER_ID'] . '|' . $_SESSION['MAIN_PROFILE_ID'];

        //$templateDirector->setCacheID( $userCacheID, 3600 );
        $templateDirector->preProcessTemplate();

        if ( !$templateDirector->isCached() ) {
            $templateVariables[] = array( 'tokenID' => 'eas_MainProfileID', 'tokenVar' => $_SESSION['MAIN_PROFILE_ID'] );        
            $templateDirector->setTemplateVariables( $templateVariables );            

            $globalMenuBarContent = new GlobalMenuBarContentProcessor();
            
            $informationBoardContainerContent = new InformationBoardContainerContentProcessor();
    
            $userControlCenterContainerContent = new UserControlCenterContainerContentProcessor();
            
            $userProfileCenterContainerContent = new UserProfileCenterContainerContentProcessor();
            $userProfileCenterContainerContent->setBuildContainer( true );
            $userProfileCenterContainerContent->setLoggedInUser( true );
            $userProfileCenterContainerContent->setProfileID( $_SESSION['MAIN_PROFILE_ID'] );
            $userProfileCenterContainerContent->setUsername( $_SESSION['USER_USERNAME'] );
    
            $userCommCenterContainerContent = new UserCommCenterContainerContentProcessor();
            
            $templateDirector->setContentProcessors( array( $globalMenuBarContent,
                                                            $informationBoardContainerContent,
                                                            $userControlCenterContainerContent,
                                                            $userProfileCenterContainerContent,
                                                            $userCommCenterContainerContent ) );
        }

        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
    }

}

?>
