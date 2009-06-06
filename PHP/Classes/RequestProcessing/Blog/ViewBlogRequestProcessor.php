<?php
/**
 * ViewBlogRequestProcessor Class File
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
 * @author <UNKNOWN>
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Blog
 * @version 1.0
 */

/**
 * ViewBlogRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class ViewBlogRequestProcessor extends RequestProcessor {
    
    public function processRequest() {
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        $templateDirector->preProcessTemplate();
        
        //$templateDirector->setCacheID( $userCacheID, 3600 );

		//get the requested blog
        $individualBlogDTO = null;
        
        $fullBlogID = "";
        
        //if they have set the blog id then get it based on the blog id
        if( $this->requestInfoBean->issetGET('blogID') ) {
			
			$fullBlogID = $this->requestInfoBean->getGET( 'blogID' );
			
			//get a specific blog        	
            $daoFunction = 'getBlog';
			$inputValues = array();
 	    	$inputValues[ 'inputBlogID' ] = $fullBlogID;
 	    	 	    	
 	    	$daoFactory = DAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$individualBlogDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
			$viewingProfileID = $individualBlogDTO->get_output_blogwriter();

		//else if they have not specified a blog id, get the latest fo the specified user
		} else if( $this->requestInfoBean->issetGET('profileID') ) {
			
	           	//get the latest blog
	            $daoFunction = 'getLatestBlog';
				$inputValues = array();
	 	    	$inputValues[ 'profileID' ] =  $this->requestInfoBean->getGET('profileID');
	 	    	 	    	
	 	    	$daoFactory = DAOFactory::getInstance();
				$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
				$individualBlogDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
				
				$fullBlogID = $individualBlogDTO->get_output_blogid();
				
				$viewingProfileID = $this->requestInfoBean->getGET('profileID');
					
		} else {
			
			echo "Error - URL tampering detected";
			return;
		}
		
		//set main profile id
		$mainProfileID = $_SESSION['MAIN_PROFILE_ID'];

		//initialize to the logged in user profile name
		$viewingUserProfileName = $_SESSION['USER_USERNAME'];

		//get the viewing profile id from the blog DTO
    	if( $viewingProfileID !==  $mainProfileID ) {
			
			$daoFunction = 'getProfileName';
			$inputValues = array();
 	    	$inputValues[ 'profileID' ] = $viewingProfileID;
 	    	 	    	
 	    	$daoFactory = DAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
            $viewingUserProfileName = $gqDTO->get_output_profile_name();
		}
		
		$templateVariables['eas_MainProfileID'] = $mainProfileID;
        $templateVariables['eas_ViewingProfileID'] = $viewingProfileID;
		$templateVariables['username'] = $viewingUserProfileName;
		
		
		$templateVariables['fullBlogWriterID'] = $viewingProfileID;
		$templateVariables['fullBlogID'] = $fullBlogID;
        $templateVariables['fullBlogTitle'] = $individualBlogDTO->get_output_blogtitle();
        $templateVariables['fullBlogContent'] = nl2br( $individualBlogDTO->get_output_blogcontent() );
        
        $fullBlogDateCreated = $individualBlogDTO->get_output_dateblogcreated();

        $templateVariables['fullBlogDateEdited'] = $individualBlogDTO->get_output_dateedited();
        $templateVariables['fullBlogYear'] = $this->getYear( $fullBlogDateCreated );
		$templateVariables['fullBlogMonth'] = $this->getMonth( $fullBlogDateCreated );
		$templateVariables['fullBlogDay'] = $this->getDay( $fullBlogDateCreated );
        $templateVariables['fullBlogDateCreated'] = $fullBlogDateCreated;
			
		//Blog Comments
		$daoFunction = 'getRootBlogCommentList';
		$inputValues = array();
 		$inputValues[ 'blog_id' ] = $fullBlogID;
	 	    	
		$daoFactory = DAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
	
		$blogcommentDTOArray = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
		$templateVariables['blogCommentListArray']=$blogcommentDTOArray;
					
        $templateDirector->setTemplateVariables( $templateVariables );            
        $globalMenuBarContent = new GlobalMenuBarContentProcessor();
        $templateDirector->setContentProcessors( array( $globalMenuBarContent ) );
        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
    }
    
    private function getDay( $date ){
    	return substr($date, 8, 2);
    }
    
    private function getYear( $date ){
    	return substr($date, 0, 4);
    }
    
    private function getMonth( $date ){
    	$monthNum = substr($date, 5, 2);
    	$monthString = "";
    	if( $monthNum === '01' ) $monthString = "January";
    	if( $monthNum === '02' ) $monthString = "February";
    	if( $monthNum === '03' ) $monthString = "March";
    	if( $monthNum === '04' ) $monthString = "April";
    	if( $monthNum === '05' ) $monthString = "May";
    	if( $monthNum === '06' ) $monthString = "June";
    	if( $monthNum === '07' ) $monthString = "July";
    	if( $monthNum === '08' ) $monthString = "August";
    	if( $monthNum === '09' ) $monthString = "September";
    	if( $monthNum === '10' ) $monthString = "October";
    	if( $monthNum === '11' ) $monthString = "November";
    	if( $monthNum === '12' ) $monthString = "December";
    	return $monthString;
    }
}

?>