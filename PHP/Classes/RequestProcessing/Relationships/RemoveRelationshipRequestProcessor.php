<?php
/**
 * RemoveRelationshipRequestProcessor Class File
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
 * @author Keith Buel
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * RemoveRelationshipRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class RemoveRelationshipRequestProcessor extends RequestProcessor {
    
    public function processRequest() {

//		$accepterProfileID = $this->requestInfoBean->getPOST('accepterProfileID');

		$profileID = null;
		$relationshipID = $this->requestInfoBean->getGET('relationshipID'); 

		$daoFactory = DAOFactory::getInstance();
		$relationshipDAO = $daoFactory->getRelationshipDAO();
		
		/**
		 * TEMP get main profile ID
		 * TODO remove later
		 */
		$profileID = $_SESSION['MAIN_PROFILE_ID'];
		
		
		$relationshipDAO->removeRelationship($relationshipID, $profileID );

		//set the header after all session information has been written.
        header("Content-type: text/html; charset=UTF-8");

        //return the json message to the axis call
		echo '';
    }
 }
?>