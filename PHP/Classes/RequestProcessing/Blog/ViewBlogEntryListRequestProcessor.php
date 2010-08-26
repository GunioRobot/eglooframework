<?php
/**
 * ViewBlogEntryListRequestProcessor Class File
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
 * ViewBlogEntryListRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Blog
 */
class ViewBlogEntryListRequestProcessor extends RequestProcessor {
	/**
	 * KEITH BUEL - DEPRECATING DO NOT USE
	 */
	
    public function processRequest() {
        $this->_templateEngine = new TemplateEngine( 'dev', 'us' );
		$template = "../Cubes/B/Blog.gloo/xhtml/ContentViewContent.html";

		$viewingProfileID = null;
        if ( $this->requestInfoBean->issetGET( 'profileID' ) ) {
            $viewingProfileID = $this->requestInfoBean->getGET( 'profileID' );
			//TODO: permission to see this profile id?
        }
        
		$loggedInProfileID = $_SESSION['MAIN_PROFILE_ID'];
        
        $daoFactory = DAOFactory::getInstance();
        $blogDTOArray = $daoFactory->getBlogDAO()->viewBlogEntryList( $viewingProfileID, $loggedInProfileID  );
        header("Content-type: text/html; charset=UTF-8");

        $this->_templateEngine->assign( 'blogDTOArray', $blogDTOArray );
        $this->_templateEngine->display( $template );
    }
    
}

?>