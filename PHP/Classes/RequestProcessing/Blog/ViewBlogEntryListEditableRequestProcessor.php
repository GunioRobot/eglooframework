<?php
/**
 * ViewBlogEntryListEditableRequestProcessor Class File
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
 * @subpackage Blog
 * @version 1.0
 */

/**
 * ViewBlogEntryListEditableRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class ViewBlogEntryListEditableRequestProcessor extends RequestProcessor {
	/**
	* KEITH BUEL - DEPRECATING DO NOT USE
	*/

    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );
		$template = "../Templates/Frameworks/Common/XHTML/Blog/Lists/BlogEntryListContainer.tpl";

        $loggedInProfileID = $_SESSION['MAIN_PROFILE_ID'];

        $templateDirector = TemplateDirectorFactory::getTemplateDirector( $this->requestInfoBean );
        $templateBuilder = new XHTMLBuilder();

        $templateDirector->setTemplateBuilder( $templateBuilder );
                

        $userCacheID = $_SESSION['USER_ID'] . '|' . $_SESSION['MAIN_PROFILE_ID'];        


//        $templateDirector->setCacheID( $userCacheID );
        $templateDirector->preProcessTemplate();

//        if ( !$templateDirector->isCached() ) {
            $templateVariables = array();
    
            $daoFactory = DAOFactory::getInstance();
            $blogDTOArray = $daoFactory->getBlogDAO()->viewBlogEntryList( $loggedInProfileID, $loggedInProfileID  );
    
     		$templateVariables['blogDTOArray'] = $blogDTOArray;
            $templateDirector->setTemplateVariables( $templateVariables );            
//        }

        $output = $templateDirector->processTemplate();

        header("Content-type: text/html; charset=UTF-8");

        echo $output;

    }
    
}

?>
