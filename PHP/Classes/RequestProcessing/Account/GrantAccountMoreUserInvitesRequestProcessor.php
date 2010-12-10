<?php
/**
 * GrantAccountMoreUserInvitesRequestProcessor Class File
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
 * @author George Cooper
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Account
 * @version 1.0
 */

/**
 * GrantAccountMoreUserInvitesRequestProcessor
 *
 * This request processor grants a specified account more user invites
 * into the system.
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class GrantAccountMoreUserInvitesRequestProcessor extends RequestProcessor {

    const STRONG_HASH = 'sha256';
    
    public function processRequest() {
		
		//check number of invites left
		$inputValues = array();
 	    $inputValues[ 'input_profilename' ] = 'egAppAdmin';
 	    $daoFunction = 'getProfileID';
		$functionDAO = $this->processDAORequest( $daoFunction, $inputValues );
  		$userProfileID = $functionDAO->get_output_profile_id();
		
  		echo $userProfileID;

		$inputValues = array();
 	    $inputValues[ 'input_user_id' ] = $userProfileID;
 	    $inputValues[ 'input_number_additional' ] = 31;
 	    $daoFunction = 'increaseNumberInvites';
		$functionDAO = $this->processDAORequest( $daoFunction, $inputValues );
  		
    	if( $functionDAO->get_output_successful() ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Granting Additional Invites SUCCESS for user: $userProfileID"  );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Granting Additional Invites FAILURE for user: $userProfileID"  );
		}
  		  		
  		exit;
	}
	
	private function processDAORequest($daoFunction, $inputValues){
		$daoFactory = AbstractDAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		return $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
	}
	
}
?>