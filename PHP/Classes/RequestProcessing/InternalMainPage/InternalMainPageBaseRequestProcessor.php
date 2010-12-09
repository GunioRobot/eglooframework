<?php
/**
 * InternalMainPageBaseRequestProcessor Class File
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
 * InternalMainPageBaseRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class InternalMainPageBaseRequestProcessor extends RequestProcessor {
    
    public function processRequest() {
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();

        $templateDirector->setTemplateBuilder( $templateBuilder );
        
        
//        $userCacheID = "";
//        $userCacheID = $_SESSION['USER_ID'] . '|' . $_SESSION['MAIN_PROFILE_ID'];
        
		
		$mainProfileID = $_SESSION['MAIN_PROFILE_ID'];
		$viewingProfileID = $_SESSION['MAIN_PROFILE_ID'];
		$viewingUserProfileName = $_SESSION['USER_USERNAME'];
		$loggedInUser = true;
		
		if( $this->requestInfoBean->issetGET('profileID') ) {
			$viewingProfileID = $this->requestInfoBean->getGET( 'profileID' );
		} else {
			//TODO in the future, /home should be distinct from /profileID=foo
			$userCacheID = $_SESSION['USER_ID'] . '|' . $_SESSION['MAIN_PROFILE_ID'];
		}

		if( $viewingProfileID !==  $mainProfileID ) {
			$loggedInUser = false;
			
			$daoFunction = 'getProfileName';
			$inputValues = array();
 	    	$inputValues[ 'profileID' ] = $viewingProfileID;
 	    	 	    	
 	    	$daoFactory = AbstractDAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
            $viewingUserProfileName = $gqDTO->get_output_profile_name();
		}

        //$templateDirector->setCacheID( $userCacheID, 3600 );
        $templateDirector->preProcessTemplate();

        //if ( !$templateDirector->isCached() ) {
        
        
        

            $templateVariables['eas_MainProfileID'] = $mainProfileID;
            $templateVariables['eas_ViewingProfileID'] = $viewingProfileID;
            $templateDirector->setTemplateVariables( $templateVariables );            

            $globalMenuBarContent = new GlobalMenuBarContentProcessor();
            
            
    
            /**
             * DEPRECATED... NOT USING
             * $informationBoardContainerContent = new InformationBoardContainerContentProcessor();
             * $userControlCenterContainerContent = new UserControlCenterContainerContentProcessor();
             */
            
            $userProfileCenterContainerContent = new UserProfileCenterContainerContentProcessor();
            $userProfileCenterContainerContent->setBuildContainer( true );
            
            
            $userProfileCenterContainerContent->setLoggedInUser( $loggedInUser );
            $userProfileCenterContainerContent->setProfileID( $viewingProfileID );
            $userProfileCenterContainerContent->setUsername( $viewingUserProfileName );

            /**
             * DEPRECATED... NOT USING
             * $userCommCenterContainerContent = new UserCommCenterContainerContentProcessor();
             */
            
            $templateDirector->setContentProcessors( array( $globalMenuBarContent,
                                                          //  $informationBoardContainerContent,
                                                         //   $userControlCenterContainerContent,
                                                            $userProfileCenterContainerContent ) );
                                                          //  $userCommCenterContainerContent ) );
        //}

        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;

    }

}

?>