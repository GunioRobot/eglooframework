<?php
/**
 * ViewBlogEntryRequestProcessor Class File
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
 * @author <UNKNOWN>
 * @copyright 2011 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Blog
 * @version 1.0
 */

/**
 * ViewBlogEntryRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class ViewBlogEntryRequestProcessor extends RequestProcessor {
   
	//TODO: Need to change this to use the template dispatch
    private $_templateDefault = '../Templates/Frameworks/Common/XHTML/Blog/Views/BlogEntryView.tpl';
    
    public function processRequest() {
        $this->_templateEngine = new XHTMLDefaultTemplateEngine( 'dev', 'us' );

        $blogEntryID = $this->requestInfoBean->getGET('blogID');

        $profileID = $_SESSION['MAIN_PROFILE_ID'];
        
        $daoFactory = AbstractDAOFactory::getInstance();
        $blogDTO = $daoFactory->getBlogDAO()->viewBlogEntry( $profileID, $blogEntryID );

        $this->_templateEngine->assign( 'blogEntryID', $blogEntryID );
        $this->_templateEngine->assign( 'blogEntryTitle', $blogDTO->getTitle() );
        $this->_templateEngine->assign( 'blogEntryContent', nl2br( $blogDTO->getContent() ) );
        
        header("Content-type: text/html; charset=UTF-8");
        $output = $this->_templateEngine->fetch( $this->_templateDefault );
        
        echo $output;
        
    }
    
}

?>