<?php
/**
 * ViewForgotAccountPasswordResetFormRequestProcessor Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Account
 * @version 1.0
 */

/**
 * ViewForgotAccountPasswordResetFormRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class ViewForgotAccountPasswordResetFormRequestProcessor extends RequestProcessor {

    
    public function processRequest() {
        
        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        $templateDirector->preProcessTemplate();
    
        //Start logic

    	$confirmationID = $this->requestInfoBean->getGET('confirmationID');
        $userID = $this->requestInfoBean->getGET('uID');
    	
   		/**
         *  TODO: do databse check to make sure this combination is valid
         *  if it is, then we should get the user name and display it for the user
         */
   		   
		$templateVariables['confirmationID'] = $confirmationID;
		$templateVariables['userID'] = $userID;

		//end logic

		$templateDirector->setTemplateVariables( $templateVariables );
        $output = $templateDirector->processTemplate();
        
        header("Content-type: text/html; charset=UTF-8");

        echo $output;
        
    }
    
}
 
 
?>
