<?php
/**
 * DeclineRelationshipRequestProcessor Class File
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
 * @author Keith Buel
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * DeclineRelationshipRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class DeclineRelationshipRequestProcessor extends RequestProcessor {
    
    public function processRequest() {


		$requesterProfileID = $this->requestInfoBean->getGET('requesterProfileID'); 
		$relationshiptType = $this->requestInfoBean->getGET('relationshipType'); 
		
		$accepterProfileID = $_SESSION['MAIN_PROFILE_ID'];
		
	    $daoFunction = 'actOnRelationship';
		$inputValues = array();
		$inputValues[ 'requesterProfileID' ] = $requesterProfileID;
		$inputValues[ 'accepterProfileID' ] = $accepterProfileID;
		$inputValues[ 'relationshipType' ] = $relationshiptType;
		$inputValues[ 'acceptRelationship' ] = 'false';
 	    	 	    	
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );	

		$success = $gqDTO->get_output_successful();
		if( $success ){
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "SUCCESSFUL call to $daoFunction");
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "FAILURE call to $daoFunction");
		}

		//set the header after all session information has been written.
        header("Content-type: text/html; charset=UTF-8");
        
        //return the json message to the axis call
		echo '';
    }
 }
?>