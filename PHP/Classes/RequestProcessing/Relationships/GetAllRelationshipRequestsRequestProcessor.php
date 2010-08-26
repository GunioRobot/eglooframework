<?php
/**
 * GetAllRelationshipRequestsRequestProcessor Class File
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * GetAllRelationshipRequestsRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class GetAllRelationshipRequestsRequestProcessor extends RequestProcessor {
    
    private $_templateDefault = '../Templates/Frameworks/Common/XHTML/Relationships/Friends/Lists/FriendRequestsList.tpl';
    
    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );

		$profileID = null;

		$daoFactory = DAOFactory::getInstance();
		$relationshipDAO = $daoFactory->getRelationshipDAO();
		
		$profileID = $_SESSION['MAIN_PROFILE_ID'];
        
        // TODO check the request profile ID for permissions AND to see if the relationship requests are
        // for a different profile that this user has access to
    	$daoFunction = 'getProfileRelationshipRequests';
		$inputValues = array();
		$inputValues[ 'profileID' ] = $profileID;
 	    	 	    	
		$daoFactory = DAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$relationshipRequestsDTOArray = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
		        
        if ( $this->requestInfoBean->issetGET( 'retVal' ) ) {
            $this->requestInfoBean->getGET( 'retVal' );
            header("Content-type: script/javascript; charset=UTF-8");
            $output = '{relationshipRequestsCount:' . count($relationshipRequestsDTOArray) . '}';
        } else {            
			
            $this->_templateEngine->assign( 'relationshipRequestsDTOArray', $relationshipRequestsDTOArray );
            
    		//set the header after all session information has been written.
            header("Content-type: text/html; charset=UTF-8");
            $output = $this->_templateEngine->fetch( $this->_templateDefault );
        }

		echo $output;
    }
 }
?>