<?php
/**
 * EditBlogEntryRequestProcessor Class File
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
 * @subpackage Blog
 * @version 1.0
 */

/**
 * EditBlogEntryRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class EditBlogEntryRequestProcessor extends RequestProcessor {
	/**
	 * KEITH BUEL - DEPRECATING DO NOT USE
	 */

    // TODO SANITY CHECKs -- Do not assume RequestProcessor wasn't drunk when it sent this to you
    private $_templateDefault = '../Templates/Frameworks/Common/XHTML/Blog/Forms/BlogEditForm.tpl';
    
    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );

        $blogEntryID = $this->requestInfoBean->getGET('blogID');

        if ( $this->requestInfoBean->issetPOST('editBlogEntrySubmit') ) {
            eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'EditBlogEntryRequestProcessor: User submitted new blog' );
            $blogEntryTitle = htmlentities( $this->requestInfoBean->getPOST('newBlogEntryTitle') );
            $blogEntryContent = htmlentities( $this->requestInfoBean->getPOST('newBlogEntryContent') );
            
            $profileID = $_SESSION['MAIN_PROFILE_ID'];
            
            $daoFactory = DAOFactory::getInstance();
            $blogDTO = $daoFactory->getBlogDAO()->editBlogEntry( $profileID, $blogEntryID, $blogEntryTitle, $blogEntryContent );
            header("Content-type: text/html; charset=UTF-8");
            //header("Content-type: application/xml; charset=UTF-8");
    
            if ( $blogDTO->createBlogEntrySuccessful() ) {
                $output = '{success:true}';
            } else {
                $output = '{success:false}';
            }
        } else {
            $profileID = $_SESSION['MAIN_PROFILE_ID'];
            
            $daoFactory = DAOFactory::getInstance();
            $blogDTO = $daoFactory->getBlogDAO()->viewBlogEntry( $profileID, $blogEntryID );

            $this->_templateEngine->assign( 'blogEntryID', $blogEntryID );
            $this->_templateEngine->assign( 'blogEntryTitle', $blogDTO->getTitle() );
            $this->_templateEngine->assign( 'blogEntryContent', $blogDTO->getContent() );
            
            header("Content-type: text/html; charset=UTF-8");
            $output = $this->_templateEngine->fetch( $this->_templateDefault );
        }
        
        echo $output;
        
    }
    
}

?>