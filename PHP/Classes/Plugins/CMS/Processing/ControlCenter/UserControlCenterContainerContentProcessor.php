<?php
/**
 * UserControlCenterContainerContentProcessor Class File
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
 * @author George Cooper
 * @copyright 2010 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * UserControlCenterContainerContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class UserControlCenterContainerContentProcessor extends ContentProcessor {

	/**
	 * ***************DEPRECATED**************DO NOT USE************
	 * 
	 */

    private $_templateDefault = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/ControlCenter/ControlCenterContainer.tpl';
    
    public function __construct() {
    }
    
    public function prepareContent() {
        // simulate DB connect

        $this->_templateEngine->assign( 'userControlCenterContainerContentUseTemplate', true );
        $this->_templateEngine->assign( 'userControlCenterContainerContentTemplate', $this->_templateDefault );
    }
}

?>