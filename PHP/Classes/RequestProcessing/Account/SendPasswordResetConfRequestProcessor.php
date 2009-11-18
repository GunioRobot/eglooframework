<?php
/**
 * SendPasswordResetConfRequestProcessor Class File
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
 * @subpackage Account
 * @version 1.0
 */

/**
 * SendPasswordResetConfRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class SendPasswordResetConfRequestProcessor extends RequestProcessor {

    
    public function processRequest() {
            
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        $templateDirector->preProcessTemplate();
        
		$templateVariables[] = array();
		
		
		$emailAddress = $this->requestInfoBean->getPOST('emailAddress');

		eGlooLogger::writeLog( eGlooLogger::DEBUG, "Sending password reset to: $emailAddress" );

		$confirmationCode = $this->generateConfirmationCode();		
		
		//update database
        $daoFunction = 'setPasswordResetConfirmation';
		$inputValues = array();
 	    $inputValues[ 'emailaddress' ] = $emailAddress;
		$inputValues[ 'passwordresetref' ] = $confirmationCode;
 	    
 	    $daoFactory = DAOFactory::getInstance();
		$genericPLFunctionDAO = $daoFactory->getGenericPLFunctionDAO();
		$gqDTO = $genericPLFunctionDAO->selectGenericData( $daoFunction,  $inputValues );
		$userID = $gqDTO->get_output_user_id();
		
		eGlooLogger::writeLog( eGlooLogger::DEBUG, "Sending password reset to: $userID" );
		
		
		$subject = "eGloo Account Password Reset";
				
		$body = "Hello, your eGloo Account has been marked for password reset.  Please click the following link to reset your password\n\n" .
		"www.egloo.com/account/viewForgotAccountPasswordResetForm/&uID=$userID&confirmationID=$confirmationCode";
		$headers = ""; 
		if ( mail($emailAddress, $subject, $body, $headers) ) {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL SUCCESS" );
		} else {
			eGlooLogger::writeLog( eGlooLogger::DEBUG, "MAIL FAIL" );
		}
		
					
        $templateDirector->setTemplateVariables( $templateVariables );            
        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");
        echo $output;
    }

    
	/**
	 * @return string
	 */
	private function generateConfirmationCode(){

		// number of chars in the password
		$totalChar = 20; 
		
		//valid characters to choose from
		$salt = "abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ123456789";
		srand( (double) microtime() * 1000000 );
		
		$confirmationCode = "";
		for ($i=0;$i<$totalChar;$i++) $confirmationCode = $confirmationCode . substr ($salt, rand() % strlen($salt), 1);
		return $confirmationCode;

	}
    
}
 
 
?>
