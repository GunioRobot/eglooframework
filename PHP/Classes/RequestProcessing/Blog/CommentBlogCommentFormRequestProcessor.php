<?php
/**
 * CommentBlogCommentFormRequestProcessor Class File
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
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Blog
 * @version 1.0
 */

/**
 * CommentBlogCommentFormRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class CommentBlogCommentFormRequestProcessor extends RequestProcessor {
    
    public function processRequest() {
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        
		
		$mainProfileID = $_SESSION['MAIN_PROFILE_ID'];
		$viewingProfileID = $_SESSION['MAIN_PROFILE_ID'];
		$viewingUserProfileName = $_SESSION['USER_USERNAME'];
		
		$loggedInUser = true;
		
		if( $this->requestInfoBean->issetGET('profileID') ) {
			$viewingProfileID = $this->requestInfoBean->getGET( 'profileID' );
		} else {
			$userCacheID = $_SESSION['USER_ID'] . '|' . $_SESSION['MAIN_PROFILE_ID'];
		}

		if( $viewingProfileID !==  $mainProfileID ) {
			$loggedInUser = false;
			
			$daoFunction = 'getProfileName';
			$inputValues = array();
 	    	$inputValues[ 'profileID' ] = $viewingProfileID;
 	    	 	    	
 	    	$daoFactory = DAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
            $viewingUserProfileName = $gqDTO->get_output_profile_name();
		}
		
        //$templateDirector->setCacheID( $userCacheID, 3600 );
        $templateDirector->preProcessTemplate();


        $templateVariables['eas_MainProfileID'] = $mainProfileID;
        $templateVariables['eas_ViewingProfileID'] = $viewingProfileID;
		$templateVariables['username'] = $viewingUserProfileName;

        

		//get the comment we are commenting on
        $daoFunction = 'getBlogComment';
		$inputValues = array();
 	    $inputValues[ 'blogComment_id' ] = $this->requestInfoBean->getGET( 'blogCommentID' );
        
        $daoFactory = DAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$blogCommentDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
        
        
		$templateVariables['replyToCommentID'] = $this->requestInfoBean->getGET( 'blogCommentID' );
        $templateVariables['replyToCommentContent'] = $blogCommentDTO->get_output_blogcommentcontent();
        $templateVariables['replyToCommentProfileName'] = $blogCommentDTO->get_output_profilename();
        $templateVariables['replyToblogID'] = $blogCommentDTO->get_output_blog_id();
        
        $templateDirector->setTemplateVariables( $templateVariables );            

        
        
        $globalMenuBarContent = new GlobalMenuBarContentProcessor();
        $templateDirector->setContentProcessors( array( $globalMenuBarContent ) );

        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
    }
      
}

?>