<?php
/**
 * ViewInformationBoardRequestProcessor Class File
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
 * @package RequestProcessing
 * @version 1.0
 */

/**
 * ViewInformationBoardRequestProcessor
 * 
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage RequestProcessors
 */
class ViewInformationBoardRequestProcessor extends RequestProcessor {
	/**
	 * KEITH BUEL - DEPRECATING DO NOT USE
	 */
		
	// TODO SANITY CHECKs -- Do not assume RequestProcessor wasn't drunk when it sent this to you
    private static $_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardBase.tpl';
    
    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );
        
        $viewInfoBoardContent = null;

        $globalMenuButtonID = $this->requestInfoBean->getGET('gMBID');

        if ( $globalMenuButtonID === 'gMBPeople' ) {
            $viewInfoBoardContent = new ViewInformationBoardPeopleBaseContentProcessor();
            self::$_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardPeople/InformationBoardPeopleBase.tpl';
        } else if ( $globalMenuButtonID === 'gMBPictures' ) {
            $viewInfoBoardContent = new ViewInformationBoardPicturesBaseContentProcessor();
            self::$_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardPictures/InformationBoardPicturesBase.tpl';
        } else if ( $globalMenuButtonID === 'gMBMusic' ) {
            $viewInfoBoardContent = new ViewInformationBoardMusicBaseContentProcessor();
            self::$_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardMusic/InformationBoardMusicBase.tpl';
        } else if ( $globalMenuButtonID === 'gMBVideo' ) {
            $viewInfoBoardContent = new ViewInformationBoardVideoBaseContentProcessor();
            self::$_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardVideo/InformationBoardVideoBase.tpl';
        } else if ( $globalMenuButtonID === 'gMBIcing' ) {
            $viewInfoBoardContent = new ViewInformationBoardIcingBaseContentProcessor();
            self::$_template = '../Templates/Applications/eGloo/InterfaceBundles/Default/XHTML/InformationBoard/InformationBoardIcing/InformationBoardIcingBase.tpl';
        } else {
            //TODO throw exception
        }

        $viewInfoBoardContent->setTemplateEngine( $this->_templateEngine );
        $viewInfoBoardContent->prepareContent();

        header("Content-type: text/html; charset=UTF-8");

        $this->_templateEngine->display( self::$_template );
    }
    
}

?>