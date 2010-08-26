<?php
/**
 * FridgeBaseInfoBoardRequestProcessor Class File
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
 * FridgeBaseInfoBoardRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class FridgeBaseInfoBoardRequestProcessor extends RequestProcessor {
    
    public function processRequest() {

        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();
        $templateDirector->setTemplateBuilder( $templateBuilder );
        $templateDirector->preProcessTemplate();
        
        
 		$profileID = $_SESSION['MAIN_PROFILE_ID'];

		//TODO: redo as a generic DB Call
		$daoFactory = DAOFactory::getInstance();
        $fridgeDTOArray = $daoFactory->getFridgeDAO()->getFridgeItemList( $profileID );
        $templateVariables['fridgeCubes'] = $fridgeDTOArray;
        
        
        
        $templateDirector->setTemplateVariables( $templateVariables );            
        $output = $templateDirector->processTemplate();
        
        // TODO move header declarations to a decorator
        header("Content-type: text/html; charset=UTF-8");
        
        echo $output;        
    }
    
}
?>