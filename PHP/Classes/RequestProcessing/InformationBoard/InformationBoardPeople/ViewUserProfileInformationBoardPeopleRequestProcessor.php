<?php
/**
 * ViewUserProfileInformationBoardPeopleRequestProcessor Class File
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
 * ViewUserProfileInformationBoardPeopleRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewUserProfileInformationBoardPeopleRequestProcessor extends RequestProcessor {
    // TODO SANITY CHECKs -- Do not assume RequestProcessor wasn't drunk when it sent this to you

    public function processRequest() {

        $requesterProfileID = $_SESSION['MAIN_PROFILE_ID'];
        $requestedProfileID = $this->requestInfoBean->getGET( 'profileID' );

        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();

        $templateDirector->setTemplateBuilder( $templateBuilder );
                
        $userCacheID = $requestedProfileID;        

        $templateDirector->setCacheID( $userCacheID, 60 );
        $templateDirector->preProcessTemplate();

        if ( !$templateDirector->isCached() ) {
            $profilesHaveRelationship = false;
    
            $daoFactory = DAOFactory::getInstance();
            $userProfileDAO = $daoFactory->getUserProfileDAO();
            $relationshipDAO = $daoFactory->getRelationshipDAO();
    
            // FIX Security hole - needs to check who is requesting this action
            $userProfileDTO = $userProfileDAO->getProfile( $requestedProfileID );
            $userProfileName = $userProfileDAO->getProfileName( $requestedProfileID );
            
            $userProfileRealName = $userProfileDAO->getProfileRealName( $requestedProfileID );
            
            $imageDAO = $daoFactory->getImageDAO();
            $profileImageDTO = $imageDAO->getProfileImageElement( $requesterProfileID, $requestedProfileID );
            
            // need to know if this is a friend so we can decide whether to offer add or remove
            // TODO this needs to be cleaned up and presented based on the particular relationship types in use
            $daoFunction = 'getProfileRelationships';
            $inputValues = array();
			$inputValues[ 'profileID' ] = $_SESSION['MAIN_PROFILE_ID'];
 	    	 	    	
			$daoFactory = DAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$relationshipDTOArray = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			
            $profilesHaveRelationship = false;
            foreach( $relationshipDTOArray as $relationshipDTO ) {
                if ( $relationshipDTO->get_relationshiptype() === 'Friends' && $relationshipDTO->get_profile_id() === $requestedProfileID ) {
                    // the requester and the requested profiles have a Friends relationship
                    $profilesHaveRelationship = true;
                }
            }
    
            $viewInfoBoardContent = new ViewInformationBoardPeopleUserProfileContentProcessor();
    
            $viewInfoBoardContent->setBuildContainer( false );
            $viewInfoBoardContent->setHasRelationship( $profilesHaveRelationship );
            $viewInfoBoardContent->setProfileImageDTO( $profileImageDTO );
            $viewInfoBoardContent->setUserProfileDTO( $userProfileDTO );
            $viewInfoBoardContent->setUserProfileID( $requestedProfileID );
            $viewInfoBoardContent->setUserProfileName( $userProfileName );
            $viewInfoBoardContent->setUserProfileRealName( $userProfileRealName );
                    
            $templateDirector->setContentProcessors( array( $viewInfoBoardContent ) );
        }        

        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
    }

}

?>
