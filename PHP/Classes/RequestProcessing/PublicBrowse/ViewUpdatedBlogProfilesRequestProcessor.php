<?php
/**
 * ViewUpdatedBlogProfilesRequestProcessor Class File
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
 * @author <UNKNOWN>
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * ViewUpdatedBlogProfilesRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewUpdatedBlogProfilesRequestProcessor extends RequestProcessor {
    
    public function processRequest() {
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        $templateDirector->preProcessTemplate();
    	

   		$mainProfileID = isset($_SESSION['MAIN_PROFILE_ID']) ? $_SESSION['MAIN_PROFILE_ID'] : 0;
		$viewingProfileID = isset($_SESSION['MAIN_PROFILE_ID']) ? $_SESSION['MAIN_PROFILE_ID'] : 0;
		$viewingUserProfileName = isset($_SESSION['USER_USERNAME']) ? $_SESSION['USER_USERNAME'] : 'Guest';
		$loggedInUser = false;

		// TODO check constraints on these values (ranges and types)
		if ( $this->requestInfoBean->issetGET( 'startIndex' ) ) {
			$startIndex = $this->requestInfoBean->getGET( 'startIndex' ); 
		} else {
			$startIndex = '0';
		}
		
		if ( $this->requestInfoBean->issetGET( 'length' ) ) {
			$length = $this->requestInfoBean->getGET( 'length' ); 
		} else {
			$length = '30';
		}
 
		$daoFunction = 'getRecentUpdateBlogProfiles';
		$inputValues = array();
		$inputValues[ 'start_index' ] = $startIndex;
		$inputValues[ 'profile_count' ] = $length;
		
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTOArray = $genericPLFunctionDAO->selectGenericData( $daoFunction, $inputValues );

		$templateVariables['recentBlogProfilesArray'] = $gqDTOArray;
		$templateVariables['startIndex'] = $startIndex;
		$templateVariables['length'] = $length; 
		
		$profileImageThumbnailHashArray = array();
		
		$daoFunction = 'getProfileImage';
		$inputValues = array();

		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		
		foreach ( $gqDTOArray as $blogProfile ) {
			$inputValues[ 'profileID' ] = $blogProfile->get_blogwriter();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			$profileImageThumbnailHashArray[$blogProfile->get_blogwriter()] = $gqDTO->get_output_imagefilehash();
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "ViewUpdatedBlogProfiles ImageHash: " . $gqDTO->get_output_imagefilehash() );
		}
		
		$templateVariables['profileImageThumbnailHashArray'] = $profileImageThumbnailHashArray;
		
		$blogInfoArray = array();
		
		$daoFunction = 'getBlog';
		$inputValues = array();
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		
		foreach ( $gqDTOArray as $blogProfile ) {
			$inputValues[ 'inputBlogID' ] = $blogProfile->get_blog_id();
			$blogInfoArray[$blogProfile->get_blog_id()] = $genericPLFunctionDAO->selectGenericData( $daoFunction, $inputValues );
		}
		
		$templateVariables['blogInfoArray'] = $blogInfoArray;

/*		$daoFunction = 'getBlogProfilesMostReply';
		$inputValues = array();
		$inputValues[ 'profile_count' ] = '6';
		
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTOArray = $genericPLFunctionDAO->selectGenericData( $daoFunction, $inputValues );
		
		$templateVariables['mostReplyBlogProfilesArray'] = $gqDTOArray; */
		
        $templateDirector->setTemplateVariables( $templateVariables );            
        $globalMenuBarContent = new GlobalMenuBarContentProcessor();
        $templateDirector->setContentProcessors( array( $globalMenuBarContent ) );
        $output = $templateDirector->processTemplate();
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
    }
    
    /*
    		$daoFunction = 'getProfileName';
			$inputValues = array();
 	    	$inputValues[ 'profileID' ] = $viewingProfileID;
 	    	 	    	
 	    	$daoFactory = AbstractDAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
            $viewingUserProfileName = $gqDTO->get_output_profile_name();
    */
}

?>