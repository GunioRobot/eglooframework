<?php
/**
 * ViewInformationBoardMusicBaseContentProcessor Class File
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
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package ContentProcessing
 * @version 1.0
 */

/**
 * ViewInformationBoardMusicBaseContentProcessor
 * 
 * Needs to be commented
 *
 * @package ContentProcessing
 */
class ViewInformationBoardMusicBaseContentProcessor extends ContentProcessor {
    
    private $_templateDefault = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardMusic/InformationBoardMusicBase.tpl';
    
    public function __construct() {
    }
    
    public function prepareContent() {
        // simulate DB connect

        $daoFactory = AbstractDAOFactory::getInstance();
        $informationBoardMusicBaseDTO = $daoFactory->getInformationBoardMusicDAO()->getInformationBoardMusicBase();

        $this->_templateEngine->assign( 'resultColumns', $informationBoardMusicBaseDTO->getInfoBoardColumns() );
        $this->_templateEngine->assign( 'resultColumnItemList', $informationBoardMusicBaseDTO->getMusicItems() );

//        $this->_templateEngine->assign( 'globalMenuBarContentUseTemplate', true );
//        $this->_templateEngine->assign( 'globalMenuBarContentTemplate', $this->_templateDefault );
        $this->_templateEngine->assign( 'informationBoardMusicBaseDTO', $informationBoardMusicBaseDTO );
        //$this->_templateEngine->assign( 'blogEntryText', $blogEntryDTO->getText() );
    }
}

?>