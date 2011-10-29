<?php
/**
 * CommentBlogFormRequestProcessor Class File
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
 * @author <UNKNOWN>
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Blog
 * @version 1.0
 */

/**
 * CommentBlogFormRequestProcessor
 *
 * Needs to be commented
 *
 * @package RequestProcessing
 * @subpackage Blog
 */
class CommentBlogFormRequestProcessor extends RequestProcessor {

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

 	    	$daoFactory = AbstractDAOFactory::getInstance();
			$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
			$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
            $viewingUserProfileName = $gqDTO->get_output_profile_name();
		}

        //$templateDirector->setCacheID( $userCacheID, 3600 );
        $templateDirector->preProcessTemplate();


        $templateVariables['eas_MainProfileID'] = $mainProfileID;
        $templateVariables['eas_ViewingProfileID'] = $viewingProfileID;
        $templateVariables['fullBlogID'] = $this->requestInfoBean->getGET( 'blogID' );


        $daoFunction = 'getBlog';
		$inputValues = array();
 	    $inputValues[ 'inputBlogID' ] = $this->requestInfoBean->getGET( 'blogID' );

 	    $daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$individualBlogDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );

		$templateVariables['fullBlogTitle'] = $individualBlogDTO->get_output_blogtitle();
        $templateVariables['fullBlogContent'] = nl2br( $individualBlogDTO->get_output_blogcontent() );

        $templateVariables['fullBlogDateCreated'] = $individualBlogDTO->get_output_dateblogcreated();
		$templateVariables['username'] = $viewingUserProfileName;

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