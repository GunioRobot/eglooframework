<?php
/**
 * GlobalMenuBarContentProcessor Class File
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
 * @author George Cooper
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * GlobalMenuBarContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class GlobalMenuBarContentProcessor extends ContentProcessor {
    
   // private $_templateDefault = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/GlobalMenuBar/GlobalMenuBarContainer.tpl';
    
    public function __construct() {
    }
    
    public function prepareContent() {
        // simulate DB connect
        
		//TODO, don't do this here.... do this in the template, thats what it is there for        
       // $daoFactory = DAOFactory::getInstance();
       // $globalMenuBarDTO = $daoFactory->getGlobalMenuBarDAO()->getMenuButtons();
         
        //$this->_templateEngine->assign( 'globalMenuBarDTO', $globalMenuBarDTO );
        $this->_templateEngine->assign( 'loggedInUserName', $_SESSION['USER_FIRST_NAME'] . ' ' . $_SESSION['USER_LAST_NAME'] );
        $this->_templateEngine->assign( 'mainProfileID', $_SESSION['MAIN_PROFILE_ID'] );
    }
}

?>