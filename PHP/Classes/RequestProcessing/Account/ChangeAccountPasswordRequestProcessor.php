<?php
/**
 * ChangeAccountPasswordRequestProcessor Class File
 *
 * Needs to be commented
 * 
 * Copyright 2011 eGloo, LLC
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
 * @package RequestProcessing
 * @subpackage Account
 * @version 1.0
 */

/**
 * ChangeAccountPasswordRequestProcessor
 *
 * Needs to be commented
 * 
 * @package RequestProcessing
 * @subpackage Account
 */
class ChangeAccountPasswordRequestProcessor extends RequestProcessor {

    public function processRequest() {
        $this->_templateEngine = new XHTMLDefaultTemplateEngine( 'dev', 'us' );

        $searchParam = $this->requestInfoBean->getGET('nameSearchParam');

        $daoFactory = AbstractDAOFactory::getInstance();
        $searchDAO = $daoFactory->getSearchDAO();
        
        // Searches for people
        $peopleSearchResults = $searchDAO->getNameAndProfileIDByName( $searchParam, 30, 0 );
        // TODO Search other categories user has set
        
        $searchResults['People']['ResultCount'] = count( $peopleSearchResults );
        $searchResults['People']['Results'] = array( $peopleSearchResults );

        $this->_templateEngine->assign( 'searchResults', $searchResults );

        $output = $this->_templateEngine->fetch( $this->_templateDefault );

        header("Content-type: text/html; charset=UTF-8");
        
        echo $output;

    }
}

?>
