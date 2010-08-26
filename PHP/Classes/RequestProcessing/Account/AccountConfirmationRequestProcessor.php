<?php
/**
 * AccountConfirmationRequestProcessor Class File
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
 * @subpackage Account
 * @version 1.0
 */

/**
 * AccountConfirmationRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class AccountConfirmationRequestProcessor extends RequestProcessor {

    public function processRequest() {
    	$templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        $templateDirector->preProcessTemplate();
    
        //Start logic

    	$confirmationID = $this->requestInfoBean->getGET('confirmationID');
        $userID = $this->requestInfoBean->getGET('uID');
    	
    	//set confirmation code
        $daoFunction = 'activateUserAccount';
		$inputValues = array();
 	    $inputValues[ 'inviteduser_id' ] = $userID;
 	    $inputValues[ 'confirmation_id' ] = $confirmationID;
 	    	 	    	
 	    $daoFactory = DAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
		

		
		$success = false;
		if( $gqDTO->get_output_successful() ) {
			$success = true;
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Account activation SUCCESS for user: $userID"  );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "Account activation FAILURE for user: $userID"  );
		}
		
		$templateVariables['success'] = $success;
					
		//end logic
		
		
        $templateDirector->setTemplateVariables( $templateVariables );
        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
		
    }

}

?>
