<?php
/**
 * DeleteBlogEntryRequestProcessor Class File
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
 * @author <UNKNOWN>
 * @copyright 2008 eGloo, LLC
 * @license http://www.apache.org/licenses/LICENSE-2.0
 * @package RequestProcessing
 * @subpackage Blog
 * @version 1.0
 */

/**
 * DeleteBlogEntryRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class DeleteBlogEntryRequestProcessor extends RequestProcessor {
    // TODO SANITY CHECKs -- Do not assume RequestProcessor wasn't drunk when it sent this to you
    private $_templateDefault = '../Templates/Frameworks/Common/XHTML/Blog/Forms/BlogEntryForm.tpl';
    
    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );

        if ( $this->requestInfoBean->issetPOST('newBlogEntrySubmit') ) {
            eGlooLogger::writeLog( eGlooLogger::$DEBUG, 'DeleteBlogEntryRequestProcessor: User submitted new blog' );
            $blogEntryTitle = $this->requestInfoBean->getPOST('newBlogEntryTitle');
            $blogEntryContent = $this->requestInfoBean->getPOST('newBlogEntryContent');
            
            $daoFactory = DAOFactory::getInstance();
            $blogDTO = $daoFactory->getBlogDAO()->createBlogEntry( $blogEntryTitle, $blogEntryContent );
            header("Content-type: text/html; charset=UTF-8");
            //header("Content-type: application/xml; charset=UTF-8");
    
            if ( $blogDTO->createBlogEntrySuccessful() ) {
                echo 'Success <br />';
            } else {
                echo 'Failure <br />';
            }
        } else {
            $this->_templateEngine->display( $this->_templateDefault );
        }
        
    }
    
}

?>