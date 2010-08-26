<?php
/**
 * smarty_function_blogNavList Function Definition File
 *
 * This file contains the definition for the smarty_function_blogNavList function.
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package SmartyFunctions
 * @version 1.0
 */

/**
 * @version  1.0
 * @author   Keith Buel <keith.buel@egloo.com>
 * @param    profile_id (required)
 * @param    fullBlogDateCreated (optional)
 * @return   string
 */
function smarty_function_blogNavList($params, &$smarty)
{
	$templateEngine = new TemplateEngine( 'dev', 'us' );

	if( !isset( $params[ 'profileID' ] ) ){
       		$smarty->trigger_error("assign: missing 'profileID' parameter");
	}


	if( isset( $params[ 'fullBlogDateCreated' ] ) ){
		$templateEngine->assign( 'fullBlogYear', getYear($params[ 'fullBlogDateCreated' ]) );
		$templateEngine->assign( 'fullBlogMonth', getMonth($params[ 'fullBlogDateCreated' ]) );
	}

   
	//get list of blogs
    $daoFunction = 'getBlogList';
	$inputValues = array();
    $inputValues[ 'profileID' ] = $params['profileID'];
 
    $daoFactory = DAOFactory::getInstance();
	$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();


	$blogDTOArray = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );

	$blogListArray = null;
	foreach( $blogDTOArray as $blogDTO ) {
				
	$date = $blogDTO->get_dateblogcreated();
	$currentYear = getYear( $date );
	$currentMonth = getMonth( $date );
			
	if( $blogListArray[ $currentYear ] === null ) {
		$blogListArray[ $currentYear ] = array();
	}
				
	$yearArray = $blogListArray[ $currentYear ];
				
	if( $blogListArray[$currentYear][ $currentMonth ] === null ) {
		$blogListArray[$currentYear][ $currentMonth ] = array();
	}
				
	$monthArray = $blogListArray[ $currentYear][ $currentMonth];
				
	$blogListArray[$currentYear][ $currentMonth ][ $blogDTO->get_blog_id() ] = $blogDTO->get_blogtitle();
				
			
	}
			
	
	
	
	$templateEngine->assign( 'blogListArray', $blogListArray );
	$templateEngine->assign( 'eas_ViewingProfileID', $params[ 'profileID' ] );
	$templateEngine->assign( 'eas_MainProfileID', $_SESSION['MAIN_PROFILE_ID'] );
		
	return $templateEngine->fetch( "Core/eGloo/XHTML/Blog/Lists/BlogNavList.tpl" );
	
}


function getDay( $date ){
	return substr($date, 8, 2);
}
    
function getYear( $date ){
	return substr($date, 0, 4);
}
	    
function getMonth( $date ){
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

?>
